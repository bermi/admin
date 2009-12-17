<?php

class Permission extends ActiveRecord
{
    public $belongs_to = 'extension';
    public $habtm = array(
        'roles'
        );

    public function validate()
    {
        $this->validatesUniquenessOf('name', array('scope' => 'extension_id'));
    }
    
}

?>
