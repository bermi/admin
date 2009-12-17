<?php

// You should place the plugin in vendor/plugins or define the base path for running tests
// define('AK_BASE_DIR', '/path/to/an/akelos/app');

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

if(!defined('AK_BASE_DIR')){
    $base_dir = realpath(dirname(__FILE__).str_repeat(DS.'..', 7));
    if(!is_dir($base_dir.'test'.DS.'shared')){
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

require_once(AK_BASE_DIR.DS.'test'.DS.'shared'.DS.'config'.DS.'config.php');

class AdminPluginUnitTest extends AkUnitTest
{
    public function __construct() {
        AkConfig::setDir('suite', dirname(__FILE__));
        $this->rebaseAppPaths(realpath(dirname(__FILE__).str_repeat(DS.'..', 3).DS.'installer'.DS.'admin_files'));
        AkUnitTestSuite::cleanupTmpDir();
    }

    public function __destruct() {
        parent::__destruct();
        AkUnitTestSuite::cleanupTmpDir();
        $this->dropTables('all');
    }
}
