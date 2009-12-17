<?php

class RoleHelper extends AkActionViewHelper
{
    function cancel_link($url = array('action' => 'listing'))
    {
        if(!empty($this->_controller->role->id)){
            $url['id'] = $this->_controller->role->id;
        }
        return $this->_controller->url_helper->link_to($this->t('Cancel'),$url, array('class'=>'action'));
    }

    function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" />';
    }

    function confirm_delete()
    {
        return '<input type="submit" value="'.$this->_controller->t('Delete').'" />';
    }

    function link_to_show(&$record)
    {
        if(User::can('show action', 'Admin::Roles')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $record->getId()), array('class'=>'action'));
        }
    }

    function link_to_edit(&$record)
    {
        if(User::can('edit action', 'Admin::Roles')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $record->getId()), array('class'=>'action'));
        }
    }

    function link_to_destroy(&$record)
    {
        if(User::can('destroy action', 'Admin::Roles')){
            return $this->_controller->url_helper->link_to($this->_controller->t('Delete'), array('action' => 'destroy', 'id' => $record->getId()), array('class'=>'action'));
        }
    }


    function display_tree_recursivelly($tree, $parent_id = null, $options = array())
    {
        if(!empty($tree)){
            foreach(array_keys($tree) as $k){
                $Node =& $tree[$k];
                if($Node->parent_id == $parent_id){
                    $result = empty($result) ? "\n<ul>\n" : $result;
                    $result .= "\n<li>\n";
                    $result .= $this->link_to_node($Node, $options);
                    $result .= $this->display_tree_recursivelly($tree, $Node->id, $options);
                    $result .= "\n</li>\n";
                }
            }

            return empty($result) ? '' : $result."\n</ul>\n";
        }
    }

    function select_as_tree($tree)
    {
        $collection = $this->_getRolesForSelect($tree);
        return $this->_controller->form_options_helper->select('role', 'parent_id', $collection);
    }

    function _getRolesForSelect($tree, $parent_id = null, $level = 0)
    {
        $result = array();
        if(!empty($tree)){
            foreach(array_keys($tree) as $k){
                $Node =& $tree[$k];
                if($Node->parent_id == $parent_id){
                    $result[($level>0?str_repeat('–', $level).' ':'').$Node->get('name')] = $Node->getId();
                    foreach ($this->_getRolesForSelect($tree, $Node->id, $level+1) as $k=>$v){
                        $result[$k] = $v;
                    }
                }
            }

            return $result;
        }
    }

    function link_to_node($Node, $options = array())
    {
        $detault_options = array(
        'display' => 'name',
        'id' => $Node->id,
        'controller'=> AkInflector::underscore($Node->getModelName()),
        'action' => 'edit'
        );
        $options = array_merge($detault_options, $options);
        $display = $Node->get($options['display']);
        unset($options['display']);
        return $this->_controller->url_helper->link_to($display, $options);
    }
}

?>