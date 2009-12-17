<?php


defined('AK_DEFAULT_USER_ROLE') ? null : define('AK_DEFAULT_USER_ROLE', 'Registered user');
defined('AK_DEFAULT_ADMIN_SETTINGS') ? null : define('AK_DEFAULT_ADMIN_SETTINGS', 'admin');

class User extends ActiveRecord
{
    var $habtm = array('roles' => array('unique'=>true, 'foreign_key'=>'user_id'));

    /**
     * @access private
     */
    var $__initial_attributes = array();
    var $__requires_password_confirmation = true;

    /**
     * We need to get initial values when instantiating to know if attributes like password have been changed
     */
    function __construct()
    {
        $attributes = (array)func_get_args();
        $this->__initial_attributes = isset($attributes[1]) && is_array($attributes[1]) ? $attributes[1] : array();
        return $this->init($attributes);
    }

    /**
     * Main authentication method
     * 
     * @param string $login user name or password
     * @param string $password
     * @return False if not found or not enabled, User instance if succedes
     */
    function authenticate($login, $password)
    {
        $UserInstance =& new User();

        $login_or_email = preg_match(AK_EMAIL_REGULAR_EXPRESSION, $login) ? 'email' : 'login';

        if($User =& $UserInstance->find('first', array('conditions'=>array($login_or_email.' = ? AND __owner.is_enabled = ? AND _roles.is_enabled = ?', $login, true, true), 'include'=>'role')) && $User->isValidPassword($password)){
            $User->set('last_login_at', Ak::getDate());
            $User->save();
            return $User;
        }
        return false;
    }

    function signUp($user_details, $options = array())
    {
        $user_details['is_enabled'] = true;
        $this->setAttributes($user_details);
        if($this->save()){
            $this->setDefaultRole();
            $this->sendSignupMessage(array(
            'login' => $user_details['login'],
            'password' => $user_details['password'],
            ));
            return true;
        }
        return false;
    }

    function setDefaultRole()
    {
        $settings = Ak::getSettings(AK_DEFAULT_ADMIN_SETTINGS);
        if(!empty($settings['account_settings']['default_role'])){
            $this->role->load();
            $Role = new Role();
            if($DefaultRole = $Role->findFirstBy('name', $settings['account_settings']['default_role'])){
                $this->role->set($DefaultRole);
            }
        }
    }

    function sendSignupMessage($options = array())
    {
        $default_options = array(
        'signup_message' => 'registration_details'
        );
        $options = array_merge($default_options, $options);
        if(!empty($options['signup_message'])){
            Ak::import_mailer('account_mailer');
            $Mailer =& new AccountMailer();
            $Mailer->_login = $options['login'];
            $Mailer->_password = $options['password'];
            $Mailer->deliver($options['signup_message'], $this->get('email'));
        }
    }


    // Validation
    // ---------------

    function validate()
    {
        $this->validatesUniquenessOf('email', array('message'=>$this->t('email %email already in use', array('%email'=>$this->get('email')))));
        $this->validatesUniquenessOf('login', array('message'=>$this->t('login %login already in use', array('%login'=>$this->get('login')))));
        $this->validatesPresenceOf(array('login','email'));
        $this->validatesFormatOf('email', AK_EMAIL_REGULAR_EXPRESSION, $this->t('Invalid email address'));
        $this->validatesLengthOf('login', array('in'=>array(3, 40), 'too_long' => $this->t('pick a shorter login'), 'too_short' => $this->t('pick a longer name')));
        $this->validatesLengthOf('password', array('in'=>array(4, 40), 'too_long' => $this->t('pick a shorter password'), 'too_short' => $this->t('pick a longer password')));
    }

    function validatesPassword()
    {
        $requires_password_confirmation = $this->hasAttributeBeenModified('password') ? $this->__requires_password_confirmation : false;
        $this->validatesPresenceOf($requires_password_confirmation ? array('password','password_confirmation') : array('password'));
        $requires_password_confirmation ? $this->validatesConfirmationOf('password', $this->t('Must match confirmation')) : null;
        return strlen($this->getErrorsOn('password').$this->getErrorsOn('password_confirmation')) == 0;
    }

    function needsPasswordLengthValidation()
    {
        return $this->isNewRecord() || !empty($this->password);
    }

    function needsEmailValidation()
    {
        return empty($this->_byspass_email_validation);
    }

    function validatesExistanceOfOriginalPasswordWhenUpdatingLogin()
    {
        if($this->hasAttributeBeenModified('login')){
            if(!$this->isValidPassword($this->get('password'), true, true)){
                $this->addError('login', $this->t('can\' be modified unless you provide a valid password.'));
            }else{
                $this->set('password_confirmation', $this->get('password'));
            }
        }
    }

