<?php

defined('AK_EXTENSION_DIR') || define('AK_EXTENSION_DIR', AK_APP_DIR.DS.'extensions');

class Extension extends ActiveRecord
{
    public $has_many = array('permissions');

    public function validate() {
        $this->validatesUniquenessOf('name');
        $this->validatesPresenceOf('name');
    }

    public function enable() {
        return $this->_enableOrDisable('enable');
    }

    public function disable() {
        return $this->_enableOrDisable('disable');
    }

    public function _enableOrDisable($enable = 'enable') {
        $enable = $enable == 'enable' || $enable === true;
        if($enable != $this->get('is_enabled')){
            $this->set('is_enabled', $enable);
            $success = $this->save();
            return $success && $this->_installOrUninstallExtension($enable ? 'install' : 'uninstall');
        }
        return false;
    }

    public function getExtensionsBasePath() {
        return AkConfig::getDir('extension');
    }

    public function getExtensionPath() {
        $path = $this->getExtensionsBasePath().DS.$this->get('name');
        return is_dir($path) ? $path : false;
    }

    public function getInstallerPath() {
        $path = ($dir = $this->getExtensionPath()) ? $dir.DS.'installer.php' : false;
        return $path && file_exists($path) ? $path : false;
    }

    protected function _installOrUninstallExtension($action = 'install') {
        if($installer_path = $this->getInstallerPath()){
            include_once($installer_path);
            $installer_class_name = AkInflector::camelize($this->get('name')).'ExtensionInstaller';
            if(class_exists($installer_class_name)){
                $Installer = new $installer_class_name();
                if(method_exists($Installer, $action)){
                    $Installer->Extension = $this;
                    return $Installer->$action();
                }
            }
        }
        return true;
    }

}


