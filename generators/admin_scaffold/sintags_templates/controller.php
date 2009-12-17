<?php echo '<?php'?>

<?php
$CamelCaseSingular = AkInflector::camelize($singular_name);
$CamelCasePlural = AkInflector::camelize($plural_name);
?>

class <?php echo "Admin_".AkInflector::camelize($controller_name)."Controller" ?> extends AdminController
{

    public $controller_information = '<?php echo AkInflector::titleize($singular_name)?> management area.';
    
<?php 
    echo "    var \$models = '$CamelCaseSingular';\n";
    echo "    var \$admin_menu_options = array('".AkInflector::titleize($controller_name)."'=>array('id'=>'".AkInflector::underscore($controller_name)."','url'=>array('controller'=>'".AkInflector::underscore($controller_name)."','action'=>'listing')));\n";
    echo "    var \$controller_menu_options = array(";
    
    foreach (array('listing','add') as $k){
        echo "'".AkInflector::humanize($k)."'=> array('id'=>'$k','url'=>array('controller'=>'".AkInflector::underscore($controller_name)."','action'=>'$k')),";
    }
    echo ");\n\n";
    
?>
   
    public function index() {
        $this->redirectToAction('listing');
    }

<?php  foreach((array)@$actions as $action) :?>
    public function <?php echo $action?>() {
    }

<?php  endforeach; ?>
    public function listing() {
        $this-><?php echo $singular_name?>_pages = $this->pagination_helper->getPaginator($this-><?php echo $model_name?>, array('items_per_page' => 10));
        $options = $this->pagination_helper->getFindOptions($this-><?php echo $model_name?>);
        $this-><?php echo $CamelCasePlural?> = $this-><?php echo $model_name?>->find('all', $options);
    }

    public function show() {
        $this->_find<?php echo $model_name?>OrRedirect();
    }

    public function add() {
        $this->_addOrEdit<?php echo $model_name?>('add');
    }

    public function edit() {
        $this->_find<?php echo $model_name?>OrRedirect();
        $this->_addOrEdit<?php echo $model_name?>('edit');
    }

    public function destroy() {
        $this->_find<?php echo $model_name?>OrRedirect();
        if($this->Request->isPost()){
            $this-><?php echo $CamelCaseSingular?>->destroy();
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('<?php echo AkInflector::titleize($singular_name)?> was successfully deleted.');
            $this->redirectToAction('listing');
        }
    }
    
    public function _find<?php echo $model_name?>OrRedirect() {
        if( empty($this->params['id']) || 
            !($this-><?php echo $CamelCaseSingular?> = $this-><?php echo $model_name?>->find(@$this->params['id']))){
            $this->flash['error'] = $this->t('<?php echo AkInflector::titleize($singular_name)?> not found.');
            $this->redirectToAction('listing');
        }
    }
        
    public function _addOrEdit<?php echo $model_name?>($add_or_edit) {
        $is_add = $add_or_edit != 'edit';
        if(!empty($this->params['<?php echo $CamelCaseSingular?>'])){
            if($is_add){
                $this-><?php echo $CamelCaseSingular?> = new $this-><?php echo $CamelCaseSingular?>();
            }
            $this-><?php echo $CamelCaseSingular?>->setAttributes($this->params['<?php echo $CamelCaseSingular?>']);
            if($this->Request->isPost() && $this-><?php echo $CamelCaseSingular?>->save()){
                $this->flash_options = array('seconds_to_close' => 10);
                $this->flash['notice'] = $this->t('<?php echo AkInflector::titleize($singular_name)?> was successfully '.($is_add?'created':'updated').'.');
                $this->redirectTo(array('action' => 'show', 'id' => $this-><?php echo $CamelCaseSingular?>->getId()));
            }
        }
    }
}

