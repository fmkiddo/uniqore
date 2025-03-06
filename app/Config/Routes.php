<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get ('/', 'APIHome::welcome');
$routes->get ('/test-constraint', 'APIHome::test');

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
    $routes->resource ('user-pics', ['namespace' => 'App\Controllers\Osam', 'controller' => 'UserProfileImage']);
    $routes->resource ('user-allocations', ['namespace' => 'App\Controllers\Osam', 'controller' => 'UserAllocations']);
    $routes->resource ('user-locations', ['namespace' => 'App\Controllers\Osam', 'controller' => 'UserLocations']);
    $routes->resource ('config-attributes', ['namespace' => 'App\Controllers\Osam', 'controller' => 'Attributes']);
    $routes->resource ('attr-pre-list', ['namespace' => 'App\Controllers\Osam', 'controller' => 'PredefinedList']);
    $routes->resource ('config-items', ['namespace' => 'App\Controllers\Osam', 'controller' => 'ConfigurationItems']);
    $routes->resource ('ci-attributes', ['namespace' => 'App\Controllers\Osam', 'controller' => 'ConfigItemAttributes']);
    $routes->resource ('fixed-assets', ['namespace' => 'App\Controllers\Osam', 'controller' => 'Assets']);
    $routes->resource ('fa-attributes', ['namespace' => 'App\Controllers\Osam', 'controller' => 'AssetConfigurations']);
    $routes->resource ('locations', ['namespace' => 'App\Controllers\Osam', 'controller' => 'Locations']);
    $routes->resource ('sublocations', ['namespace' => 'App\Controllers\Osam', 'controller' => 'Sublocations']);
    $routes->resource ('fa-request-sum', ['namespace' => 'App\Controllers\Osam', 'controller' => 'AssetRequestSummaries']);
    $routes->resource ('fa-procure', ['namespace' => 'App\Controllers\Osam', 'controller' => 'Procurements']);
    $routes->resource ('fa-tsout', ['namespace' => 'App\Controllers\Osam', 'controller' => 'TransferOut']);
    $routes->resource ('fa-tsout-item', ['namespace' => 'App\Controllers\Osam', 'controller' => 'TransferOutItem']);
});