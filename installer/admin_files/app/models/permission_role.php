<?php 

class PermissionRole extends ActiveRecord 
{
    public $_avoidTableNameValidation = true;
    
    public function __construct() {
        $attributes = (array)func_get_args();
        $this->setTableName('permissions_roles', true, true);
        $this->init($attributes);
    }
}

