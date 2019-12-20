<?php
// Inicia session
session_start();
// Requiere functions.php
require 'function/functions.php';

switch ($_GET) {
    case isset($_GET['action']):
        $title = ucfirst($_GET['action']);
        break;

    case isset($_GET['view']):
        $title = ucfirst($_GET['view']);
        break;

    case isset($_GET['admin']):
        $title = ucfirst($_GET['admin']);
        break;
    
    default:
        $title = 'Home';
        break;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Old School Store - <?= $title ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css" integrity="sha256-46qynGAkLSFpVbEBog43gvNhfrOj+BmwXdxFgVK/Kvc=" crossorigin="anonymous" />
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
</head>
<body>

<?php

    // Paginas Action:
    $action = array('contact','login','logout','register','cart-modify','favorites-modify','payment','shipping');
    // Paginas View:
    $view = array('403','404','product','detail','home','profile','cart','favorites','review-cart');
    // Paginas Admin:
    $admin = array('new-product','product-list','users-list','users-type');


    if (isset($_GET['action']) && $_GET['action'] != "") {

        if (in_array($_GET['action'],$action,true)) {
            require 'action/'. $_GET['action'] . '.php';
        } else {
            require 'view/404.php';
        }

    } elseif (isset($_GET['view']) && $_GET['view'] != "") {

        if (in_array($_GET['view'],$view,true)) {
            require 'view/'. $_GET['view'] . '.php';
        } else {
            require 'view/404.php';
        }

    } elseif (isset($_GET['admin']) && isset($_SESSION['usuario'])) {

        if ($_SESSION['usuario']['user_type'] == 0) {

            if (in_array($_GET['admin'],$admin,true)) {
                require 'admin/'. $_GET['admin'] . '.php';
            } else {
                require 'view/404.php';
            }
            
        } else {
            require 'view/403.php';
        }

    } else {
        require header('location: ?view=home');
    }
    
?>

    <!-- Scripts Java -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>

</body>
</html>