<?php
namespace Project\Auth\controllers;

use Project\Auth\models\AuthModel;
use Project\Auth\models\GitHub;
use Project\Classes\Config\OAuthInfo;
use Project\Classes\Controller;
use Project\Classes\Helper;
use Project\Classes\View;
use Project\Validation\Validator;

/**
 * @Author Yi Zhao
 *
 */
class AuthController extends Controller {
  private $view;
  private $model;

  public function __construct() {
    $this->model = new AuthModel();
    $this->view = new View([
      'css' => '/Assets/css/bootstrap.min.css',
      'js' => [
        '/Assets/js/jquery.min.js',
        '/Assets/js/bootstrap.min.js',
        '/jspm_packages/system.js',
        '/config.js'
      ],
      'header' => '/Auth/Views/header.php',
      'footer' => '/Auth/Views/footer.php'
    ]);
    $this->view->setTemplate("
      <html>
      <head>
        <title>%title%</title>
        %css%
        %js%
      </head>
      <body>
        %header%
        %content%
        %footer%
      </body>
      </html>
    ");
  }

  // Login page GET
  public function loginPage() {
    if ($this->model->logInViaCookie()) {
      echo('logged in as ' . AuthModel::getUser());
    } else {
      $this->view->render('/Auth/Views/index', 'Login Page');
    }
  }

  // Login page POST
  public function processLogin() {

    $output = ['success' => false, 'error' => []];
    $loginResult = $this->model->logIn(Helper::getParam('username'), Helper::getParam('password'));
    if ($loginResult) {
      $output['success'] = true;
    } else {
      $output['error'][] = 'username or password is not correct';
    }
    $this->view->json($output);
  }

  // Logout GET
  public function logout() {
    $this->view->json($this->resultArray(AuthModel::getUser(), 'You have not logged in yet'));
    $this->model->logOut();
  }

  // Register GET
  public function registerPage() {
    $this->view->render('/Auth/Views/index', 'Register');
  }
  // Register/user POST
  public function checkAvailability() {
    $name = Helper::getParam('name');
    $value = Helper::getParam('value');
    $this->view->json(['available' => $this->model->checkAvailability($name, $value)]);
  }

  // Register POST
  public function registerUser() {
    $v = new Validator($_POST);
    $v->validate('username', ['notEmpty']);
//    $v->password()->validate('password', ['password']);
    $v->email()->validate('email', ['notEmpty', 'EmailValidator']);

    if ($v->isValid()) {
      $result = $this->model->newUser(Helper::getParam('username'), Helper::getParam('password'), Helper::getParam('email'));
      $this->view->json($this->resultArray($result['success'], $result['error']));
    } else {
      $this->view->json($this->resultArray(false, $v->getErrors()));
    }
  }

}