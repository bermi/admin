<?php

include_once dirname(__FILE__).'/../config.php';

class UserTestCase extends AdminPluginUnitTest
{
    public $module = 'admin';

    public $insert_models_data = true;
    
    public function __construct(){
        parent::__construct();       
        AkConfig::setOption('test_mode_settings_namespace', 'admin_testing');
        $config_file = realpath(AkConfig::getDir('suite').DS.'..'.DS.'..'.DS.'..'.DS.'installer'.DS.'admin_files'.DS.'config').DS.'admin.yml';
        copy($config_file, AkConfig::getDir('config').DS.'admin_testing.yml');
    }

    public function __destruct(){      
        unlink(AkConfig::getDir('config').DS.'admin_testing.yml');
        AkConfig::setOption('test_mode_settings_namespace', null);
        parent::__destruct();
    }
    
    public function setup(){
        $this->Sentinel = new Sentinel();
        $Controller = new stdClass;
        $this->Sentinel->Controller = $Controller;
    }
    
    public function test_setup() {
        $this->uninstallAndInstallMigration('AdminPlugin');
        $this->includeAndInstatiateModels('User', 'Sentinel', 'Role', 'Permission');
    }

    public function test_should_request_valid_password() {
        $Alicia = new User(array('email' => 'alicia@example.com', 'login'=>'alicia', 'password' => 'abcd1234'));
        $this->assertFalse($Alicia->save());
        $this->assertEqual("can't be blank", $Alicia->getErrorsOn('password_confirmation'));

        $Alicia->setAttributes(array('password' => 'abcd1234','password_confirmation' => 'abcd1234'));
        $this->assertTrue($Alicia->save());
        $this->assertNotEqual($Alicia->get('password'), 'abcd1234');
        $this->assertTrue(strlen($Alicia->get('password_salt')) == 16);
    }

    public function test_should_avoid_replicated_users() {
        $Alicia = new User(array('email' => 'alicia@example.com', 'login'=>'alicia', 'password' => 'abcd1234', 'password_confirmation' => 'abcd1234'));
        $this->assertFalse($Alicia->save());
        $this->assertEqual("email alicia@example.com already in use", $Alicia->getErrorsOn('email'));
        $this->assertEqual("login alicia already in use", $Alicia->getErrorsOn('login'));
    }

    public function test_should_prevent_from_using_invalid_email_addresses() {
        $Bogus = new User(array('email' => 'bogus', 'login'=>'alicia', 'password' => 'abcd1234', 'password_confirmation' => 'abcd1234'));
        $this->assertFalse($Bogus->save());
        $this->assertEqual("Invalid email address", $Bogus->getErrorsOn('email'));
    }
    
