<?php 

class PermissionRole extends ActiveRecord 
{
    public $_avoidTableNameValidation = true;
    
    public function PermissionRole()
    {
        $this->setModelName("PermissionRole");
        $attributes = (array)func_get_args();
        $this->setTableName('permissions_roles', true, true);
        $this->init($attributes);
    }
}

?>