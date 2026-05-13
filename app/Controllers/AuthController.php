<?php

namespace App\Controllers;

use App\Models\Employes;

class AuthController extends BaseController
{
    public function showLoginForm()
    {
        if (session()->get('user')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $model = new Employes();
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        if ($email === '' || $password === '') {
            return redirect()->back()->withInput()->with('erreur', 'Veuillez renseigner email et mot de passe.');
        }

        $user = $model->where('email', $email)->first();
        if (!$user || empty($user['password']) || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('erreur', 'Email ou mot de passe incorrect.');
        }

        if (array_key_exists('actif', $user) && (int) $user['actif'] !== 1) {
            return redirect()->back()->withInput()->with('erreur', 'Compte inactif.');
        }

        session()->set('user', [
            'id'    => $user['id'],
            'nom'   => $user['nom'],
            'prenom'   => $user['prenom'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'DepartementId' => $user['DepartementId'],
            'actif' => $user['actif']
        ]);
        return redirect()->to('/dashboard');
    }

    public function dashboard()
    {
        $user = session()->get('user');

        if (!$user) {
            return redirect()->to('/login');
        }

        $role = $user['role'] ?? null;
        if ($role === 'admin') {
            return redirect()->to('/admin');
        }
        if ($role === 'rh') {
            return redirect()->to('/rh');
        }
        return redirect()->to('/employe');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
