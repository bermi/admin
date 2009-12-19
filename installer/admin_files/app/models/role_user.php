<?php 

class RoleUser extends ActiveRecord 
{
    public $_avoidTableNameValidation = true;
    public $locale_namespace = 'admin_plugin';
    
    public function __construct() {
        $attributes = (array)func_get_args();
        $this->setTableName('roles_users', true, true);
        $this->init($attributes);
    }
}

