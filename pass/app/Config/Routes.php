<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/', 'Home::wrap');
$routes->get('unwrap/(:segment)', 'Unwrap::unwrap/$1');
$routes->get('secret/(:segment)', 'Unwrap::unwrap_secret/$1');
$routes->get('download/(:segment)', 'Download::show_download/$1');
$routes->post('download', 'Download::download');
$routes->post('thankyou', 'Download::thankyou');
$routes->get('token', 'Token::index');
$routes->post('token', 'Token::unwrap_token');
$routes->get('upload', 'File::index');
$routes->post('upload', 'File::upload');
