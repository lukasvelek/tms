<?php

use App\Helpers\FormDataHelper;

function get(string $key, bool $escape = true) {
    return FormDataHelper::get($key, $escape);
}

function post(string $key, bool $escape = true) {
    return FormDataHelper::post($key, $escape);
}

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

?>