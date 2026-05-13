<?php

namespace App\Controllers;

use App\Models\Employes;
use App\Models\Departements;
use App\Models\Soldes;
use App\Models\TypesConge;

class AdminController extends BaseController
{
    public function dashboard()
    {
        return view('admin/dashboard');
    }

    public function employes()
    {
        $employeModel = new Employes();
        $deptModel = new Departements();
        
        $employes = $employeModel->select('employes.*, departements.nom as dept_nom')
                                 ->join('departements', 'employes.DepartementId = departements.id', 'left')
                                 ->findAll();
                                 
        $departements = $deptModel->findAll();

        return view('admin/employes', [
            'employes'     => $employes,
            'departements' => $departements
        ]);
    }

    public function storeEmploye()
    {
        $rules = [
            'prenom' => 'required|min_length[2]',
            'nom' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[employes.email]',
            'password' => 'required|min_length[6]',
            'DepartementId' => 'required|integer',
            'role' => 'required|in_list[employe,rh,admin]',
            'dateEmbauche' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $employeModel = new Employes();
        
        $data = [
            'prenom' => $this->request->getPost('prenom'),
            'nom' => $this->request->getPost('nom'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'DepartementId' => $this->request->getPost('DepartementId'),
            'role' => $this->request->getPost('role'),
            'dateEmbauche' => $this->request->getPost('dateEmbauche'),
            'actif' => 1
        ];

        $employeModel->createEmploye($data);
        $newEmpId = $employeModel->getInsertID();

        $typesCongeModel = new TypesConge();
        $soldesModel = new Soldes();
        $types = $typesCongeModel->findAll();
        $annee = date('Y');

        foreach ($types as $type) {
            $soldesModel->insert([
                'EmployeId' => $newEmpId,
                'TypeCongeId' => $type['id'],
                'annee' => $annee,
                'joursAttribues' => $type['joursAnnuels'] ?? 0,
                'joursPris' => 0
            ]);
        }

        return redirect()->to('admin/employes')->with('success', 'Employé créé avec succès. Ses soldes de base ont été initialisés.');
    }
}
