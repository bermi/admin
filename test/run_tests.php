<?php

include_once dirname(__FILE__).'/plugins/admin/config.php';

$_model_files = glob(dirname(__FILE__).DS.'plugins'.DS.'admin'.DS.'cases'.DS.'*.php');
$_included_files = get_included_files();
if(count($_included_files) == count(array_diff($_included_files, $_model_files))){
    $Suite = new AkUnitTestSuite('Admin Plugin Tests');
    foreach ($_model_files as $file){
        $Suite->addFile($file);
    }
}

if(isset($argv[1]) && $argv[1] == '-ci'){
    exit ($Suite->run(new AkXUnitXmlReporter()));
}else{
    $Suite->run(new AkelosTextReporter());
}
