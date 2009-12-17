<?php 


class RoleUser extends ActiveRecord 
{
    public $_avoidTableNameValidation = true;
    
    public function RoleUser()
    {
        $this->setModelName("RoleUser");
        $attributes = (array)func_get_args();
        $this->setTableName('roles_users', true, true);
        $this->init($attributes);
    }
}

?>