<?php

class Admin_UsersController extends AdminController
{
    var $finder_options = array('User'=>array('include'=>'roles'));

    var $controller_menu_options = array(
    'Accounts'   => array('id' => 'accounts', 'url'=>array('controller'=>'users', 'action'=>'listing')),
    'Roles'   => array('id' => 'roles', 'url'=>array('controller'=>'roles')),
    'Permissions'   => array('id' => 'permissions', 'url'=>array('controller'=>'permissions', 'action'=>'manage')),
    );
    var $controller_selected_tab = 'Accounts';

    function index()
    {
        $this->redirectToAction('listing');
    }

    function listing()
    {
        $this->user_pages = $this->pagination_helper->getPaginator($this->User, array('items_per_page' => 50));
        $finder_options = $this->pagination_helper->getFindOptions($this->User);
        empty($finder_options['order']) ? $finder_options['order'] = 'created_at DESC' : null;

        if (!$this->Users =& $this->User->find('all', $finder_options)){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('It seems like you don\'t have Users on your site. Please fill in the form below in order to create your first user.');
            $this->redirectTo(array('action' => 'add'));
        }
    }

    function show()
    {
        if (!$this->User){
            $this->flash['error'] = $this->t('User not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
    }

    function add()
    {
        !empty($this->params['id']) ? $this->redirectTo(array('action' => 'add', 'id' => NULL)) : null;
        $this->_loadCurrentUserRoles();
        $this->_addOrEdit();
    }

    function edit()
    {
        if (empty($this->params['id']) || empty($this->User->id)){
            $this->flash['error'] = $this->t('Invalid user or not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
        $this->User->role->load();
        $this->_loadCurrentUserRoles();
        if(empty($this->params['user']['password'])){
            unset($this->params['user']['password']);
        }
        $this->_addOrEdit();
    }

    function destroy()
    {
        if (empty($this->params['id']) || empty($this->User->id)){
            $this->flash['notice'] = $this->t('Invalid user or not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
        $this->_protectUserFromBeingModified();
        if ($this->Request->isPost()){
            if($this->User->getId() == $this->CurrentUser->getId()){
                $this->flash['error'] = $this->t('You can\'t delete your own account.');
            }else{
                if($this->User->destroy()){
                    $this->flash['success'] = $this->t('User was successfully deleted.');
                }else{
                    $this->flash['error'] = $this->t('There was a problem while deleting the user.');
                }
            }
            $this->redirectTo(array('action' => 'listing'));
        }
    }

    function _addOrEdit()
    {
        $this->_protectUserFromBeingModified();
        if ($this->Request->isPost() && !empty($this->params['user'])){
            $this->User->setAttributes($this->params['user']);
            empty($this->params['roles']) ? $this->User->addError('Role', Ak::t('Please select at least one role for this user.')) : null;

            if($this->User->save()){

                if(User::can('Set roles', 'Admin::Users')){
                    $posted_roles = array_diff($this->params['roles'], array(0));
                    if(!empty($posted_roles)){
                        $role_ids = array_intersect(array_keys($posted_roles),
                        array_keys($this->User->collect($this->Roles,'id','id')));
                        $User =& $this->User->find($this->User->id, array('include'=>'roles'));
                        $User->role->setByIds($role_ids);
                    }
                }

                $this->flash_options = array('seconds_to_close' => 10);
                $this->flash['success'] = $this->t('User was successfully '.($this->getActionName()=='add'?'created':'updated'));
                $this->redirectTo(empty($this->params['continue_editing']) ?
                array('action' => 'show', 'id' => $this->User->getId()) : array('action' => 'edit', 'id' => $this->User->getId()));
            }
        }
    }

    function _protectUserFromBeingModified()
    {
        $self_editing = $this->User->getId() == $this->CurrentUser->getId();
        if($this->User->isNewRecord()){
            return ;
        }elseif(!User::can('Set roles', 'Admin::Users') && $this->User->hasRootPrivileges() && !$self_editing){
            $this->flash['error'] = $this->t('You don\'t have the privileges to modify selected user.');
            $this->redirectToAction('listing');
        }elseif (!$self_editing && !User::can('Edit other users', 'Admin::Users')){
            $this->flash['error'] = $this->t('You can\' modify other users account.');
            $this->redirectToAction('listing');
        }
    }
}

?>