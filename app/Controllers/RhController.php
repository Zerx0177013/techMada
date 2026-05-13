<?php

namespace App\Controllers;

use App\Models\Conges;
use App\Models\Soldes;
use App\Models\TypesConge;
use CodeIgniter\Database\BaseConnection;

class RhController extends BaseController
{
    public function index()
    {
        $refuseId = $this->request->getGet('refuse');

        $congeModel = new Conges();
        $demandes = $congeModel
            ->select([
                'conges.*',
                'employes.nom AS emp_nom',
                'employes.prenom AS emp_prenom',
                'departements.nom AS dept_nom',
                'typesConge.libelle AS type_libelle',
                'typesConge.deductible AS type_deductible',
                'typesConge.joursAnnuels AS type_joursAnnuels',
                'traite.nom AS traite_nom',
                'traite.prenom AS traite_prenom',
            ])
            ->join('employes', 'conges.EmployeId = employes.id')
            ->join('departements', 'employes.DepartementId = departements.id', 'left')
            ->join('typesConge', 'conges.TypeCongeId = typesConge.id')
            ->join('employes AS traite', 'conges.TraitePar = traite.id', 'left')
            ->orderBy("CASE conges.statut WHEN 'enAttente' THEN 0 WHEN 'approuvee' THEN 1 WHEN 'refusee' THEN 2 WHEN 'annulee' THEN 3 ELSE 9 END", '', false)
            ->orderBy('conges.createdAt', 'DESC')
            ->findAll();

        $pendingCount = 0;
        $soldesModel = new Soldes();
        foreach ($demandes as &$demande) {
            $demande['initials'] = $this->initials((string) ($demande['emp_prenom'] ?? ''), (string) ($demande['emp_nom'] ?? ''));
            $demande['type_class'] = $this->typeBadgeClass((string) ($demande['type_libelle'] ?? ''));

            if (($demande['statut'] ?? null) === 'enAttente') {
                $pendingCount++;
            }

            $demande['solde_dispo'] = null;
            $demande['can_approve'] = true;

            $deductible = (int) ($demande['type_deductible'] ?? 0) === 1;
            if ($deductible) {
                $annee = (int) substr((string) ($demande['dateDebut'] ?? ''), 0, 4);
                $solde = $soldesModel
                    ->where('EmployeId', $demande['EmployeId'])
                    ->where('TypeCongeId', $demande['TypeCongeId'])
                    ->where('annee', $annee)
                    ->first();

                if ($solde) {
                    $dispo = (int) $solde['joursAttribues'] - (int) $solde['joursPris'];
                    $demande['solde_dispo'] = $dispo;
                    $demande['can_approve'] = $dispo >= (int) $demande['nbJours'];
                } else {
                    $attribues = (int) ($demande['type_joursAnnuels'] ?? 0);
                    $demande['solde_dispo'] = $attribues;
                    $demande['can_approve'] = $attribues >= (int) $demande['nbJours'];
                }
            } else {
                $demande['solde_dispo'] = null;
                $demande['can_approve'] = true;
            }
        }
        unset($demande);

        $refuseTarget = null;
        if ($refuseId !== null && ctype_digit((string) $refuseId)) {
            foreach ($demandes as $d) {
                if ((int) $d['id'] === (int) $refuseId && ($d['statut'] ?? null) === 'enAttente') {
                    $refuseTarget = $d;
                    break;
                }
            }
        }

        return view('rh/index', [
            'demandes' => $demandes,
            'pendingCount' => $pendingCount,
            'refuseTarget' => $refuseTarget,
        ]);
    }

