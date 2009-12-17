<?php

/**
 * The Sentinel is the agent who controls the credentials in the admin plugin.
 */
class Sentinel
{
    public $Controller;
    public $CurrentUser;
    public $Session;

    public function init(&$Controller)
    {
        $this->Controller =& $Controller;
        $this->Session =& $this->Controller->Request->getSession();
    }

    public function authenticate()
    {
        $this->saveOriginalRequest();
        if(!$User = $this->getUserFromSession()){
            if($User = $this->getAuthenticatedUser()){
                $this->restoreOriginalRequest();
            }
        }

        if($User){
            $this->setCurrentUser($User);
        }
        return $User;
    }

    public function getAuthenticatedUser()
    {
        return $this->{$this->getAuthenticationMethod()}();
    }

    public function getAuthenticationMethod()
    {
        if(!empty($this->Controller->params['ak_login']) || $this->shouldDefaultToPostAuthentication()){
            return 'authenticateUsingPostedVars';
        }

        return 'authenticateUsingHttpBasic';
    }

    public function authenticateUsingPostedVars()
    {
        $UserInstance = new User();
        $login = @$this->Controller->params['ak_login'];
        $result =  $UserInstance->authenticate(@$login['login'], @$login['password']);
        if(!$result){
            if(!empty($this->Controller->params['ak_login'])){
                $this->Controller->flash['error'] = Ak::t('Invalid user name or password, please try again', null, 'account');
            }
            $this->redirectToSignInScreen();
        }
        return $result;
    }

    public function authenticateWithToken($token)
    {
        $options = User::_decodeToken($token);
        
        if(!empty($options) && !empty($options['hash']) && !empty($options['id'])){
            $User = new User();
            $User = $User->find($options['id']);
            if(!empty($options['expires']) && $options['expires'] < Ak::getTimestamp()){
                return false;
            }
            if($options['hash'] == $User->_getTokenHash($options)){

                $User->updateAttribute('last_login_at', Ak::getDate());

                return $User;
            }
        }
        return false;
    }


    public function saveOriginalRequest($force = false)
    {
        if(empty($this->Session['__OriginalRequest']) || $force){
            $this->Session['__OriginalRequest'] = serialize($this->Controller->Request);
        }
    }

    public function restoreOriginalRequest()
    {
        if(!empty($this->Session['__OriginalRequest'])){
            $this->Controller->Request = unserialize($this->Session['__OriginalRequest']);
            $this->Controller->params = $this->Controller->Request->getParams();
            unset($this->Session['__OriginalRequest']);
        }
    }

    public function redirectToSignInScreen()
    {
        $settings = Ak::getSettings('admin');
        $this->Controller->redirectTo($settings['sign_in_url']);
    }

    public function authenticateUsingHttpBasic()
    {
        $settings = Ak::getSettings('admin');
        return $this->Controller->_authenticateOrRequestWithHttpBasic(Ak::t($settings['http_auth_realm'], null, 'account'), new User());
    }

    public function hasUserOnSession()
    {
        return !empty($this->Session['__current_user_id']);
    }

    public function getUserFromSession()
    {
        if ($this->hasUserOnSession()) {
            $model = isset($this->Session['__CurrentUserType'])?$this->Session['__CurrentUserType']:'User';
            Ak::import($model);
            $UserInstance = new $model();
            return $UserInstance->find($this->Session['__current_user_id'], array('include'=>'roles'));
        }
        return false;
    }

    public function setCurrentUserOnSession($User, $force = false)
    {
        if(!$this->hasUserOnSession() || $force){
            $this->Session['__current_user_id'] = $User->getId();
            $this->Session['__CurrentUserType'] = get_class($User);
        }

    }

    public function removeCurrentUserFromSession()
    {
        if($this->hasUserOnSession()){
            $this->Session['__CurrentUserType'] = null;
            $this->Session['__current_user_id'] = null;
        }
    }

    public function setCurrentUserOnController($User)
    {
        $this->Controller->CurrentUser =& $User;
    }

    public function removeCurrentUserFromController()
    {
        $this->Controller->CurrentUser = null;
    }

    public function getCurrentUser()
    {
        return $this->CurrentUser;
    }

    public function setCurrentUser(&$User)
    {
        $this->CurrentUser =& $User;
        $this->setCurrentUserOnController($User);
        $this->setCurrentUserOnSession($User);
        User::setCurrentUser($User);
    }

    public function unsetCurrentUser()
    {
        $this->CurrentUser = null;
        $this->removeCurrentUserFromController();
        $this->removeCurrentUserFromSession();
        User::unsetCurrentUser();
    }

    public function shouldDefaultToPostAuthentication()
    {
        $settings = Ak::getSettings('admin');
        if(!empty($settings['default_authentication_method']) &&
        $settings['default_authentication_method'] == 'post'){
            return $this->isWebBrowser();
        }
    }

    public function isWebBrowser()
    {
        return preg_match('/Mozilla|MSIE|Gecko|Opera/i',@$this->Controller->Request->env['HTTP_USER_AGENT']);
    }


    public function getCredentialsRenewalUrl($User)
    {
        if($User){
            return $this->Controller->urlFor(array(
            'controller' => 'account',
            'action' => 'reset_password',
            'token' => $User->getToken(array('single_use' => true, 'expires' => 86400)),
            ));
        }

        return false;
    }

    public function sendPasswordReminder($User)
    {
        if($password_reset_url = $this->getCredentialsRenewalUrl($User)){
            Ak::import_mailer('account_mailer');
            $Mailer = new AccountMailer();
            $Mailer->setPasswordResetUrl($password_reset_url);
            $Mailer->deliver('password_reminder', $User->get('email'));
            return true;
        }
        return false;
    }

}

?>