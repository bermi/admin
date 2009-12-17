<?php

class AccountController extends ApplicationController
{
    var $models = array('User','Sentinel');

    function __construct()
    {
        $this->settings = Ak::getSettings('admin');
    }

    function index()
    {
        $this->redirectToAction('sign_in');
    }

    function sign_in()
    {
    }

    function sign_up()
    {
        if ($this->Request->isPost() && !empty($this->params['user'])){
            if($this->User->signUp($this->params['user'])){
                $this->flash_options = array('seconds_to_close' => 10);
                $this->flash['success'] = $this->t('Your account has been successfully created');
                    $this->redirectToAction('sign_in');
            }
        }
    }

    function is_login_available()
    {
        if(!empty($this->params['login'])){
            $this->User->set('login', $this->params['login']);
            $this->User->validatesUniquenessOf('login');
            if($this->User->getErrorsOn('login')){
                $this->renderText('0');
                return ;
            }
        }
        $this->renderText('1');
    }

    function logout()
    {
        $this->flash['message'] = $this->t("You have successfully logged out.");
        $this->_perform_logout();
    }

    function _perform_logout($redirect = true)
    {
        $this->Sentinel->init($this);
        $this->Sentinel->unsetCurrentUser();
        if($redirect){
            $settings = Ak::getSettings('admin');
            $this->redirectTo(empty($settings['sign_in_url'])? array('action'=>'sign_in') : $settings['sign_in_url']);
        }
    }

    function password_reminder()
    {
        if($this->Request->isPost()){
            $this->Sentinel->init($this);
            if($User = $this->User->findFirstBy('email', @$this->params['email'])){
                if($this->Sentinel->sendPasswordReminder($User)){
                    $this->renderAction('password_reminder_sent');
                }else{
                    $this->flash_now['error'] = $this->t('There was an error while trying to send you the instructions to reset your password.');
                }
            }else{
                $this->flash_now['error'] = $this->t('Account not found');
            }
        }

    }

    function reset_password()
    {
        if($this->User = $this->Sentinel->authenticateWithToken(@$this->params['token'])){
            $this->token = $this->User->getToken(array('expires' => true, 'single_use' => true));
            if($this->Request->isPost()){
                $this->User->setAttributes($this->params['user']);
                if($this->User->save()){
                    $this->flash['message'] = $this->t("You can now login using your user name and password.");
                    $this->_perform_logout();
                }
            }
        }else{
            $this->flash['error'] = $this->t('Invalid or expired authentication URL.');
            $this->redirectToAction('password_reminder');
        }
        $this->_perform_logout(false);
    }
}

?>