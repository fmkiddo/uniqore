<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get ('/admin', 'APIHome::index');
$routes->match (['get', 'post'], '/uniqore/forge/(:any)', 'UniqoreForger::index/$1');
$routes->match (['get', 'post'], '/uniqore/session/(:any)', 'APISessionControl::index/$1');