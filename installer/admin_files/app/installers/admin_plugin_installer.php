<?php

class AdminPluginInstaller extends AkInstaller
{
    function up_1()
    {
        $this->createTable('users', '
          id,
          login string(40) not null idx,
          email string(50) not null idx,
          password string(40) not null,
          password_salt string(16) not null,
          last_login_at,
          is_enabled bool default 1
        '); 

        $this->createTable('roles', '
          id,
          name,
          description,
          is_enabled bool default 1,
          parent_id,
          lft integer(8) index,
          rgt integer(8) index,
        ');

        $this->createTable('roles_users', 'id, role_id, user_id', array('timestamp' => false));
        $this->createTable('permissions_roles', 'id, permission_id, role_id', array('timestamp' => false));
        $this->createTable('extensions', 'id, name, is_core, is_enabled');
        $this->createTable('permissions', 'id, name, extension_id');

        if(AK_ENVIRONMENT != 'testing' && empty($this->root_details)){
        $this->root_details = array(
                'login' => $this->promptUserVar('Master account login.',  array('default'=>'admin')),
                'email' => $this->promptUserVar('Master account email.',  array('default'=>'root@example.com')),
                'password' => $this->promptUserVar('Root password.', array('default'=>'admin')),
            );
        }

        $this->addDefaults();
    }

    function down_1()
    {
        $this->dropTables('users, roles, roles_users, permissions_roles,  permissions, extensions');
    }

    function addDefaults()
    {
        if(AK_ENVIRONMENT == 'testing'){
            return ;
        }
        Ak::import('User', 'Role', 'Permission', 'Extension');
        $this->createExtensions();
        $this->createRoles();
        $this->createAdministrator();
    }

    function createExtensions()
    {
        $Extension =& new Extension();
        $this->AdminUsers =& $Extension->create(array('name'=>'Admin::Users','is_core'=>true, 'is_enabled' => true));
        $this->AdminPermissions =& $Extension->create(array('name'=>'Admin::Permissions','is_core'=>true, 'is_enabled' => true));
        $this->AdminRoles =& $Extension->create(array('name'=>'Admin::Roles','is_core'=>true, 'is_enabled' => true));
        $this->AdminDashboard =& $Extension->create(array('name'=>'Admin::Dashboard','is_core'=>true, 'is_enabled' => true));
        $this->AdminMenuTabs =& $Extension->create(array('name'=>'Admin Menu Tabs','is_core'=>true, 'is_enabled' => true));
    }

    function createRoles()
    {
        $Role =& new Role();
        $ApplicationOwner =& $Role->create(array('name' => 'Application owner'));

        $Administrator =& $ApplicationOwner->addChildrenRole('Administrator');

        foreach (Ak::toArray('add,destroy,edit,index,listing,show') as $action){
            $Administrator->addPermission(array('name'=>$action.' action', 'extension' => $this->AdminUsers));
        }
        $Administrator->addPermission(array('name'=>'Manage Users (users controller)', 'extension' => $this->AdminMenuTabs));
        $Administrator->addPermission(array('name'=>'Accounts (users controller, listing action)', 'extension' => $this->AdminMenuTabs));
        $Administrator->addPermission(array('name'=>'Edit other users', 'extension' => $this->AdminUsers));

        $NormalUser =& $Administrator->addChildrenRole('Registered user');
        $NormalUser->addPermission(array('name'=>'index action', 'extension' => $this->AdminDashboard));
        $NormalUser->addPermission(array('name'=>'Dashboard (dashboard controller)', 'extension' => $this->AdminMenuTabs));

    }

    function createAdministrator()
    {
        $Role =& new Role();
        $ApplicationOwner =& new User(array(
        'login'=>$this->root_details['login'], 
        'email'=>$this->root_details['email'], 
        'password'=> $this->root_details['password'], 
        'password_confirmation'=>$this->root_details['password']));
        $ApplicationOwner->role->add($Role->findFirstBy('name', 'Application owner'));
        $ApplicationOwner->save();
    }
}

?>