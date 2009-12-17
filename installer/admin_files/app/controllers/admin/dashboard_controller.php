<?php

class Admin_DashboardController extends AdminController
{
    public function index() {
        $this->renderAction('blank_slate');
    }

    public function action_privileges_error() {
        $this->Response->addHeader('Status', 405);
        $this->flash_now['error'] = $this->t('You don\'t have enough privileges to perform selected action.');
    }
    
    public function blank_slate() {
    }
}

