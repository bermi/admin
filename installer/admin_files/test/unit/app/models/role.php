<?php

class RoleTestCase extends AkUnitTest
{
    var $module = 'admin';

    function test_setup()
    {
        $this->uninstallAndInstallMigration('AdminPlugin');
        $this->includeAndInstatiateModels('User', 'Role', 'Permission');
    }

    function test_should_create_root_role()
    {
        $this->assertTrue($Admin =& $this->Role->create(array('name' => 'Administrator')));
        $this->assertTrue($Admin->nested_set->isRoot());
    }

    function test_should_create_direct_child_of_root_node()
    {
        $Manager =& $this->Role->createUnder('Administrator','Manager');
        $this->assertTrue($Manager->nested_set->isChild());
        $Parent =& $Manager->nested_set->getParent();
        $Admin =& $this->Role->findFirstBy('name', 'Administrator');
        $this->assertEqual($Parent->getAttributes(), $Admin->getAttributes());
    }

    function test_should_create_new_branch()
    {
        $this->assertTrue($Auditor =& $this->Role->create(array('name' => 'Auditor')));
        $Inspector =& $this->Role->createUnder($Auditor, 'Inspector');
        $ShouldBeAuditor =& $Inspector->nested_set->getParent();
        $this->assertEqual($Auditor->nested_set->countChildren(), 1);
        $this->assertEqual($Auditor->getAttributes(), $ShouldBeAuditor->getAttributes());
    }

    function test_should_add_supervisor_under_manager()
    {
        $Supervisor =& $this->Role->createUnder('Manager','Supervisor');
        $Admin =& $this->Role->findFirstBy('name', 'Administrator');
        $this->assertEqual($Admin->nested_set->countChildren(), 2);
    }

    function test_should_not_duplicate_roles_for_user()
    {
        $Bermi =& $this->User->create(array('email'=>'bermi@example.com', 'login'=>'bermi', 'password'=>'pass', 'password_confirmation'=>'pass'));
        $this->assertFalse($Bermi->isNewRecord());
        $Developer =& $this->Role->createUnder('Auditor', 'Developer');

        $Developer->user->add($Bermi);
        $Developer->save();

        $Bermi->reload();
        $Bermi->role->load(true);

        $this->assertEqual($Bermi->roles[0]->getAttributes(), $Developer->getAttributes());

        $Developer->user->add($Bermi);
        $Developer->save();

        $Bermi->reload();
        $Bermi->role->load();

        $this->assertEqual($Bermi->role->count(), 1);
    }

    function test_should_not_duplicate_permissions_for_role()
    {
        $CreateUser =& $this->Permission->create(array('name' => 'create user'));
        $this->assertTrue(!$CreateUser->isNewRecord());

        $Developer =& $this->Role->findFirstBy('name', 'Developer');

        $Developer->addPermission($CreateUser);
        $Developer->addPermission($CreateUser);

        $CreateUser->reload();
        $CreateUser->role->load();
        $this->assertEqual($CreateUser->roles[0]->getAttributes(), $Developer->getAttributes());
        $this->assertEqual($CreateUser->role->count(), 1);
    }

    function test_should_get_permissions_from_children_roles()
    {
        $Supervisor =& $this->Role->findFirstBy('name', 'Supervisor');
        $Manager =& $this->Role->findFirstBy('name', 'Manager');
        $Administrator =& $this->Role->findFirstBy('name', 'Administrator');

        $Supervisor->addPermission('edit');
        $Supervisor->addPermission('create');
        $Supervisor->addPermission('complete');
        $Supervisor->addPermission('reasign');

        $Manager->addPermission('add');
        $Manager->addPermission('assign');

        $Administrator->addPermission('destroy');

        $this->assertEqual($this->_getPermissionDescriptionsForRole($Administrator),
        array('add', 'assign','complete','create','destroy','edit','reasign'));

        $this->assertEqual($this->_getPermissionDescriptionsForRole($Manager),
        array('add', 'assign','complete','create','edit','reasign'));

        $this->assertEqual($this->_getPermissionDescriptionsForRole($Supervisor),
        array('complete','create','edit','reasign'));
    }

    function test_should_allow_permission_name_repetition()
    {
        $Developer =& $this->Role->findFirstBy('name', 'Developer');
        $Developer->addPermission(array('name'=>'edit', 'extension_id'=>5));

        $this->assertEqual($this->_getPermissionDescriptionsForRole($Developer),
        array('create user', 'edit'));

    }

    /**/

    function _getPermissionDescriptionsForRole(&$Role)
    {
        $permissions = array_values($Role->collect($Role->getPermissions(),'id','name'));
        sort($permissions);
        return $permissions;
    }

}

?>