    function isValidPassword($password, $hash_password = true, $hash_using_original_name = false)
    {
        return $this->getPreviousValueForAttribute('password') == ($hash_password ? $this->sha1($password, $hash_using_original_name) : $password);
    }


    // Triggers
    // ---------------

    function beforeCreate()
    {
        $this->validatesPassword();
        $this->encryptPassword();
        return !$this->hasErrors();
    }

    function beforeDestroy()
    {
        return !$this->hasRootPrivileges();
    }

    function beforeUpdate()
    {
        $this->validatesExistanceOfOriginalPasswordWhenUpdatingLogin();
        $this->validatesPassword();
        $this->_encryptPasswordUnlessEmptyOrUnchanged();
        return !$this->hasErrors();
    }

    function afterSave()
    {
        $this->__initial_attributes = $this->getAttributes();
        return true;
    }

    function afterCreate()
    {
        if(empty($this->roles)){
            $this->role->load();
            $Role =& new Role();
            if($Role =& $Role->findFirstBy('name', AK_DEFAULT_USER_ROLE)){
                $this->role->set($Role);
            }
        }
        return true;
    }



    // Enabling disabling accounts
    // --------------------------


    function enable()
    {
        $this->updateAttribute('is_enabled', true);
    }

    function disable()
    {
        $this->updateAttribute('is_enabled', false);
    }




    // Inspecting original values
    // --------------------------


    function hasAttributeBeenModified($attribute)
    {
        return $this->getPreviousValueForAttribute($attribute) != $this->get($attribute);
    }

    function getPreviousValueForAttribute($attribute)
    {
        return $this->hasColumn($attribute) && isset($this->__initial_attributes[$attribute]) ? $this->__initial_attributes[$attribute] : null;
    }


    // Hashing
    // -----------------------

    function encryptPassword()
    {
        $this->set('password', $this->sha1($this->get('password')));
    }

    function sha1($phrase, $use_original_login = false)
    {
        $login = $use_original_login ? $this->getPreviousValueForAttribute('login') : $this->get('login');
        empty($this->password_salt) ? $this->set('password_salt', Ak::randomString(16)) : null;
        return sha1($this->get('password_salt').$phrase.$login);
    }

    function isTokenValid($token)
    {
        return $this->getToken() == $token;
    }

    function _encryptPasswordUnlessEmptyOrUnchanged()
    {
        if($this->hasAttributeBeenModified('password') || $this->get('password') == ''){
            $this->encryptPassword();
        }else{
            $this->set('password', $this->getPreviousValueForAttribute('password'));
        }
    }




    // User::getToken(), User::loadFromToken()
    /**
     * Returns a one time use token for accesing an account.
     * 
     * This might be used for retrieving lost passwords.
     * 
     * Tokens can be validated using the Sentinel::isValidLoginTokenForUser method
     */
    function getToken($options = array())
    {
        $default_options = array(
        'id' => (int)$this->get('id'),
        'single_use' => !empty($options['single_use'])
        );
        $options = array_merge($default_options, $options);

        $options['expires'] = empty($options['expires']) ? 0 : Ak::getTimestamp()+((empty($options['expires']) ? '0' : ($options['expires'] === true ? 86400 : $options['expires'])));
        $options['single_use'] = $options['single_use'] ? 1 : 0;

        $options['hash'] = $this->_getTokenHash($options);

        return $this->_encodeToken($options);
    }

    function _getTokenHash($options)
    {
        return md5($this->get('id').
        $this->get('email').
        $this->get('login').
        $this->get('password').
        $this->get('password_salt').
        (!empty($options['single_use'])?$this->get('last_login_at'):'').
        $this->get('is_enabled').
        (isset($options['expires'])?$options['expires']:''));
    }

    /**
     * Given an array of options it will return an encrypted url string
     *
     * @param array $options token options
     * @return string Url ready authentication Token
     */
    function _encodeToken($options)
    {
        return base64_encode(Ak::blowfishEncrypt(Ak::toJson($options), Ak::getSetting(AK_DEFAULT_ADMIN_SETTINGS, 'token_key')));
    }

    /**
     * Decodes a token generated with encodeToken and returns an array of options
     * 
     * @param string $token token options
     * @param bool $url_decode should it URL decode the token true by default
     * @return array Array of options for the authentication token
     */
    function _decodeToken($token)
    {
        return (array)Ak::fromJson(Ak::blowfishDecrypt(base64_decode($token), Ak::getSetting(AK_DEFAULT_ADMIN_SETTINGS, 'token_key')));
    }


