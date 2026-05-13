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
$routes->get('employe/create', 'Home::employeCreate', ['filter' => 'auth']);
$routes->get('employe/conges', 'Home::employeIndex', ['filter' => 'auth']);
$routes->get('rh', 'Home::rhIndex', ['filter' => ['auth', 'role:rh,admin']]);
$routes->get('admin', 'Home::adminDashboard', ['filter' => ['auth', 'role:admin']]);
$routes->get('admin/employes', 'Home::adminEmployes', ['filter' => ['auth', 'role:admin']]);
