<?php

namespace App\Controllers;

use App\Models\TypesConge;

class TypesCongeController extends BaseController
{
    public function index($id = null)
    {
        $typesModel = new TypesConge();
        $types = $typesModel->findAll();

        $typeToEdit = null;
        if ($id) {
            $typeToEdit = $typesModel->find($id);
        }

        return view('admin/types_conge', [
            'types'      => $types,
            'typeToEdit' => $typeToEdit
        ]);
    }

    public function store()
    {
        $typesModel = new TypesConge();
        
        $rules = [
            'libelle'      => 'required|min_length[2]',
            'joursAnnuels' => 'required|numeric',
            'deductible'   => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $typesModel->insert([
            'libelle'      => $this->request->getPost('libelle'),
            'joursAnnuels' => $this->request->getPost('joursAnnuels'),
            'deductible'   => $this->request->getPost('deductible')
        ]);

        return redirect()->to('admin/types-conge')->with('success', 'Type de congé créé avec succès.');
    }

    public function update($id)
    {
        $typesModel = new TypesConge();
        $type = $typesModel->find($id);

        if (!$type) {
            return redirect()->to('admin/types-conge')->with('error', 'Type de congé introuvable.');
        }

        $rules = [
            'libelle'      => 'required|min_length[2]',
            'joursAnnuels' => 'required|numeric',
            'deductible'   => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $typesModel->update($id, [
            'libelle'      => $this->request->getPost('libelle'),
            'joursAnnuels' => $this->request->getPost('joursAnnuels'),
            'deductible'   => $this->request->getPost('deductible')
        ]);

        return redirect()->to('admin/types-conge')->with('success', 'Type de congé mis à jour avec succès.');
    }

    public function delete($id)
    {
        $typesModel = new TypesConge();
        if ($typesModel->find($id)) {
            $typesModel->delete($id);
            return redirect()->back()->with('success', 'Type de congé supprimé avec succès.');
        }

        return redirect()->back()->with('error', 'Type de congé introuvable.');
    }
}
