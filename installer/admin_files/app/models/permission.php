<?php

class Permission extends ActiveRecord
{
    var $belongs_to = 'extension';
    var $habtm = array(
        'roles'
        );

    function validate()
    {
        $this->validatesUniquenessOf('name', array('scope' => 'extension_id'));
    }
    
}

?>
