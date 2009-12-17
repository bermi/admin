<?php

class AdminController extends ApplicationController
{
    var $app_models = array('user','role','permission','extension');
    var $protect_all_actions = true;
    //var $protected_actions = 'index,show,edit,delete'; // You can protect individual actions

    var $admin_menu_options = array();
    var $controller_menu_options = array();

    function __construct()
    {
        $this->beforeFilter('load_settings');
        $this->beforeFilter('authenticate');
        !empty($this->protected_actions) ? $this->beforeFilter('_protectAction') : null;
        !empty($this->protect_all_actions) ? $this->beforeFilter(array('_protectAllActions' => array('except'=>array('action_privileges_error', 'login')))) : null;
    }

    function load_settings()
    {
        $this->admin_settings = Ak::getSettings('admin');
        return true;
    }

    function authenticate()
    {
        Ak::import('sentinel');
        $Sentinel =& new Sentinel();
        $Sentinel->init($this);
        return $Sentinel->authenticate();
    }

    function access_denied()
    {
        header('HTTP/1.0 401 Unauthorized');
        echo "HTTP Basic: Access denied.\n";
        exit;
    }

    function _protectAction()
    {
        $protected_actions = Ak::toArray($this->protected_actions);
        $action_name = $this->getActionName();
        if(in_array($action_name, $protected_actions) && !$this->CurrentUser->can($action_name.' action', 'Admin::'.$this->getControllerName())){
            $this->redirectTo(array('action'=>'protected_action'));
        }
    }

    function _protectAllActions()
    {
        if(!$this->CurrentUser->can($this->getActionName().' action', 'Admin::'.$this->getControllerName())){
            $this->redirectTo(array('action'=>'action_privileges_error', 'controller'=>'dashboard'));
        }
    }

    function _loadCurrentUserRoles()
    {
        $this->Roles =& $this->CurrentUser->getRoles();
        if (empty($this->Roles)){
            $this->flash['notice'] = $this->t('It seems like you don\'t have Roles on your site. Please fill in the form below in order to create your first role.');
            $this->redirectTo(array('controller' => 'role', 'action' => 'add'));
        }
    }
}

?>