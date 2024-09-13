<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->match (['get', 'post'], '/admin', 'APIHome::index');
$routes->match (['get', 'post'], '/uniqore/forge/(:any)', 'UniqoreForger::index/$1');

$routes->group ('api-uniqore', static function ($routes) {
    $routes->resource ('users', ['namespace' => 'App\Controllers\Uniqore']);
});
