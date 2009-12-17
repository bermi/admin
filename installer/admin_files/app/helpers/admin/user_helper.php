<?php

class UserHelper extends AkActionViewHelper
{
    function cancel_link($url = array('action' => 'listing'))
    {
        if(!empty($this->_controller->User->id)){
            $url['id'] = $this->_controller->User->id;
        }
        return $this->_controller->url_helper->link_to($this->t('Cancel'),$url, array('class'=>'action'));
    }

    function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" tabindex="10" />';
    }

    function confirm_delete()
    {
        return '<input type="submit" value="'.$this->t('Delete').'" class="primary" /> ';
    }

    function link_to_show(&$record)
    {
        if(User::can('show action', 'Admin::Users')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $record->getId()), array('class'=>'action'));
        }
    }

    function link_to_edit(&$record)
    {
        if(User::can('edit action', 'Admin::Users') && (User::can('Edit other users', 'Admin::Users')
        || $this->_controller->CurrentUser->id == $record->id )){
            return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $record->getId()), array('class'=>'action'));
        }
    }

    function link_to_destroy(&$record)
    {
        if(User::can('destroy action', 'Admin::Users') &&
        $this->_controller->CurrentUser->id != $record->id && (User::can('Edit other users', 'Admin::Users') || $this->_controller->CurrentUser->id == $record->id )){
            return $this->_controller->url_helper->link_to($this->_controller->t('Delete'), array('action' => 'destroy', 'id' => $record->getId()), array('class'=>'action'));
        }
    }
}

?>