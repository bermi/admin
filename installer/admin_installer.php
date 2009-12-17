<?php

define('AK_ADMIN_PLUGIN_FILES_DIR', AK_APP_PLUGINS_DIR.DS.'admin'.DS.'installer'.DS.'admin_files');

class AdminInstaller extends AkInstaller
{
    function up_1()
    {
        $this->files = Ak::dir(AK_ADMIN_PLUGIN_FILES_DIR, array('recurse'=> true));
        empty($this->options['force']) ? $this->checkForCollisions($this->files) : null;
        $this->copyAdminFiles();
        echo "\nWe need some details for setting up the admin.\n\n ";
        $this->modifyRoutes();
        $this->relativizeStylesheetPaths();
        $this->runMigration();
        echo "\n\nInstallation completed\n";
    }

    function down_1()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'admin_plugin_installer.php');
        $Installer =& new AdminPluginInstaller();

        echo "Uninstalling the admin plugin migration\n";
        $Installer->uninstall();
    }


    function checkForCollisions(&$directory_structure, $base_path = AK_ADMIN_PLUGIN_FILES_DIR)
    {
        foreach ($directory_structure as $k=>$node){
            if(!empty($this->skip_all)){
                return ;
            }
            $path = str_replace(AK_ADMIN_PLUGIN_FILES_DIR, AK_BASE_DIR, $base_path.DS.$node);
            if(is_file($path)){
                $message = Ak::t('File %file exists.', array('%file'=>$path));
                $user_response = AkInstaller::promptUserVar($message."\n d (overwrite mine), i (keep mine), a (abort), O (overwrite all), K (keep all)", 'i');
                if($user_response == 'i'){
                    unset($directory_structure[$k]);
                }    elseif($user_response == 'O'){
                    return false;
                }    elseif($user_response == 'K'){
                    $directory_structure = array();
                    return false;
                }elseif($user_response != 'd'){
                    echo "\nAborting\n";
                    exit;
                }
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        if($this->checkForCollisions($directory_structure[$k][$dir], $path) === false){
                            $this->skip_all = true;
                            return;
                        }
                    }
                }
            }
        }
    }

    function copyAdminFiles()
    {
        $this->_copyFiles($this->files);
    }

    function modifyRoutes()
    {
        $preffix = '/'.trim($this->promptUserVar('Admin url preffix',  array('default'=>'/admin/')), "\t /").'/';
        $path = AK_CONFIG_DIR.DS.'routes.php';
        Ak::file_put_contents($path, str_replace('<?php',"<?php \n\n \$Map->connect('$preffix:controller/:action/:id', array('controller' => 'dashboard', 'action' => 'index', 'module' => 'admin'));",Ak::file_get_contents($path)));

    }

    function runMigration()
    {
        include_once(AK_APP_INSTALLERS_DIR.DS.'admin_plugin_installer.php');
        $Installer =& new AdminPluginInstaller();

        echo "Running the admin plugin migration\n";
        $Installer->install();
    }
    function relativizeStylesheetPaths()
    {
        $url_suffix = AkInstaller::promptUserVar(
        'The admin plugin comes with some fancy CSS background images.

Your aplication might be accesible at /myapp, 
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

    function _copyFiles($directory_structure, $base_path = AK_ADMIN_PLUGIN_FILES_DIR)
    {
        foreach ($directory_structure as $k=>$node){
            $path = $base_path.DS.$node;
            if(is_dir($path)){
                echo 'Creating dir '.$path."\n";
                $this->_makeDir($path);
            }elseif(is_file($path)){
                echo 'Creating file '.$path."\n";
                $this->_copyFile($path);
            }elseif(is_array($node)){
                foreach ($node as $dir=>$items){
                    $path = $base_path.DS.$dir;
                    if(is_dir($path)){
                        echo 'Creating dir '.$path."\n";
                        $this->_makeDir($path);
                        $this->_copyFiles($items, $path);
                    }
                }
            }
        }
    }

    function _makeDir($path)
    {
        $dir = str_replace(AK_ADMIN_PLUGIN_FILES_DIR, AK_BASE_DIR,$path);
        if(!is_dir($dir)){
            mkdir($dir);
        }
    }

    function _copyFile($path)
    {
        $destination_file = str_replace(AK_ADMIN_PLUGIN_FILES_DIR, AK_BASE_DIR,$path);
        copy($path, $destination_file);
        $source_file_mode =  fileperms($path);
        $target_file_mode =  fileperms($destination_file);
        if($source_file_mode != $target_file_mode){
            chmod($destination_file,$source_file_mode);
        }
    }

}

?>