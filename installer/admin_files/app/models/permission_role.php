<?php 

class PermissionRole extends ActiveRecord 
{
    var $_avoidTableNameValidation = true;
    
    function PermissionRole()
    {
        $this->setModelName("PermissionRole");
        $attributes = (array)func_get_args();
        $this->setTableName('permissions_roles', true, true);
        $this->init($attributes);
    }
}

?>