<?php

namespace Project\Classes;

/**
 * Page class is responsible for dynamically generating a page.
 * The simplest way to use is:
 *   $view = new View();
 *   $view->render('/path/to/targetFile');
 * Need to use absolute path, and do not add .php after it.
 * Only support php file for now.
 * This way, it assumes css, js, and title have the same name.
 *
 * It is also possible to pass in array of options.
 *
 *
 * @Author Yi Zhao
 */

class View {
  private $title = '';
  private $header = '';
  private $footer = '';
  private $content = [];
  private $css = [];
  private $js = [];

  private $HTML = '
    <html>
      <head>
       <title>%title%</title>
       %css%
      </head>
      <body>
        %content%
        %js%
      </body>
    </html>
  ';

  public function __construct(array $options = []) {
    $this->set($options);
  }

  public function set(array $options) {
    $this->title = isset($options['title']) ? $options['title'] : '';
    $this->header = isset($options['header']) ? $this->getRoot() . $options['header'] : '';
    $this->footer = isset($options['footer']) ? $this->getRoot() . $options['footer'] : '';
    isset($options['content']) ? $this->content[] = $options['content'] : [];
    isset($options['css']) ? $this->css[] = $options['css'] : [];
    isset($options['js']) ? $this->js[] = $options['js'] : [];
  }

  // Includes predefined contents to current page, also add css and js files.
  public function render($fileName = null, $title = null) {
    if ($fileName) $this->quickSet($fileName);
    if ($title) $this->title = $title;
//    $page = "
//    <html>
//    <head>
//     <title>$this->title</title>"
//      . $this->addCSS() .
//    "</head>
//    <body>" .
//    $this->addBody() .
//    "</body>
//    </html>";
//
//    echo $page;
    echo str_replace(
      ['%title%', '%css%', '%content%', '%js%'],
      [$this->title, $this->addCSS(), $this->addBody(), $this->addJS()],
      $this->HTML);
  }
  // Renders plain text
  public function json($variable) {
    header('Content-Type: application/json');
    echo json_encode($variable);
  }

  public function text($text) {
    echo $text;
  }

  // Private functions
  private function getRoot() {
    return $_SERVER['DOCUMENT_ROOT'];
  }
  private function addCSS() {
    $output = '';
    foreach ($this->css as $css) {
      $output .= "<link rel='stylesheet' href='$css' />";
    }
    return $output;
  }
  private function addJS() {
    $output = '';
    foreach ($this->js as $js) {
      $output .= "<script src='$js'></script>";
    }
    return $output;
  }
  private function addBody() {
    ob_start();
    include $this->header;
    foreach ($this->content as $content) {
      include $content;
    }
    include $this->footer;
    return ob_get_clean();
  }
  // Provides an easier way to setup a page.
  // Assumes php, js, and css files have the same name.
  private function quickSet($fileName) {
    $root = $this->getRoot();
    $pattern = '/\/\w+$/';
    preg_match($pattern, $fileName, $name);
    $name = !empty($name) ? substr($name[0], 1) : $fileName;
    !empty($this->title) || $this->title = $name;
    !empty($this->header) || $this->header = $root . preg_filter($pattern, '/header.php', $fileName);
    !empty($this->footer) || $this->footer = $root . preg_filter($pattern, '/footer.php', $fileName);
    $this->content[] = $root . $fileName . '.php';
    $this->js[] = '/js/' . $name . '.js';
    $this->css[] = '/css/' . $name . '.css';
  }


}