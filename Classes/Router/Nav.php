<?php
/**
 * @Author Yi Zhao
 *
 */

namespace Project\Classes\Router;


use Project\Classes\Helper;

/**
 * Class Nav
 *
 * It combines routing and creating menu together,
 * and it is possible to only display menus and do not use the router.
 * There must be at least one Navigation group exist,
 * so group method should be called before any other methods.
 *
 *
 * Example:
 *  Nav::group('/ as Home', function() {
 *    Nav::get('news as News', function() {
 *      // do something coming to this link
 *    })
 *    Nav::post('news as News', function() {
 *      // do something coming to this link
 *    })
 *  })
 *
 *
 * @package Project\Classes\Router
 * @method static void get($pathAsName, $callback)
 * @method static void post($pathAsName, $callback)
 */
class Nav {
  private static $base;
  private static $name;
  private static $callbacks = [];
  private static $routes = [];
  private static $menu;
  private static $hasMatch;

  public static function __callStatic($name, $arguments) {
    list($pathAsName, $callback) = $arguments;
    self::$routes[] = self::add($pathAsName, $callback, $name);
  }

  /**
   * Groups whatever inside together, this method should first, then add routes or groups inside.
   * @param string $baseAsName Base dir and name of this group, separated by 'as', for example, '/ as Home'
   * @param callable $callback Should be either routes of groups.
   */
  public static function group($baseAsName, $callback) {
    list($base, $name) = Helper::separateName($baseAsName);
    self::$base[] = $base;
    self::$name[] = $name;
    self::$callbacks[] = $callback;
  }

  /**
   * Starts routing. Calls 404 method when there is no match.
   */
  public static function start() {
    self::generateMenu();
    self::matchRoute(self::$menu);
    if(!self::$hasMatch) self::show404();
  }

  /**
   * Display a group in unordered list
   * @param string $menuName Name of the group, it only gets top level groups
   */
  public static function drawMenu($menuName) {
    function draw($menu) {
      echo "<ul>";
      foreach ($menu as $item) {
        if (array_key_exists('link', $item)) {
          echo "<li><a href='" . $item['link'] . "'>" . $item['name'] . "</a></li>";
        }else {
          draw($item);
        }
      }
      echo "</ul>";
    };
    foreach (self::getLink() as $item) {
      if($menuName == $item['name']) {
        draw($item['menu']);
        break;
      }
    }
  }
  ///////////////////////
  //  Private functions
  ///////////////////////
  /**
   * Calls itself recursively to match all the routes in the array passed in.
   * @param array $menu
   * @param string $base
   */
  private function matchRoute(array $menu, $base = '') {
    foreach ($menu as $item) {
      if(is_object($item) && $item->match($base)) {
        self::$hasMatch = true;
      }
      if(is_array($item) && array_key_exists('menu', $item)){
        self::matchRoute($item['menu'], $item['base']);
      }
    }
  }

  /**
   * Generates a menu for each top level group and adds them to self::menu array.
   * @throws \Exception
   */
  private function generateMenu() {
    foreach (self::$callbacks as $index => $callback) {
      self::$menu[$index] = [
          'base' => self::$base[$index],
          'name' => self::$name[$index],
          'callback' => self::$callbacks[$index],
      ];
      self::getMenu(self::$menu[$index]);
    }
//    var_dump('menu', self::$menu);
  }

  /**
   * Creates an array of groups and routes with only link and name,
   * so they can be easily drawn on the page.
   * @return array Menus with link and name for each group
   */
  private function getLink() {
    if (empty(self::$menu)) self::generateMenu();

    return array_map(function ($item) {
      $item['menu'] = self::makeLink($item);
      unset($item['base']);
      return $item;
    }, self::$menu);
  }
  /**
   * Generate the link and name for a single group, with GET method only
   * @param array $menu A group of routes
   * @return array  Ordered menu
   */
  private function makeLink(array $menu) {
    $baseDir = $menu['base'];
    return array_reduce($menu['menu'], function($acc, $currRoute) use($baseDir) {
      $r = [];
      if(is_array($currRoute)) $r = self::makeLink($currRoute);
      if(is_object($currRoute) && strcasecmp($currRoute->getProp('method'), 'get') == 0) {
        $r['link'] = $baseDir .  $currRoute->getProp('path');
        $r['name'] = $currRoute->getProp('name');
      }
      if(!empty($r)) $acc[] = $r;
      return $acc;
    }, []);
  }
  /**
   * Adds routes to groups and order them in correct order.
   * It starts with calling each callback in the callback array,
   * because in the beginning, each callback should be a independent group.
   * Then it compares new callback, name, and base array with the old ones,
   * the newly appeared ones should be the subgroup of the item that current callback
   * belongs to. It also moves all routes to the target array.
   * @param array $target
   * @throws \Exception
   */
  private function getMenu(array &$target) {
    if (!array_key_exists('callback', $target)) return ;
    // Clear routes
    self::$routes = [];
    // Save old value for comparison later.
    $oldCallbacks = self::$callbacks;
    $oldNames = self::$name;
    $oldBase = self::$base;
    // Exec callback.
    $target['callback']();
    // Unset callback property in array.
    unset($target['callback']);
    // Set menu items equal to newly generated routes.
    $target['menu'] = self::$routes;
    // Compare the difference in array to get value for any new groups.
    $diffCallbacks = array_diff_key(self::$callbacks, $oldCallbacks);
    $diffNames = array_diff_key(self::$name, $oldNames);
    $diffBase = array_diff_key(self::$base, $oldBase);
    // Something goes wrong if numbers do not match.
    if (count($diffCallbacks) != count($diffNames)) throw new \Exception('array length not equal');
    // For each new group, try to get menus, then add it to correct position.
    foreach ($diffNames as $key => $name) {
      $group = [
        'base' => $diffBase[$key],
        'name' => $diffNames[$key],
        'callback' => $diffCallbacks[$key],
      ];
      // See if there is any menus for this group
      self::getMenu($group);
      // Find parent for this group
      $parentIndex = self::findParent($target['menu'], $group['name']);
      // Add group back.
      $parentIndex ? array_splice($target['menu'], $parentIndex + 1, 0, [$group]) : $target['menu'][] = $group;
    }
  }

  // Find the index of any routes which has the same name
  private function findParent(array $possibleFather, $childName) {
    foreach ($possibleFather as $index => $p) {
      if (is_object($p) && $p->getProp('name') == $childName){
        return $index;
      }
    }
  }
  private static function show404(){
    http_response_code(404);
    echo '<h1 style="color: hotpink;">Sorry, page not found...</h1>';
  }
  private function add($pathAsName, $action, $method = 'GET') {
    list($path, $name) = Helper::separateName($pathAsName);
    return new Route($path, $name, $method, $action);
  }

}