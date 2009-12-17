<?php

class PermissionHelper extends AkActionViewHelper
{
    function permission_check_box(&$Permission, &$Extenssion, &$Role)
    {
        $options = array(
        'id' => 'permissions_'.$Permission->getId().'_'.$Role->getId(),
        'name' => 'permissions['.$Permission->getId().']['.$Role->getId().']',
        'type' => 'checkbox'
        );
        $Role->permission->load();
        if(in_array($Permission->getId(), $Permission->collect($Role->permissions, 'id', 'id'))){
            $options['checked'] = 'checked';
        }

        if(!$Role->get('is_enabled')){
            $options['disabled'] = 'disabled';
        }

        return TagHelper::tag('input', array('name' => $options['name'], 'type' => 'hidden',
        'value' => 0)).TagHelper::tag('input', $options);
    }

    function link_to_destroy(&$record)
    {
        if(AK_DEV_MODE && User::can('destroy action', 'Admin::Permissions')){
            return $this->_controller->url_helper->link_to($this->_controller->t('delete'), array('action' => 'destroy', 'id' => $record->getId()), array('class'=>'seccondary'));
        }
    }

    function confirm_delete()
    {
        return '<input type="submit" value="'.$this->_controller->t('Delete').'" />';
    }

    function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Update permissions').'" class="primary" />';
    }
    
}
?>