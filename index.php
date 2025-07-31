<?php


if (isset($_GET['action']) && $_GET['action'] !== '') {

    switch ($_GET['action']) {
        case 'Acceuil':
                require_once('templates/homepage.php');

            break;
        default:
            header('Location: 404.php');
            exit;
    }
} else {
 require_once('templates/homepage.php');
    exit;
}