<?php

use App\Entities\UserEntity;
use App\UI\GridBuilder;
use App\UI\LinkBuilder;

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

function ajaxList() {
    global $userRepository;

    $page = get('page');

    $qb = $userRepository->composeQueryForGrid(__METHOD__);

    $offset = GRID_SIZE * $page;

    if($offset > 0) {
        $qb ->offset($page * GRID_SIZE);
    }
        
    $qb->execute();

    $users = [];
    while($row = $qb->fetchAssoc()) {
        $users[] = UserEntity::createUserEntityFromDbRow($row);
    }

    $gb = new GridBuilder();

    $gb->addColumns(['username' => 'Username', 'fullname' => 'Fullname', 'email' => 'Email']);
    $gb->addDataSource($users);
    $gb->addAction(function(UserEntity $user) {
        return LinkBuilder::createAdvLink(['page' => 'AdminModule:Users:profile', 'idUser' => $user->getId()], 'Profile');
    });
    $gb->addAction(function(UserEntity $user) {
        return LinkBuilder::createAdvLink(['page' => 'AdminModule:UserAdmin:form', 'idUser' => $user->getId()], 'Edit');
    });
    $gb->addAction(function(UserEntity $user) {
        return LinkBuilder::createAdvLink(['page' => 'AdminModule:UserAdmin:delete', 'idUser' => $user->getId()], 'Delete');
    });

    echo $gb->build();
}

exit;

?>