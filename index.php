<?php

use App\Exceptions\AException;

session_start();

try {
    include('app/app_loader.php');
} catch(AException $e) {
    echo('<b>Exception: </b>' . $e->getMessage() . '<br><b>Stack trace: </b>' . $e->getTraceAsString());
    exit;
}

if(!isset($_SESSION['id_current_user'])) {
    if(!str_contains($_GET['page'], 'AdminModule:Login')) {
        $app->redirect('AdminModule:Login:form');
    }
} else {
    $user = $app->userRepository->getUserById($_SESSION['id_current_user']);
    $app->setCurrentUser($user);
}

if(isset($_GET['page'])) {
    $page = htmlspecialchars($_GET['page']);

    $app->currentUrl = $page;
} else {
    $app->redirect('AdminModule:Login:checkLogin');
}

try {
    $app->loadPages();
    $app->renderPage();
} catch(Exception $e) {
    echo('<b>Exception: </b>' . $e->getMessage() . '<br><b>Stack trace: </b>' . $e->getTraceAsString());
    exit;
}

$title = 'TMS | ' . $app->currentPresenter->getTitle();

?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/bootstrap.css">
    </head>
    <body>
        <script type="text/javascript" src="js/jquery-3.7.1.js"></script>
        <script type="text/javascript" src="js/Application.js"></script>
        <!--<div id="cover">
            <img style="position: fixed; top: 50%; left: 49%;" src='img/loading.gif' width='32' height='32'>
        </div>-->
        <?php

        $app->showPage();

        ?>
    </body>
</html>