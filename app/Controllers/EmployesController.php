<?php

namespace App\Controllers;

use App\Models\Employes;
use App\Models\Conges;
use App\Models\Soldes;



class EmployesController extends BaseController
{
    public function dashboard(){
        $model = new Employes();
        $userId = session()->get('user')['id'];
        $employe = $model->getInformation($userId);

        $congeModel = new Conges();
        $conges = $congeModel->select('conges.*, typesConge.libelle as type_libelle')
                             ->where('EmployeId', $userId)
                             ->join('typesConge', 'conges.TypeCongeId = typesConge.id')
                             ->orderBy('conges.createdAt', 'DESC') 
                             ->findAll();

        $employe['demandeCongesEnAttente'] = array_filter($conges, function($conge) {
            return $conge['statut'] === 'enAttente';
        });

        $employe['demandeCongesApprouvees'] = array_filter($conges, function($conge) {
            return $conge['statut'] === 'approuvee';
        });

        $employe['demandeCongesRefusees'] = array_filter($conges, function($conge) {
            return $conge['statut'] === 'refusee';
        });

        $employe['demandeCongesAnnulees'] = array_filter($conges, function($conge) {
            return $conge['statut'] === 'annulee';
        });

        $soldesModel = new Soldes();
        $soldes = $soldesModel->select('soldes.*, typesConge.libelle as type_libelle, typesConge.joursAnnuels')
                              ->where('EmployeId', $userId)
                              ->join('typesConge', 'soldes.TypeCongeId = typesConge.id')
                              ->findAll();

        $employe['soldeAnnuel'] = array_filter($soldes, function($solde) {
            return $solde['TypeCongeId'] == 1; // 1 = Annuel
        });

        $employe['soldeMaladie'] = array_filter($soldes, function($solde) {
            return $solde['TypeCongeId'] == 2; // 2 = Maladie
        });

        $employe['soldeSpecial'] = array_filter($soldes, function($solde) {
            return $solde['TypeCongeId'] == 3; // 3 = Spécial
        });

        $employe['soldes'] = $soldes;
        
        return view('employe/dashboard', ['employe' => $employe]);
    }

    public function create()
    {
        $userId = session()->get('user')['id'];
        $model = new Employes();
        $employe = $model->getInformation($userId);

        $typesCongeModel = new \App\Models\TypesConge();
        $typesConges = $typesCongeModel->findAll();

        $soldesModel = new Soldes();
        // Pour l'année en cours
        $annee = date('Y');
        $soldes = $soldesModel->select('soldes.*, typesConge.libelle as type_libelle')
                              ->where('EmployeId', $userId)
                              ->where('annee', $annee)
                              ->join('typesConge', 'soldes.TypeCongeId = typesConge.id')
                              ->findAll();

        $soldesParType = [];
        foreach ($soldes as $s) {
            $soldesParType[$s['TypeCongeId']] = $s;
        }

        return view('employe/create', [
            'employe' => $employe,
            'typesConges' => $typesConges,
            'soldesParType' => $soldesParType
        ]);
    }

    public function store()
    {
        $userId = session()->get('user')['id'];
        
        $rules = [
            'type_conge' => 'required|integer',
            'date_debut' => 'required|valid_date',
            'date_fin'   => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $typeCongeId = $this->request->getPost('type_conge');
        $dateDebut = $this->request->getPost('date_debut');
        $dateFin = $this->request->getPost('date_fin');
        $motif = $this->request->getPost('motif');

        $start = strtotime($dateDebut);
        $end = strtotime($dateFin);
        
        if ($end < $start) {
            return redirect()->back()->withInput()->with('error', 'La date de fin doit être après ou égale à la date de début.');
        }

        $nbJours = round(($end - $start) / 86400) + 1;

        $soldesModel = new Soldes();
        $solde = $soldesModel->where('EmployeId', $userId)
                             ->where('TypeCongeId', $typeCongeId)
                             ->where('annee', date('Y'))
                             ->first();
                             
        $typesCongeModel = new \App\Models\TypesConge();
        $typeConge = $typesCongeModel->find($typeCongeId);

        if ($typeConge && $typeConge['deductible'] == 1) {
            if (!$solde || ($solde['joursAttribues'] - $solde['joursPris']) < $nbJours) {
                return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour cette demande.');
            }
        }

        $congeModel = new Conges();
        $congeModel->insert([
            'EmployeId'   => $userId,
            'TypeCongeId' => $typeCongeId,
            'dateDebut'   => $dateDebut,
            'dateFin'     => $dateFin,
            'nbJours'     => $nbJours,
            'motif'       => $motif,
            'statut'      => 'enAttente'
        ]);

        return redirect()->to('/employe')->with('success', 'Votre demande de congé a bien été soumise. Elle est en attente de validation.');
    }

    public function conges()
    {
        $userId = session()->get('user')['id'];
        $model = new Employes();
        $employe = $model->getInformation($userId);

        $congeModel = new Conges();
        $builder = $congeModel->select('conges.*, typesConge.libelle as type_libelle')
                              ->where('EmployeId', $userId)
                              ->join('typesConge', 'conges.TypeCongeId = typesConge.id')
                              ->orderBy('conges.createdAt', 'DESC');

        $statut = $this->request->getGet('statut');
        if ($statut && $statut !== 'Tous les statuts' && $statut !== '') {
            $builder->where('statut', $statut);
        }

        $congesResult = $builder->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['conges' => $congesResult]);
        }

        return view('employe/index', [
            'employe' => $employe,
            'conges' => $congesResult
        ]);
    }

    public function cancelConge($congeId)
    {
        $userId = session()->get('user')['id'];
        $congeModel = new Conges();
        
        $conge = $congeModel->where('id', $congeId)
                            ->where('EmployeId', $userId)
                            ->first();

        if ($conge && $conge['statut'] === 'enAttente') {
            $congeModel->update($congeId, ['statut' => 'annulee', 'commentaireRh' => 'Annulé par l\'employé']);
            return redirect()->back()->with('success', 'La demande a été annulée avec succès.');
        }

        return redirect()->back()->with('error', 'Impossible d\'annuler cette demande.');
    }
}
