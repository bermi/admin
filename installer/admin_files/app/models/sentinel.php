<?php

/**
 * The Sentinel is the agent who controls the credentials in the admin plugin.
 */
class Sentinel extends AkBaseModel
{
    public $Controller;
    public $CurrentUser;
    public $Session;
    public $locale_namespace = 'admin_plugin';
    

    public function init(&$Controller) {
        $this->Controller = $Controller;
        $this->Session =& $this->Controller->Request->getSession();
    }

    public function authenticate() {
        $restore = false;
        $this->saveOriginalRequest();
        if(!$User = $this->getUserFromSession()){
            if($User = $this->getAuthenticatedUser()){
                $restore = true;
            }
        }
        if($User){
            $this->setCurrentUser($User);
        }
        if($restore){
            $this->restoreOriginalRequest();
        }
        return $User;
    }



    public function getAuthenticatedUser() {
        return $this->{$this->getAuthenticationMethod()}();
    }

    public function getAuthenticationMethod() {
        if(!empty($this->Controller->params['ak_token'])) {
            return 'authenticateUsingToken';
        } elseif(!empty($this->Controller->params['ak_login']) || $this->shouldDefaultToPostAuthentication()){
            return 'authenticateUsingPostedVars';
        }
        return 'authenticateUsingHttpBasic';
    }

    public function authenticateUsingPostedVars() {
        $UserInstance = new User();
        $login = @$this->Controller->params['ak_login'];
        $result =  $UserInstance->authenticate(@$login['login'], @$login['password']);
        if(!$result){
            if(!empty($this->Controller->params['ak_login'])){
                $this->Controller->flash['error'] = $this->t('Invalid user name or password, please try again');
            }
            $this->redirectToSignInScreen();
        }
        return $result;
    }


    public function authenticateUsingToken(){
        return $this->authenticateWithToken(@$this->Controller->params['ak_token']);
    }

    public function authenticateWithToken($token, $update_last_login = true) {
        $options = User::decodeToken($token);
        if(!empty($options) && !empty($options['hash']) && !empty($options['id'])){
            $User = new User();
            $User = $User->find($options['id']);
            if(!empty($options['expires']) && $options['expires'] < Ak::getTimestamp()){
                return false;
            }
            if($options['hash'] == $User->getTokenHash($options)){
                if($update_last_login){
                    $User->updateAttribute('last_login_at', Ak::getDate());
                }
                $this->setCurrentUser($User);
                return $User;
            }
        }
        return false;
    }


    public function saveOriginalRequest($force = false) {
        if(empty($this->Session['__OriginalRequest']) || $force){
            $this->Session['__OriginalRequest'] =  AK_CURRENT_URL;
        }
    }

    public function restoreOriginalRequest() {
        if(!empty($this->Session['__OriginalRequest'])){
            $url = $this->Session['__OriginalRequest'];
            unset($this->Session['__OriginalRequest']);
            $this->Controller->redirectTo($url);
            exit;
        }
    }

    public function redirectToSignInScreen() {
        $settings = Ak::getSettings('admin');
        $this->Controller->redirectTo($settings['sign_in_url']);
    }

    public function authenticateUsingHttpBasic() {
        $settings = Ak::getSettings('admin');
        return $this->Controller->_authenticateOrRequestWithHttpBasic($this->t($settings['http_auth_realm']), new User());
    }

    public function hasUserOnSession() {
        return !empty($this->Session['__current_user_id']);
    }

    public function getUserFromSession(){
        if (!isset($this->Controller->params['ak_login']) && $this->hasUserOnSession()) {
            $model_class = isset($this->Session['__CurrentUserType']) ? $this->Session['__CurrentUserType'] : 'User';
            $UserInstance = new $model_class();
            $finder_options = empty($this->Controller->user_finder_options) ? array('include'=>'roles') : $this->Controller->user_finder_options;
            $User = $UserInstance->find($this->Session['__current_user_id'], $finder_options);
            if($User){
                $User->type = $model_class;
            }
            return $User;
        }
        return false;
    }

    public function setCurrentUserOnSession($User, $force = false){
        if((!$this->hasUserOnSession() && empty($this->_authenticated_with_token)) || $force){
            if ($User instanceof AkActiveRecord) {
                $this->Session['__current_user_id'] = $User->getId();
                $this->Session['__CurrentUserType'] = $User->getType();
                return true;
            }
        }
        return $this->hasUserOnSession();
    }

    public function removeCurrentUserFromSession() {
        if($this->hasUserOnSession()){
            $this->Session['__CurrentUserType'] = null;
            $this->Session['__current_user_id'] = null;
        }
    }

    public function setCurrentUserOnController($User) {
        $this->Controller->CurrentUser = $User;
    }

    public function removeCurrentUserFromController() {
        $this->Controller->CurrentUser = null;
    }

    public function getCurrentUser() {
        return $this->CurrentUser;
    }

    public function setCurrentUser(&$User) {
        $this->CurrentUser = $User;
        $this->setCurrentUserOnController($User);
        $this->setCurrentUserOnSession($User);
        User::setCurrentUser($User);
    }

    public function unsetCurrentUser() {
        $this->CurrentUser = null;
        $this->removeCurrentUserFromController();
        $this->removeCurrentUserFromSession();
        User::unsetCurrentUser();
    }

    public function shouldDefaultToPostAuthentication() {
        $settings = Ak::getSettings('admin');
        if(!empty($settings['default_authentication_method']) &&
            $settings['default_authentication_method'] == 'post'){
            return $this->isWebBrowser();
        }
    }

    public function isWebBrowser() {
        return preg_match('/Mozilla|MSIE|Gecko|Opera/i',@$this->Controller->Request->env['HTTP_USER_AGENT']);
    }

    public function getCredentialsRenewalUrl($User) {
        if($User){
            return $this->Controller->urlFor(array(
            'controller' => 'account',
            'action' => 'reset_password',
            'token' => $User->getToken(array('single_use' => true, 'expires' => 86400)),
            ));
        }

        return false;
    }

    public function sendPasswordReminder($User) {
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

