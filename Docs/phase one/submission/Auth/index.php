<?php

/**
 * @Author Yi Zhao
 *
 */
namespace Project\Auth;
use Project\Classes\Router\Nav;
use Project\Classes\Router\Router;
use Project\Classes\View;

include '../autoloader.php';

$auth = new AuthController();
Nav::group('/Auth as Auth', function () use($auth) {
  Nav::get('/ as Home', View::useContent('Views/header.php', 'Views/footer.php'));
  Nav::get('/register as Register', $auth->action('registerPage'));
  Nav::get('/login as Login', $auth->action('loginPage'));
  Nav::get('/logout as Logout', $auth->action('logout'));

  Nav::group('/Auth/register as Register', function() {
    Nav::get('/test as Test', function() {
      echo 'register';
    });
  });

  Nav::post('/login', $auth->action('processLogin'));
  Nav::post('/register', $auth->action('registerUser'));
});

Nav::start();
?>


