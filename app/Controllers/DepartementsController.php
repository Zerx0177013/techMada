<?php

namespace App\Controllers;

use App\Models\Departements;

class DepartementsController extends BaseController
{
    public function index($id = null)
    {
        $deptModel = new Departements();
        $departements = $deptModel->findAll();

        $deptToEdit = null;
        if ($id) {
            $deptToEdit = $deptModel->find($id);
        }

        return view('admin/departements', [
            'departements' => $departements,
            'deptToEdit'   => $deptToEdit
        ]);
    }

    public function store()
    {
        $rules = [
            'nom' => 'required|min_length[2]',
            'description' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $deptModel = new Departements();
        
        $deptModel->insert([
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description')
        ]);

        return redirect()->to('admin/departements')->with('success', 'Département créé avec succès.');
    }

    public function update($id)
    {
        $deptModel = new Departements();
        $dept = $deptModel->find($id);

        if (!$dept) {
            return redirect()->to('admin/departements')->with('error', 'Département introuvable.');
        }

        $rules = [
            'nom' => 'required|min_length[2]',
            'description' => 'permit_empty|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $deptModel->update($id, [
            'nom' => $this->request->getPost('nom'),
            'description' => $this->request->getPost('description')
        ]);

        return redirect()->to('admin/departements')->with('success', 'Département mis à jour avec succès.');
    }

    public function delete($id)
    {
        $deptModel = new Departements();
        if ($deptModel->find($id)) {
            $deptModel->delete($id);
            return redirect()->back()->with('success', 'Département supprimé avec succès.');
        }

        return redirect()->back()->with('error', 'Département introuvable.');
    }
}
