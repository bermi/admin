<?php

class Admin_PermissionsController extends AdminController
{
    var $controller_menu_options = array(
    'Accounts'   => array('id' => 'accounts', 'url'=>array('controller'=>'users', 'action'=>'listing')),
    'Roles'   => array('id' => 'roles', 'url'=>array('controller'=>'roles')),
    'Permissions'   => array('id' => 'permissions', 'url'=>array('controller'=>'permissions', 'action'=>'manage')),
    );

    var $admin_selected_tab = 'Manage Users';

    function index()
    {
        $this->redirectToAction('manage');
    }

    function manage()
    {
        if($this->Request->isPost()){
            $this->_updatePermissions();
        }
        $this->Roles =& $this->Role->findAllBy('name:<>', 'Application owner');
        $this->Extensions =& $this->Extension->find('all', array('include'=>array('permissions' => array('order'=> 'name ASC'))));
    }

    function destroy()
    {
        if(!empty($this->params['id'])){
            if($this->Permission =& $this->Permission->find($this->params['id'], array('include' => 'extension'))){
                if($this->Request->isPost()){
                    if($this->Permission->destroy()){
                        $this->flash_options = array('seconds_to_close' => 10);
                        $this->flash['success'] = $this->t('Permission was successfully deleted.');
                        $this->redirectToAction('manage');
                    }else{
                        $this->flash['error'] = $this->t('Permission could not be deleted.');
                        $this->redirectToAction('manage');
                    }
                }
            }else {
                $this->flash['error'] = $this->t('Permission not found.');
                $this->redirectToAction('manage');
            }
        }
    }

    function _updatePermissions()
    {
        foreach ($this->params['permissions'] as $permission_id=>$roles) {
            $role_ids = array_keys(array_diff($roles, array('')));
            $Permission =& $this->Permission->find($permission_id, array('include'=>'roles'));
            $Permission->role->deleteAll();
            $Permission->role->setByIds($role_ids);
        }
    }

    function _loadPermissionsAndExtensions()
    {
        $this->Permissions =& $this->role->getPermissions();
        $this->Extensions =& $this->Extension->find('all', array('include'=>'permissions', 'sort'=>'__owner.name ASC, _permissions.name ASC'));
    }
}

?>