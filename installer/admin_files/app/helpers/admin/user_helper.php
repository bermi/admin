<?php

class UserHelper extends AkActionViewHelper
{
    public function cancel_link($url = array('action' => 'listing')) {
        if(!empty($this->_controller->User->id)){
            $url['id'] = $this->_controller->User->id;
        }
        return $this->_controller->url_helper->link_to($this->t('Cancel'),$url, array('class'=>'action'));
    }

    public function save_button() {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" tabindex="10" />';
    }

    public function confirm_delete() {
        return '<input type="submit" value="'.$this->t('Delete').'" class="primary" /> ';
    }

    public function link_to_show(&$record) {
        if(User::currentUserCan('show action', 'Admin::Users')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $record->getId()), array('class'=>'action'));
        }
    }

    public function link_to_edit(&$record) {
        if(User::currentUserCan('edit action', 'Admin::Users') && (User::currentUserCan('Edit other users', 'Admin::Users')
        || $this->_controller->CurrentUser->id == $record->id )){
            return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $record->getId()), array('class'=>'action'));
        }
    }

    public function link_to_destroy(&$record) {
        if(User::currentUserCan('destroy action', 'Admin::Users') &&
        $this->_controller->CurrentUser->id != $record->id && (User::currentUserCan('Edit other users', 'Admin::Users') || $this->_controller->CurrentUser->id == $record->id )){
            return $this->_controller->url_helper->link_to($this->_controller->t('Delete'), array('action' => 'destroy', 'id' => $record->getId()), array('class'=>'action'));
        }
    }
}

