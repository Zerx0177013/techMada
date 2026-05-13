<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('login', 'Home::login');
$routes->get('employe', 'Home::employeDashboard');
$routes->get('employe/create', 'Home::employeCreate');
$routes->get('employe/conges', 'Home::employeIndex');
$routes->get('rh', 'Home::rhIndex');
$routes->get('admin', 'Home::adminDashboard');
$routes->get('admin/employes', 'Home::adminEmployes');
