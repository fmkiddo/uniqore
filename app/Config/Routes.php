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
    $routes->match (['get', 'post'], 'fetch-data', 'APIFetcher::index');
    $routes->match (['get', 'post'], 'forge/(:any)', 'UniqoreForger::index/$1');
});
    
$routes->group ('api-uniqore', static function ($routes) {
    $routes->resource ('users', ['namespace' => 'App\Controllers\Uniqore']);
});