    public function approve(int $congeId)
    {
        $user = session()->get('user');
        if (!$user) {
            return redirect()->to('/login');
        }

        $congeModel = new Conges();
        $conge = $congeModel->find($congeId);

        if (!$conge) {
            return redirect()->to('/rh')->with('error', 'Demande introuvable.');
        }
        if (($conge['statut'] ?? null) !== 'enAttente') {
            return redirect()->to('/rh')->with('error', 'Cette demande a déjà été traitée.');
        }

        $typesModel = new TypesConge();
        $type = $typesModel->find((int) $conge['TypeCongeId']);
        if (!$type) {
            return redirect()->to('/rh')->with('error', 'Type de congé introuvable.');
        }

        $deductible = (int) ($type['deductible'] ?? 0) === 1;
        $annee = (int) substr((string) ($conge['dateDebut'] ?? ''), 0, 4);

        $db = db_connect();
        $soldesModel = new Soldes();

        $db->transStart();

        if ($deductible) {
            $solde = $soldesModel
                ->where('EmployeId', $conge['EmployeId'])
                ->where('TypeCongeId', $conge['TypeCongeId'])
                ->where('annee', $annee)
                ->first();

            if (!$solde) {
                $soldesModel->insert([
                    'EmployeId' => $conge['EmployeId'],
                    'TypeCongeId' => $conge['TypeCongeId'],
                    'annee' => $annee,
                    'joursAttribues' => (int) ($type['joursAnnuels'] ?? 0),
                    'joursPris' => 0,
                ]);
                $solde = $soldesModel
                    ->where('EmployeId', $conge['EmployeId'])
                    ->where('TypeCongeId', $conge['TypeCongeId'])
                    ->where('annee', $annee)
                    ->first();
            }

            $dispo = $solde ? ((int) $solde['joursAttribues'] - (int) $solde['joursPris']) : 0;
            if ($dispo < (int) $conge['nbJours']) {
                $db->transRollback();
                return redirect()->to('/rh')->with('error', 'Solde insuffisant pour approuver cette demande.');
            }
        }

        $congeModel->update($congeId, [
            'statut' => 'approuvee',
            'commentaireRh' => null,
            'TraitePar' => $user['id'],
        ]);

        if ($deductible) {
            $builder = $db->table('soldes');
            $builder
                ->set('joursPris', 'joursPris + ' . (int) $conge['nbJours'], false)
                ->where('EmployeId', $conge['EmployeId'])
                ->where('TypeCongeId', $conge['TypeCongeId'])
                ->where('annee', $annee)
                ->update();
        }

        $db->transComplete();
        if ($db->transStatus() === false) {
            return redirect()->to('/rh')->with('error', 'Erreur lors du traitement.');
        }

        return redirect()->to('/rh')->with('success', 'Demande approuvée.');
    }

    public function refuse(int $congeId)
    {
        $user = session()->get('user');
        if (!$user) {
            return redirect()->to('/login');
        }

        $congeModel = new Conges();
        $conge = $congeModel->find($congeId);

        if (!$conge) {
            return redirect()->to('/rh')->with('error', 'Demande introuvable.');
        }
        if (($conge['statut'] ?? null) !== 'enAttente') {
            return redirect()->to('/rh')->with('error', 'Cette demande a déjà été traitée.');
        }

        $commentaire = trim((string) $this->request->getPost('commentaire'));
        if ($commentaire === '') {
            $commentaire = null;
        }

        $congeModel->update($congeId, [
            'statut' => 'refusee',
            'commentaireRh' => $commentaire,
            'TraitePar' => $user['id'],
        ]);

        return redirect()->to('/rh')->with('success', 'Demande refusée.');
    }

    private function initials(string $prenom, string $nom): string
    {
        $p = trim($prenom);
        $n = trim($nom);
        $first = $p !== '' ? mb_substr($p, 0, 1) : '';
        $last = $n !== '' ? mb_substr($n, 0, 1) : '';
        $out = strtoupper($first . $last);
        return $out !== '' ? $out : '—';
    }

    private function typeBadgeClass(string $libelle): string
    {
        $lib = mb_strtolower($libelle);
        if (str_contains($lib, 'annuel')) {
            return 't-annuel';
        }
        if (str_contains($lib, 'malad')) {
            return 't-maladie';
        }
        if (str_contains($lib, 'special')) {
            return 't-special';
        }
        return 't-sans-solde';
    }
}
