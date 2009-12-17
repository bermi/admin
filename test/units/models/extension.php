<?php

include_once dirname(__FILE__).'/../../config.php';

class ExtensionTestCase extends AdminPluginUnitTest
{
    public $module = 'admin';
    public $insert_models_data = true;

    public function test_setup() {
        $this->uninstallAndInstallMigration('AdminPlugin');
        $this->Extension = new Extension();
        $this->populateTables('extensions');
    }

    public function test_should_disable_enabled_extension() {
        $Page = $this->Extension->findFirstBy('name', 'page');
        $this->assertTrue($Page->is_enabled);
        $Page->disable();
        $Page->reload();
        $this->assertFalse($Page->is_enabled);
    }

    public function test_should_enable_disabled_extension() {
        $Page = $this->Extension->findFirstBy('name', 'page');
        $this->assertFalse($Page->is_enabled);
        $Page->enable();
        $Page->reload();
        $this->assertTrue($Page->is_enabled);
    }

    public function test_should_not_allow_duplicated_extensions() {
        $Test = $this->Extension->create(array('name' => 'test', 'description' => 'Testing permission', 'is_enabled' => false));
        $Test = $this->Extension->create(array('name' => 'test', 'description' => 'Testing permission', 'is_enabled' => false));
        $this->assertTrue($Test->hasErrors() && $Test->isNewRecord());
    }

    public function test_should_add_extension_permission_avoiding_duplicates() {
        $Page = $this->Extension->findFirstBy('name', 'page');
        $this->assertTrue($Page->permission->create(array('name' => 'Create pages')));
        $this->assertTrue($Page->save());


        // This one is a duplicated permission
        $this->assertTrue($Page->permission->create(array('name' => 'Create pages')));
        $this->assertFalse($Page->save());

        $Page = $this->Extension->findFirstBy('name', 'page', array('include' => 'permissions'));
        $this->assertEqual(count($Page->permissions), 1);
        $this->assertEqual($Page->permissions[0]->get('name'), 'Create pages');
    }
}

ak_test_case('ExtensionTestCase');