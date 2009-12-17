<?php

class AccountMailer extends AkActionMailer
{
    public $_password_reset_url;
    public $delivery_method = 'smtp';

    public function __construct() {
        $this->_settings = Ak::getSettings('admin');
        return parent::__construct();
    }

    public function registration_details($recipient) {
        $this->recipients    =  $recipient;
        $this->subject    = "[{$this->_settings['application_name']}] ".$this->t('Registration details');
        $this->from       = $this->_settings['do_not_reply_email'];
        $this->body          =  array(
        'login' => $this->_login,
        'sign_in_url' => $this->getSignInUrl(),
        'password' => $this->_password,
        'application_name' => $this->_settings['application_name'],
        );
    }


    public function password_reminder($recipient) {
        $this->recipients =  array($recipient);
        $this->subject    = "[{$this->_settings['application_name']}] ".$this->t('Password reminder');
        $this->from       = $this->_settings['do_not_reply_email'];
        $this->body       =  array(
        'application_name' => $this->_settings['application_name'],
        'password_reset_url' => $this->getPasswordResetUrl()
        );
    }


    public function setPasswordResetUrl($password_reset_url) {
        $this->_password_reset_url = $password_reset_url;
    }

    public function getPasswordResetUrl() {
        return $this->_password_reset_url;
    }

    public function getSignInUrl() {
        $settings = Ak::getSettings('admin');
        $settings['base_url'] = isset($settings['base_url']) ? $settings['base_url'] : true;
        return $this->urlFor($settings['sign_in_url']);
    }

}

