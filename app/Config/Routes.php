<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::showLoginForm');
$routes->get('login', 'AuthController::showLoginForm');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->get('dashboard', 'EmployesController::dashboard',['filter' => 'auth']);
$routes->get('employe', 'EmployesController::dashboard', ['filter' => 'auth']);
$routes->get('employe/create', 'EmployesController::create', ['filter' => 'auth']);
$routes->post('employe/store', 'EmployesController::store', ['filter' => 'auth']);
$routes->get('employe/conges', 'EmployesController::conges', ['filter' => 'auth']);
$routes->post('employe/conges/cancel/(:num)', 'EmployesController::cancelConge/$1', ['filter' => 'auth']);
$routes->get('rh', 'Home::rhIndex', ['filter' => ['auth', 'role:rh,admin']]);
$routes->get('admin', 'Home::adminDashboard', ['filter' => ['auth', 'role:admin']]);
$routes->get('admin/employes', 'Home::adminEmployes', ['filter' => ['auth', 'role:admin']]);
