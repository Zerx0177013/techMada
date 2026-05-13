<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('auth/login');
    }

    public function login(): string
    {
        return view('auth/login');
    }

    public function employeDashboard(): string
    {
        return view('employe/dashboard');
    }

    public function employeCreate(): string
    {
        return view('employe/create');
    }

    public function employeIndex(): string
    {
        return view('employe/index');
    }

    public function rhIndex(): string
    {
        return view('rh/index');
    }

    public function adminDashboard(): string
    {
        return view('admin/dashboard');
    }

    public function adminEmployes(): string
    {
        return view('admin/employes');
    }
}
