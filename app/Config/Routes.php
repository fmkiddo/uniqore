<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get ('/', 'APIHome::welcome');

$routes->group ('uniqore', static function ($routes) {
    $routes->get ('generate-key', 'UniqoreForger::sodiumKey');
    $routes->get ('fortknox-password', 'UniqoreForger::passwordRandomize');
    $routes->match (['get', 'post'], 'admin', 'APIHome::index');
    $routes->match (['get', 'post'], 'admin/dashboard', 'APIDashboard::index');
    $routes->match (['get', 'post'], 'admin/dashboard/validate', 'APIDashboard::formValidator');
    $routes->match (['get', 'post'], 'validator', 'LicenseController::index', ['namespace' => 'App\Controllers\Uniqore']);
    $routes->match (['get', 'post'], 'generator', 'APIFetcher::dataGenerator');
    $routes->match (['get', 'post'], 'fetch-data', 'APIFetcher::index');
    $routes->match (['get', 'post'], 'forge/(:any)', 'UniqoreForger::index/$1');
});
    
$routes->group ('controls', static function ($routes) {
    $routes->resource ('programming', ['namespace' => 'App\Controllers\Uniqore']);
    $routes->resource ('apiuser', ['namespace' => 'App\Controllers\Uniqore']);
    $routes->resource ('apiuserprofile', ['namespace' => 'App\Controllers\Uniqore']);
    $routes->resource ('apiuserconfig', ['namespace' => 'App\Controllers\Uniqore']);
    $routes->resource ('users', ['namespace' => 'App\Controllers\Uniqore']);
});

$routes->group ('osam', static function ($routes) {
    $routes->resource ('users', ['namespace' => 'App\Controllers\Osam']);
    $routes->resource ('controller', ['namespace' => 'App\Controllers\Osam', 'controller' => 'SystemGroup']);
    $routes->resource ('acl', ['namespace' => 'App\Controllers\Osam', 'controller' => 'AccessControl']);
    $routes->resource ('user-profile', ['namespace' => 'App\Controllers\Osam', 'controller' => 'UserProfile']);
});