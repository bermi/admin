<?php

// You should place the plugin in vendor/plugins or define the base path for running tests
// define('AK_BASE_DIR', '/path/to/an/akelos/app');

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

if(!defined('AK_BASE_DIR')){
    $base_dir = realpath(dirname(__FILE__).str_repeat(DS.'..', 7));
    if(!is_dir($base_dir.'test'.DS.'shared')){
        define('ADMIN_PLUGIN_RUNNING_ON_APPLICATION_SCOPE', false);
        $akelos_version = trim(@`akelos -v`);
        if(version_compare($akelos_version, '1.0', '>=')){
            $base_dir = trim(@`akelos --base_dir`);
        }
    }
    define('AK_BASE_DIR', $base_dir);
    $test_config_file = AK_BASE_DIR.DS.'test'.DS.'shared'.DS.'config'.DS.'config.php';
    if(!file_exists($test_config_file)){
        die("Could not find a base path for testing your plugin. Please edit ".__FILE__."\n");
    }
}

defined('ADMIN_PLUGIN_RUNNING_ON_APPLICATION_SCOPE') ||
define('ADMIN_PLUGIN_RUNNING_ON_APPLICATION_SCOPE', defined('MAKELOS_STANDALONE') ? !MAKELOS_STANDALONE : true);

require_once(AK_BASE_DIR.DS.'test'.DS.'shared'.DS.'config'.DS.'config.php');

class AdminPluginUnitTest extends AkUnitTest
{
    public function __construct() {
        AkConfig::setDir('suite', dirname(__FILE__));
        if(!ADMIN_PLUGIN_RUNNING_ON_APPLICATION_SCOPE){
            $this->rebaseAppPaths(realpath(dirname(__FILE__).str_repeat(DS.'..', 3).DS.'installer'.DS.'admin_files'));
        }
        AkUnitTestSuite::cleanupTmpDir();
    }

    public function __destruct() {
        if(!ADMIN_PLUGIN_RUNNING_ON_APPLICATION_SCOPE){
            AdminPluginInstaller::setTokenKey('some long and random secret value to avoid being hacked, used for login urls and API calls');
        }
        parent::__destruct();
        AkUnitTestSuite::cleanupTmpDir();
        $this->dropTables('all');
    }
}
