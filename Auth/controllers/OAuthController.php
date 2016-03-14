<?php

namespace Project\Auth\controllers;

use Project\Auth\models\GitHub;
use Project\Auth\models\OAuthModel;
use Project\Classes\Config\OAuthInfo;
use Project\Classes\Controller;
use Project\Classes\View;

class OAuthController extends Controller{

  public function __construct() {
    $this->view = new View();
    $this->model = new OAuthModel();
  }

  public function github() {
    $gitHub = new GitHub(OAuthInfo::GITHUB);

    if($gitHub->connect()) {
      $this->model->logIn('github', $gitHub->getUser(), $gitHub->getEmail());
    }
    View::previousPage();
  }
}