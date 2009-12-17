<?php  echo '<?php'?>


class <?php  echo $helper_name?> extends AkActionViewHelper
{ 
    function cancel_link($url = array('action' => 'listing'))
    {
        return $this->_controller->url_helper->link_to($this->t('Cancel'), $url, array('class'=>'action'));
    }

    function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" />';
    }

    function confirm_delete()
    {
        return '<input type="submit" value="'.$this->_controller->t('Delete').'" />';
    }

    function link_to_show(&$Record)
    {
        if(User::can('show action', 'Admin::<?php  echo $controller_name?>')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $Record->getId()), array('class'=>'action'));
        }
    }
    
    function link_to_edit(&$Record)
    {
        if(User::can('edit action', 'Admin::<?php  echo $controller_name?>')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $Record->getId()), array('class'=>'action'));
        }
    }
    
    function link_to_destroy(&$Record)
    {
        if(User::can('destroy action', 'Admin::<?php  echo $controller_name?>')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Delete'), array('action' => 'destroy', 'id' => $Record->getId()), array('class'=>'action'));
        }
    }
}

?>
