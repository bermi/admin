# Akelos Admin Plugin

The Admin plugin assists you on creating a basic admin with [RBAC](http://csrc.nist.gov/rbac/) (permission, role and user management).

It is not meant to be used as a "fit all" solution but as a simple to adapt/modify base for your applications.

As Akelos itself the Admin plugin is based on conventions for building navigation and handling user permissions.

The user permission interface is heavily inspired by [Drupal](http://drupal.org/ "drupal.org | Community plumbing").


## Installation

    ./script/plugin install https://github.com/bermi/admin.git

You will be prompted for the URL path and master account details.

After installing you can visit http://yourhost.com/admin (by default admin)


## Admin preferences

Your admin preferences are located at:

    ./config/admin.yml


## Admin Scaffold generator

You can use the admin scaffold generator exactly like the scaffold generator.

    ./makelos generate admin_scaffold

It will generate controllers inside the the admin module, views that match the admin conventions, and helpers with permission check-points in order filter the links to show.


## RBAC (Role Based Access Control)

Keeping track of who can do what on your admin is plain simple. Permissions are grouped/scoped into Extensions for clarity.

You can restrict access to portions of your application using the

    if(User::can('Create project', 'Project administration')){
      // create project code
    }

This can be added to your models, helpers or controllers if the user has been authenticated.

In your views you can use

    <? if($admin_helper->can('View credit card number', 'Account management')) : ?>
      <p>_{Credit card number}: {card.number} </p>
    <? endif; ?>


The ideal scenario is to have an authenticated area under the admin module and unrestricted areas which do not require credentials under normal controllers.

By default actions on controllers inside the admin module are added to the "Permissions table". In order to disable this behaviour on your controller, just define the attribute

    public $protect_all_actions = false;

and select individual actions if any using

    public $protected_actions = 'index,show,edit,delete';

If you're logged as Root, new permissions found in your code will be added automatically to your permission pool. Just like with multilingual strings on Akelos.


## The menu system

In order to benefit from the menu building system and automated privileges your controllers in the admin module must extend AdminController, which is located at

    ./app/controllers/admin_controller.php

There are 2 different menus on the admin:

* An admin menu. Which affects the whole admin module.
* An controller menu. Which is dependent on each controller.

Menus are built by declaring in your controller the following attributes:

    class Admin_UsersController extends AdminController
    {
        // just for this controller
        public $controller_menu_options = array(
        'Accounts'   => array('id' => 'accounts', 'url'=>array('controller'=>'users', 'action'=>'listing')),
        'Roles'   => array('id' => 'roles', 'url'=>array('controller'=>'roles')),
        'Permissions'   => array('id' => 'permissions', 'url'=>array('controller'=>'permissions', 'action'=>'manage')),
        );
        
        // Which tab to select on the controller menu
        public $controller_selected_tab = 'Accounts';
    }

The code is quite straight forward.

By convention, the selected tab will be the one that matches the array key with current controller name. In this case we manually set it to Accounts.

By default strings on the menu system are internationalized.

You could also have set **var $admin_menu_options = array(....);** which will sum/override the options inherited from the AdminController.

To completely override the admin menu you must use **var $_admin_menu_options = array(....);**



## The User Model

The admin provides a "basic" user model. It's quite limited on purpose, so you can evolve the basic model to suit your needs.


## Future

Not a full featured automated admin. You'll have to custom code your intranets, but this might speed up the process.

See TODO file to know what will be implemented into future versions of this plugin.