    public function test_should_update_without_changing_password() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $pass = $Alicia->get('password');
        $Alicia->save();
        $Alicia->reload();
        $this->assertEqual($Alicia->get('password'), $pass);
    }

    public function test_should_not_update_password_if_no_confirmation_is_provided() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $pass = $Alicia->get('password');
        $Alicia->set('password', 'badpass');
        $this->assertFalse($Alicia->save());
        $Alicia->reload();
        $this->assertEqual($Alicia->get('password'), $pass);
    }

    public function test_should_update_password() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $pass = $Alicia->get('password');
        $Alicia->set('password', 'goodpass');
        $Alicia->set('password_confirmation', 'goodpass');
        $this->assertTrue($Alicia->save());
        $Alicia->reload();
        $this->assertNotEqual($Alicia->get('password'), $pass);
    }

    public function test_should_emit_and_and_validate_single_use_login_token() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $token = $Alicia->getToken(array('single_use'=> true));
    }

    public function test_should_emit_and_and_validate_login_token() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $token = $Alicia->getToken();
        $this->assertTrue($User = $this->Sentinel->authenticateWithToken($token));        
        $this->assertEqual($Alicia->get('login'), $User->get('login'));
        $this->assertTrue($User = $this->Sentinel->authenticateWithToken($token));
    }
    
    public function test_should_issue_expiring_tokens() {
        
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $token = $Alicia->getToken(array('expires'=>1));
        $this->assertTrue($User = $this->Sentinel->authenticateWithToken($token));        
        $this->assertTrue($User = $this->Sentinel->authenticateWithToken($token));        
        $this->assertEqual($Alicia->get('login'), $User->get('login'));
        sleep(2);
        $this->assertFalse($User = $this->Sentinel->authenticateWithToken($token));
    }

    public function test_should_detect_if_given_password_is_valid() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $this->assertTrue($Alicia->isValidPassword('goodpass'));
        $this->assertFalse($Alicia->isValidPassword('badone'));
    }

    public function test_should_avoid_changing_login_if_no_password_is_provided() {
        $Alicia = $this->User->findFirstBy('login', 'alicia');
        $Alicia->set('login', 'aliciasadurni');
        $this->assertFalse($Alicia->save());

        $Alicia->set('password', 'badpass');

        $this->assertFalse($Alicia->save());

        $Alicia->reload();
        $Alicia->set('login', 'aliciasadurni');
        $Alicia->set('password', 'goodpass');

        $this->assertTrue($Alicia->save());
    }


    public function test_should_set_roles() {
        $Alicia = $this->User->findFirstBy('login', 'aliciasadurni');

        $this->_createRoles();
        
        $Visitor = $this->Role->findFirstBy('name', 'Visitor');
        $Alicia->role->add($Visitor);
        $Editor = $this->Role->findFirstBy('name', 'Editor');
        $Alicia->role->add($Editor);
        $Copy = $this->Role->findFirstBy('name', 'Copywriter');
        $Alicia->role->add($Copy);
        $Alicia->save();

        $Alicia->reload();
        $Alicia->role->load();

        $this->assertTrue($Alicia->role->count(), 3);

    }


    public function test_should_be_able_to_authenticate() {
        $this->assertFalse(User::authenticate('aliciasadurni', 'badpass'));
        $this->assertTrue($Alicia = User::authenticate('aliciasadurni', 'goodpass'));
        $this->assertNotNull($Alicia->get('last_login_at'), 'Should update last_login_at');
        $this->assertEqual(substr($Alicia->get('last_login_at'),0,-2), substr(Ak::getDate(),0,-2));
    }

    public function test_should_create_disabled_user() {
        $Bermi = new User(array('email'=>'bermi@example.com', 'login'=>'bermi', 'password'=>'abcde', 'password_confirmation'=>'abcde', 'is_enabled' => false));
        $this->assertTrue($Bermi->save());

        $this->assertFalse($Bermi->get('is_enabled'));
    }

    public function test_should_only_authenticate_users_with_roles() {
        $Bermi = $this->User->findFirstBy('login', 'bermi');
        $Bermi->enable();
        $this->assertFalse(User::authenticate('bermi', 'abcde'));
        $Bermi->role->add(new Role(array('name'=>'Tmp Role')));
        $Bermi->save();
        $this->assertTrue(User::authenticate('bermi', 'abcde'));
    }


    public function test_should_only_authenticate_enabled_users() {
        $Bermi = $this->User->findFirstBy('login', 'bermi');

        $this->assertTrue($User = User::authenticate('bermi', 'abcde'));

        $Bermi->disable();
        $this->assertFalse(User::authenticate('bermi', 'abcde'));

        $Role = new Role();
        $Role = $Role->findFirstBy('name', 'Tmp Role');
        $Role->destroy();
    }


    public function test_should_get_roles() {
        $Alicia = $this->User->findFirstBy('login', 'aliciasadurni');
        $Alicia->role->load();
        $this->assertEqual(array_values($Alicia->collect($Alicia->roles, 'id','name')), array('Visitor', 'Editor', 'Copywriter'));
    }

    public function test_should_get_permissions() {
        $Alicia = $this->User->findFirstBy('login', 'aliciasadurni');
        $this->assertEqual($this->_getPermissionDescriptionsForUser($Alicia), array('authenticate','create','edit','list','view'));
    }

    public function test_should_verify_user_credential_for_specific_tasks() {
        $Alicia = $this->User->findFirstBy('login', 'aliciasadurni');

        $this->assertTrue($Alicia->can('authenticate'));
        $this->assertTrue($Alicia->can('create'));
        $this->assertTrue($Alicia->can('edit'));
        $this->assertTrue($Alicia->can('list'));
        $this->assertTrue($Alicia->can('view'));

        $this->assertFalse($Alicia->can('remove'));
        $this->assertFalse($Alicia->can('connect'));
    }

    public function test_should_verify_user_credential_for_specific_tasks_on_extensions() {
        $Alicia = $this->User->findFirstBy('login', 'aliciasadurni');
        $Developer = $this->Role->findFirstBy('name', 'Developer');
        $Alicia->role->add($Developer);

        $this->assertTrue($Alicia->can('connect', 2, true));
        $this->assertTrue($Alicia->can('connect', 2));
        $this->assertTrue($Alicia->can('remove', 2));
        $this->assertFalse($Alicia->can('remove'));
        $this->assertFalse($Alicia->can('connect'));
    }

    public function test_should_set_user_roles_by_id() {
        $Administrator = $this->Role->findFirstBy('name', 'Administrator');
        $Developer = $this->Role->findFirstBy('name', 'Developer');
        $Visitor = $this->Role->findFirstBy('name', 'Visitor');


        $Salavert = new User(array('email'=>'salavert@example.com', 'login'=>'salavert', 'password'=>'abcde', 'password_confirmation'=>'abcde'));
        $this->assertTrue($Salavert->save());

        $expected_ids = array($Administrator->id, $Developer->id);
        $Salavert->role->load();
        $Salavert->role->setByIds($expected_ids);

        $Salavert->reload();

        $this->assertEqual(count($Salavert->roles), 2);
        
        $found_ids = array_values($Salavert->collect($Salavert->roles, 'id', 'id'));

        sort($found_ids);
        sort($expected_ids);
        $this->assertEqual($found_ids, $expected_ids);

        $Salavert->role->setByIds(array($Visitor->id));

        $Salavert = $Salavert->find($Salavert->id, array('include'=>'roles'));

        $this->assertEqual(count($Salavert->roles), 1);
        $this->assertEqual($Salavert->roles[0]->id, $Visitor->id);
    }

    /**/
    public function _createRoles() {
        $Administrator = $this->Role->create(array('name' => 'Administrator'));

        // Page roles
        $Collaborator = $Administrator->addChildrenRole('Collaborator');
        $Collaborator->addPermission('create');
        $Collaborator->addPermission('rename');

        $Authenticated = $Collaborator->addChildrenRole('Authenticated');
        $Authenticated->addPermission('comment');

        $Visitor = $Authenticated->addChildrenRole('Visitor');
        $Visitor->addPermission('authenticate');
        $Visitor->addPermission('view');
        $Visitor->addPermission('list');

        // API Roles
        $Developer = $Administrator->addChildrenRole('Developer');
        $Developer->addPermission(array('name'=>'connect','extension_id'=>2));
        $Developer->addPermission(array('name'=>'remove','extension_id'=>2));

        // Outsourced
        $ServiceProviders = $Administrator->addChildrenRole('Service providers');
        $ContentManagement = $ServiceProviders->addChildrenRole('Content management');
        $ContentManagement->addPermission('create');

        $Editor = $ContentManagement->addChildrenRole('Editor');
        $Editor->addPermission('edit');
        $Translator = $ContentManagement->addChildrenRole('Translator');
        $Translator->addPermission('fork');
        $Translator->addPermission('edit');
        $Legal = $ServiceProviders->addChildrenRole('Legal');
        $Legal->addPermission('warn');

        $Copywriter = $Legal->addChildrenRole('Copywriter');
        $Copywriter->addPermission('edit');
        $Copywriter->addPermission('create');
        $Auditor = $Legal->addChildrenRole('Auditor');
        $Auditor->addPermission('remove');
        $Auditor->addPermission('warn');
    }

    public function _getPermissionDescriptionsForUser(&$User) {
        $permissions = array_values($User->collect($User->getPermissions(),'id','name'));
        sort($permissions);
        return $permissions;
    }
}


ak_test_case('UserTestCase');

