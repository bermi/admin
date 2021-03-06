<?php

class AdminController extends ApplicationController
{
    public $app_models = array('user','role','permission','extension');
    public $protect_all_actions = true;
    //public $protected_actions = 'index,show,edit,delete'; // You can protect individual actions
    public $locale_namespace = 'admin_plugin';

    public $admin_menu_options = array();
    public $controller_menu_options = array();

    public function __construct() {

        $this->beforeFilter('_loadAdminSettings');
        $this->beforeFilter('_authenticate');
        !empty($this->protected_actions) ? $this->beforeFilter('_protectAction') : null;
        !empty($this->protect_all_actions) ? $this->beforeFilter(array('_protectAllActions' => array('except'=>array('action_privileges_error', 'login')))) : null;
    }


    /**
    * Filters. Public methods but underscored to they cant be triggered directly via URL calls
    */
    public function _loadAdminSettings() {
        $this->admin_settings = Ak::getSettings('admin');
        return true;
    }

    public function _authenticate() {
        $Sentinel = new Sentinel();
        $Sentinel->init($this);
        return $Sentinel->authenticate();
    }

    public function access_denied() {
        header('HTTP/1.0 401 Unauthorized');
        echo "HTTP Basic: Access denied.\n";
        exit;
    }

    public function _protectAction() {
        $protected_actions = Ak::toArray($this->protected_actions);
        $action_name = $this->getActionName();
        if(in_array($action_name, $protected_actions) && !$this->CurrentUser->can($action_name.' action', 'Admin::'.$this->getControllerName())){
            $this->redirectTo(array('action'=>'protected_action'));
        }
    }

    public function _protectAllActions() {
        if(!$this->CurrentUser->can($this->getActionName().' action', 'Admin::'.$this->getControllerName())){
            $this->redirectTo(array('action'=>'action_privileges_error', 'controller'=>'dashboard'));
        }
    }

    protected function _loadCurrentUserRoles() {
        $this->Roles = $this->CurrentUser->getRoles();
        if (empty($this->Roles)){
            $this->flash['notice'] = $this->t('It seems like you don\'t have Roles on your site. Please fill in the form below in order to create your first role.');
            $this->redirectTo(array('controller' => 'role', 'action' => 'add'));
        }
    }
}

