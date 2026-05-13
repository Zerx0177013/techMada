<?php

namespace App\Controllers;

use App\Models\Employes;

class AuthController extends BaseController
{
    public function showLoginForm()
    {
        return view('auth/login');
    }
    public function login()
    {
        $model = new Employes();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user = $model->where('email', $email)->first();
        if (!$user || !password_verify($password, $user['password'])) {
            return view('auth/login', [
                'erreur' => 'Email ou mot de passe incorrect'
            ]);
        }
        session()->set('user', [
            'id'    => $user['id'],
            'nom'   => $user['nom'],
            'prenom'   => $user['nom'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'DepartementId' => $user['DepartementId'],
            'actif' => $user['actif']
        ]);
        return redirect()->to('/dashboard');
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
