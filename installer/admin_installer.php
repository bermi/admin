<?php

define('AK_ADMIN_PLUGIN_FILES_DIR', AK_APP_PLUGINS_DIR.DS.'admin'.DS.'installer'.DS.'admin_files');

class AdminInstaller extends AkInstaller
{
    public $skip_db_sql = true;
    
    public function up_1() {
        $this->copyAdminFiles();
        echo "\nWe need some details for setting up the admin.\n\n ";
        $this->modifyRoutes();
        $this->relativizeStylesheetPaths();
        $this->runMigration();
        echo "\n\nInstallation completed\n";
    }

    public function down_1() {
        include_once(AK_APP_INSTALLERS_DIR.DS.'admin_plugin_installer.php');
        $Installer = new AdminPluginInstaller();

        echo "Uninstalling the admin plugin migration\n";
        $Installer->uninstall();
        $Installer->removeFilesFromApp(AK_ADMIN_PLUGIN_FILES_DIR);
    }

    public function copyAdminFiles() {
        $this->copyFilesIntoApp(AK_ADMIN_PLUGIN_FILES_DIR, $this->options['force']);
    }

    public function modifyRoutes() {
        $prefix = '/'.trim(AkConsole::promptUserVar('Admin url prefix',  array('default'=>'/admin/')), "\t /").'/';
        $path = AK_CONFIG_DIR.DS.'routes.php';
        Ak::file_put_contents($path, str_replace('<?php',"<?php \n\n \$Map->connect('$prefix:controller/:action/:id', array('controller' => 'dashboard', 'action' => 'index', 'module' => 'admin'));",Ak::file_get_contents($path)));

    }

    public function runMigration() {
        include_once(AK_APP_INSTALLERS_DIR.DS.'admin_plugin_installer.php');
        $Installer = new AdminPluginInstaller();

        echo "Running the admin plugin migration\n";
        $Installer->install();
    }
    public function relativizeStylesheetPaths() {
        $url_suffix = AkConsole::promptUserVar(
        'The admin plugin comes with some fancy CSS background images.

Your application might be accessible at /myapp, 
and your images folder might be at /myapp/public

Insert the relative path where your images folder is
so you don\'t need to manually edit the CSS files', array('default'=>'/'));
        
        $url_suffix =  trim(preg_replace('/\/?images\/admin\/?$/','',$url_suffix),'/');
        
        if(!empty($url_suffix)){
            $stylesheets = array('admin/admin','admin/menu');
            foreach ($stylesheets as $stylesheet) {
                $filename = AK_PUBLIC_DIR.DS.'stylesheets'.DS.$stylesheet.'.css';
                $relativized_css = preg_replace("/url\((\'|\")?\/images/","url($1/$url_suffix/images", @Ak::file_get_contents($filename));
                !empty($relativized_css) && @Ak::file_put_contents($filename, $relativized_css);
            }
        }
    }

}

