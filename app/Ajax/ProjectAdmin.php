<?php

use App\Components\Grids\ProjectGridFactory;

require_once('Ajax.php');
require_once('AjaxCommonMethods.php');

$action = null;

if(isset($_GET['action'])) {
    $action = get('action');
} else if(isset($_POST['action'])) {
    $action = post('action');
}

if($action === NULL) {
    throw new Exception('$action is null');
}

try {
    echo($action());
} catch(Exception $e) {
    echo $e->getMessage();
    exit;
}

function ajaxProjectList() {
    global $db, $logger, $clientRepository, $projectRepository, $userRepository;

    $pgf = new ProjectGridFactory($db, $logger, $clientRepository, $projectRepository, $userRepository);

    $json = json_encode(['table' => $pgf->createComponent(), 'controls' => $pgf->createGridControls()]);

    return $json;
}

exit;

?>