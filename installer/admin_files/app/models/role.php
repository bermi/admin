<?php

class Role extends ActiveRecord
{
    public $habtm = array(
    'users' => array('unique'=>true),
    'permissions',
    );
    public $acts_as = 'nested_set';
    public $locale_namespace = 'admin_plugin';

    public function validate() {
        $this->validatesPresenceOf('name');
        $this->validatesUniquenessOf('name'); // for hierarchical rbac add: array('scope'=>'parent_id')
        if($this->nested_set->isRoot() && empty($this->is_enabled)){
            $this->addError('is_enabled', 'Can\'t be disabled for on root.');
            $this->set('is_enabled', true);
        }
    }

    public function &createUnder($Parent, $Child) {
        if(is_string($Child)){
            $Child = new Role(is_array($Child) ? $Child : array('name' => $Child));
        }
        $Child->addUnder($Parent);
        return $Child;
    }
    
    public function addUnder($Parent) {
        $this->transactionStart();
        $is_new_record = $this->isNewRecord();
        if(is_string($Parent)){
            $Parent = $this->findFirstBy('name', $Parent);
        }
        if($Parent && (!$this->isNewRecord() || $this->save())){
            $Parent->nested_set->addChild($this);
        }
        if($this->hasErrors()){
            $this->transactionFail();
            if($is_new_record){
                unset($this->id, $this->_newRecord);
            }
        }
        $this->transactionComplete();
    }

    public function &addChildrenRole($Children) {
        return $this->createUnder($this, $Children);
    }

    public function addPermission($Permission) {
        if(!is_object($Permission)){
            $PermissionInstance = new Permission();
            if(!is_array($Permission)){
                $Permission = $PermissionInstance->findOrCreateBy('name', $Permission);
            }else{
                if(isset($Permission['extension']) && is_object($Permission['extension'])){
                    $Permission['extension_id'] = $Permission['extension']->getId();
                    unset($Permission['extension']);
                }

                $args = array(join(' AND ', array_keys($Permission)).' ');
                $args = array_merge($args, $Permission);
                $Permission = call_user_func_array(array($PermissionInstance, 'findOrCreateBy'), $args);
            }
        }
        return $this->permission->add($Permission);
    }

    public function &getPermissions() {
        $this->permission->load();
        $Permissions = empty($this->permissions) ? array() : $this->permissions;
        $ChildrenRoles = $this->nested_set->getChildren();
        if(!empty($ChildrenRoles)){
            foreach ($ChildrenRoles as $Role){
                $Permissions = array_merge($Permissions, $Role->getPermissions());
            }
        }
        return $Permissions;
    }
}

