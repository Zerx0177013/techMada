<?php

namespace App\Controllers;

use App\Models\User;

class AuthController extends BaseController
{
    public function showLoginForm()
    {
        return view('auth/login');
    }
    // public function login()
    // {
    //     $model = new User();
    //     $email = $this->request->getPost('email');
    //     $password = $this->request->getPost('password');
    //     $user = $model->where('email', $email)->first();
    //     if (!$user || $password !== $user['password']) {
    //         return view('auth/login', [
    //             'erreur' => 'Email ou mot de passe incorrect'
    //         ]);
    //     }
    //     // Stocker uniquement les données non sensibles en session
    //     session()->set('user', [
    //         'id'    => $user['id'],
    //         'nom'   => $user['nom'],
    //         'email' => $user['email'],
    //         'role'  => $user['role'] // 'admin', 'bibliothecaire' ou 'user'
    //     ]);
    //     return redirect()->to('/listeLivre');
    // }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
