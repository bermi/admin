<?php

class Admin_RolesController extends AdminController
{
    public $controller_menu_options = array(
    'Accounts'   => array('id' => 'accounts', 'url'=>array('controller'=>'users', 'action'=>'listing')),
    'Roles'   => array('id' => 'roles', 'url'=>array('controller'=>'roles')),
    'Permissions'   => array('id' => 'permissions', 'url'=>array('controller'=>'permissions', 'action'=>'manage')),
    );

    public $admin_selected_tab = 'Manage Users';
    public $controller_selected_tab = 'Roles';

    public function index() {
        $this->redirectToAction('listing');
    }

    public function listing() {
        $this->_loadCurrentUserRoles();
    }

    public function add() {
        $this->_loadCurrentUserRoles();

        if(empty($this->Roles) || !isset($this->CurrentUser->roles[0]) || !$Root = $this->CurrentUser->roles[0]){
            $this->flash['notice'] = $this->t('Can not create a Role. Parent Role not found.');
            $this->redirectToAction('listing');
        }

        if($this->Request->isPost() && !empty($this->params['role'])){
            $this->role = $Root->addChildrenRole($this->params['role']['name']);
            $this->_addOrEditRole('add');
        }
    }

    public function edit() {
        if (empty($this->params['id'])){
            $this->redirectToAction('listing');
        }

        if($this->Request->isPost() && !empty($this->params['role'])){
            $this->_addOrEditRole('edit');
        }
    }
    

    public function destroy() {
        if(!empty($this->params['id'])){
            if($this->role = $this->role->find($this->params['id'])){
                if($this->Request->isPost()){
                    $this->role->destroy();
                    $this->flash_options = array('seconds_to_close' => 10);
                    $this->flash['notice'] = $this->t('Role was successfully deleted.');
                    $this->redirectToAction('listing');
                }
            }else {
                $this->flash['error'] = $this->t('Role not found.');
                $this->redirectToAction('listing');
            }
        }
    }

    protected function _addOrEditRole($action) {
        $this->role->setAttributes(Ak::pick('name,description,is_enabled', $this->params['role']));
        if ($this->role->save()){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('Role was successfully '.($action=='add'?'created':'updated'.'.'));
            $this->redirectToAction('listing');
        }
    }
}