    // Permissions
    // ----------------------
    function &getPermissions()
    {
        $this->role->load();
        $Permissions = array();
        if(!empty($this->roles)){
            foreach (array_keys($this->roles) as $k){
                $Permissions = array_merge($Permissions, $this->roles[$k]->getPermissions());
            }
        }
        return $Permissions;
    }

    function can($task, $extension = null, $force_reload = false)
    {
        if(!isset($this->_activeRecordHasBeenInstantiated) || 
            !in_array('User', array($this->getModelName(), $this->getParentModelName()))){
            if (User::isLoaded()) {
                $User =& User::getCurrentUser();
                return $User->can($task, $extension, $force_reload);
            } else {
                return false;
            }
        }

        static $Permissions;
        if(!isset($Permissions) || $force_reload){
            $Permissions = array();
            $UserPermissions =& $this->getPermissions();
            foreach (array_keys($UserPermissions) as $k){
                $extension_id = $UserPermissions[$k]->get('extension_id');
                $Permissions[(empty($extension_id)?'core':$extension_id)][] = $UserPermissions[$k]->get('name');
            }
        }
        $extension_id = $this->_getExtensionId($extension);
        return (!empty($Permissions[$extension_id]) && in_array($task, $Permissions[$extension_id])) ? true : $this->_addRootPermission($task, $extension_id);
    }

    function hasRole($role_name, $force_reload = false)
    {
        if(!isset($this->_activeRecordHasBeenInstantiated)){
            $User =& User::getCurrentUser();
            return $User->hasRole($role_name, $force_reload);
        }
        $role_name = strtolower($role_name);
        $Roles =& $this->getRoles($force_reload);
        if(!empty($Roles)){
            foreach(array_keys($Roles) as $k){
                if(strtolower($Roles[$k]->get('name')) == $role_name){
                    return true;
                }
            }
        }
        return false;
    }

    function &getRoles($force_reload = false)
    {
        if((!isset($this->LoadedRoles) || $force_reload) && $this->role->load()){
            $this->LoadedRoles = array();
            foreach (array_keys($this->roles) as $k){
                $this->LoadedRoles[$this->roles[$k]->getId()] =& $this->roles[$k];
                foreach ($this->roles[$k]->nested_set->getFullSet() as $Role){
                    $this->LoadedRoles[$Role->getId()] = $Role;
                }
            }
        }
        if(isset($this->LoadedRoles)){
            return $this->LoadedRoles;
        }
        $result = array();
        return $result;
    }

    function hasRootPrivileges()
    {
        $this->role->load();
        return isset($this->roles[0]) ? $this->roles[0]->nested_set->isRoot() : false;
    }

    function _addRootPermission($task, $extension_id)
    {
        if($this->hasRootPrivileges()){
            $Permission =& new Permission();
            $Permission =& $Permission->findOrCreateBy('name AND extension_id', $task, $extension_id);
            $this->roles[0]->addPermission($Permission);
            return true;
        }
        return false;
    }

    function _getExtensionId($extension, $force_reload = false)
    {
        static $extenssion_ids = array();
        if(is_string($extension) && !is_numeric($extension)){
            if(isset($extenssion_ids[$extension]) && $force_reload == false){
                return $extenssion_ids[$extension];
            }
            $extension_key = $extension;
            Ak::import('Extension');
            $ExtensionInstance =& new Extension();
            $extension =& $ExtensionInstance->findOrCreateBy('name', $extension);
        }
        $extension = is_object($extension) ? $extension->getId() : (empty($extension)?'core':$extension);
        isset($extension_key) ? $extenssion_ids[$extension_key] = $extension : null;
        return $extension;
    }


    /**
     * Returns the current user if it is set, otherwise throws an error
     * 
     * @see isLoaded() to check before and not throw an error
     * @return User
     */
    function getCurrentUser()
    {
        $User =& Ak::getStaticVar('CurrentUser');
        if (empty($User)) {
            trigger_error(Ak::t('Current user has not been set yet.'), E_USER_ERROR);
        }
        return $User;
    }
    /**
     * Checks if the user is set
     *
     * @return boolean
     */
    function isLoaded()
    {
        return Ak::getStaticVar('CurrentUser') != null;
    }

    /**
     * Sets the current user
     *
     * @param User $CurrentUser
     */
    function setCurrentUser($CurrentUser)
    {
        Ak::_staticVar('CurrentUser', $CurrentUser);
    }


    function unsetCurrentUser()
    {
        User::setCurrentUser(null);
    }
}


?>
