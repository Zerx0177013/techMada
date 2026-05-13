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
        
        return view('employes/dashboard', ['employe' => $employe]);
    }

    
}
