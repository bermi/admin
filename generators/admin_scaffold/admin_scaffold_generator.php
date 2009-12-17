<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// +----------------------------------------------------------------------+
// | Akelos Framework - http://www.akelos.org                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2002-2007, Akelos Media, S.L.  & Bermi Ferrer Martinez |
// | Released under the GNU Lesser General Public License, see LICENSE.txt|
// +----------------------------------------------------------------------+

/**
 * @package ActiveSupport
 * @subpackage Generators
 * @author Bermi Ferrer <bermi a.t akelos c.om>
 * @copyright Copyright (c) 2002-2006, Akelos Media, S.L. http://www.akelos.org
 * @license GNU Lesser General Public License <http://www.gnu.org/copyleft/lesser.html>
 */

define('AK_ADMIN_PLUGIN_GENERATORS_DIR', AK_PLUGINS_DIR.DS.'admin'.DS.'generators');

require_once(AK_GENERATORS_DIR.DS.'scaffold'.DS.'scaffold_generator.php');

class AdminScaffoldGenerator extends  ScaffoldGenerator
{
    var $sintags = true;
    //var $module_preffix = 'admin';
    var $generators_dir = AK_ADMIN_PLUGIN_GENERATORS_DIR;

    function cast()
    {
        $this->controller_name = 'Admin::'.(empty($this->controller_name) ? $this->model_name : (AkInflector::camelize($this->controller_name)));
        return parent::cast();
    }
}



?>
