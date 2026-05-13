<?php

namespace App\Controllers;

use Config\Database;

class EnsembleController extends BaseController
{
    public function index()
    {
        $db = Database::connect();

        $today = date('Y-m-d');
        $year = (int) date('Y');
        $yearMonth = date('Y-m');

        $activeEmployees = (int) $db->table('employes')->where('actif', 1)->countAllResults();
        $departementsCount = (int) $db->table('departements')->countAllResults();
        $pendingCount = (int) $db->table('conges')->where('statut', 'enAttente')->countAllResults();

        $approvedThisMonth = (int) ($db->query(
            "SELECT COUNT(*) AS c
             FROM conges
             WHERE statut = ?
               AND strftime('%Y-%m', createdAt) = ?",
            ['approuvee', $yearMonth]
        )->getRowArray()['c'] ?? 0);

        $absentCountToday = (int) ($db->query(
            "SELECT COUNT(*) AS c
             FROM conges
             WHERE statut = ?
               AND dateDebut <= ?
               AND dateFin >= ?",
            ['approuvee', $today, $today]
        )->getRowArray()['c'] ?? 0);

        $recentDemandesRaw = $db->table('conges c')
            ->select('c.id, c.nbJours, c.statut, c.dateDebut, c.dateFin, c.createdAt, e.prenom, e.nom, t.libelle AS type_libelle')
            ->join('employes e', 'e.id = c.EmployeId')
            ->join('typesConge t', 't.id = c.TypeCongeId')
            ->orderBy('c.createdAt', 'DESC')
            ->limit(3)
            ->get()
            ->getResultArray();

        $recentDemandes = array_map(fn ($row) => $this->decorateCongeRow($row), $recentDemandesRaw);

        $absentsRaw = $db->table('conges c')
            ->select('c.id, c.dateFin, e.prenom, e.nom, t.libelle AS type_libelle')
            ->join('employes e', 'e.id = c.EmployeId')
            ->join('typesConge t', 't.id = c.TypeCongeId')
            ->where('c.statut', 'approuvee')
            ->where('c.dateDebut <=', $today)
            ->where('c.dateFin >=', $today)
            ->orderBy('c.dateFin', 'ASC')
            ->limit(3)
            ->get()
            ->getResultArray();

        $absents = array_map(function (array $row) {
            $row['initials'] = $this->initials(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''));
            $row['avatar_class'] = $this->avatarClass(crc32(($row['prenom'] ?? '') . ($row['nom'] ?? '')));
            $row['retour_label'] = $this->formatDateFr($row['dateFin'] ?? null);
            $row['type_label'] = $this->humanType($row['type_libelle'] ?? '');
            return $row;
        }, $absentsRaw);

        $criticalSoldeCount = (int) ($db->query(
            "SELECT COUNT(DISTINCT s.EmployeId) AS c
             FROM soldes s
             JOIN typesConge t ON t.id = s.TypeCongeId
             WHERE s.annee = ?
               AND t.libelle LIKE ?
               AND (s.joursAttribues - s.joursPris) <= 2",
            [$year, '%annuel%']
        )->getRowArray()['c'] ?? 0);

        $currentUser = session()->get('user');

        return view('admin/dashboard', [
            'currentUser' => $currentUser,
            'activeEmployees' => $activeEmployees,
            'departementsCount' => $departementsCount,
            'pendingCount' => $pendingCount,
            'approvedThisMonth' => $approvedThisMonth,
            'absentCountToday' => $absentCountToday,
            'recentDemandes' => $recentDemandes,
            'absents' => $absents,
            'criticalSoldeCount' => $criticalSoldeCount,
            'year' => $year,
        ]);
    }

    public function employes()
    {
        $db = Database::connect();
        $year = (int) date('Y');

        $departements = $db->table('departements')
            ->select('id, nom')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

        $employeesRaw = $db->table('employes e')
            ->select('e.id, e.nom, e.prenom, e.email, e.role, e.dateEmbauche, e.actif, d.nom AS departement')
            ->join('departements d', 'd.id = e.DepartementId', 'left')
            ->orderBy('e.actif', 'DESC')
            ->orderBy('e.nom', 'ASC')
            ->orderBy('e.prenom', 'ASC')
            ->get()
            ->getResultArray();

        $annualTypeIdRow = $db->query(
            "SELECT id FROM typesConge WHERE libelle LIKE ? ORDER BY id ASC LIMIT 1",
            ['%annuel%']
        )->getRowArray();
        $annualTypeId = (int) ($annualTypeIdRow['id'] ?? 0);

        $annualSoldes = [];
        if ($annualTypeId > 0) {
            $soldesRows = $db->table('soldes')
                ->select('EmployeId, joursAttribues, joursPris')
                ->where('annee', $year)
                ->where('TypeCongeId', $annualTypeId)
                ->get()
                ->getResultArray();

            foreach ($soldesRows as $sr) {
                $annualSoldes[(int) $sr['EmployeId']] = [
                    'attribues' => (int) ($sr['joursAttribues'] ?? 0),
                    'pris' => (int) ($sr['joursPris'] ?? 0),
                ];
            }
        }

        $employees = array_map(function (array $row) use ($annualSoldes) {
            $fullName = trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''));
            $row['full_name'] = $fullName;
            $row['initials'] = $this->initials($fullName);
            $row['avatar_class'] = $this->avatarClass((int) ($row['id'] ?? 0));
            $row['role_badge_class'] = $this->roleBadgeClass($row['role'] ?? '');
            $row['status_class'] = ((int) ($row['actif'] ?? 0) === 1) ? 's-approuvee' : 's-annulee';
            $row['status_label'] = ((int) ($row['actif'] ?? 0) === 1) ? 'actif' : 'inactif';

            $solde = $annualSoldes[(int) ($row['id'] ?? 0)] ?? null;
            if ($solde) {
                $attribues = (int) $solde['attribues'];
                $pris = (int) $solde['pris'];
                $row['annual_available'] = max(0, $attribues - $pris);
                $row['annual_attribues'] = $attribues;
            } else {
                $row['annual_available'] = null;
                $row['annual_attribues'] = null;
            }

            return $row;
        }, $employeesRaw);

        $currentUser = session()->get('user');

        return view('admin/employes', [
            'currentUser' => $currentUser,
            'employees' => $employees,
            'departements' => $departements,
            'year' => $year,
        ]);
    }

    private function decorateCongeRow(array $row): array
    {
        $fullName = trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''));
        $row['full_name'] = $fullName;
        $row['initials'] = $this->initials($fullName);
        $row['avatar_class'] = $this->avatarClass((int) ($row['id'] ?? 0));
        $row['type_label'] = $this->humanType($row['type_libelle'] ?? '');
        $row['type_class'] = $this->typeClass($row['type_libelle'] ?? '');
        $row['status_label'] = $this->humanStatut($row['statut'] ?? '');
        $row['status_class'] = $this->statutClass($row['statut'] ?? '');
        $row['duree_label'] = (int) ($row['nbJours'] ?? 0) . ' j';
        return $row;
    }

    private function initials(string $fullName): string
    {
        $fullName = trim($fullName);
        if ($fullName === '') {
            return '—';
        }

        $parts = preg_split('/\s+/', $fullName) ?: [];
        $first = strtoupper(mb_substr($parts[0] ?? '', 0, 1));
        $last = strtoupper(mb_substr($parts[count($parts) - 1] ?? '', 0, 1));
        return $first . $last;
    }

    private function avatarClass(int $seed): string
    {
        $classes = ['av-green', 'av-amber', 'av-blue'];
        return $classes[abs($seed) % count($classes)];
    }

    private function humanType(string $libelle): string
    {
        $l = mb_strtolower($libelle);
        if (str_contains($l, 'annuel')) {
            return 'Annuel';
        }
        if (str_contains($l, 'malad')) {
            return 'Maladie';
        }
        if (str_contains($l, 'special') || str_contains($l, 'spécial')) {
            return 'Spécial';
        }
        return $libelle !== '' ? $libelle : '—';
    }

    private function typeClass(string $libelle): string
    {
        $l = mb_strtolower($libelle);
        if (str_contains($l, 'annuel')) {
            return 't-annuel';
        }
        if (str_contains($l, 'malad')) {
            return 't-maladie';
        }
        return 't-special';
    }

    private function humanStatut(string $statut): string
    {
        return match ($statut) {
            'enAttente' => 'en attente',
            'approuvee' => 'approuvée',
            'refusee' => 'refusée',
            'annulee' => 'annulée',
            default => $statut !== '' ? $statut : '—',
        };
    }

    private function statutClass(string $statut): string
    {
        return match ($statut) {
            'enAttente' => 's-attente',
            'approuvee' => 's-approuvee',
            'refusee' => 's-refusee',
            'annulee' => 's-annulee',
            default => 's-attente',
        };
    }

    private function roleBadgeClass(string $role): string
    {
        return match ($role) {
            'rh' => 't-maladie',
            'admin' => 't-annuel',
            default => '',
        };
    }

    private function formatDateFr(?string $isoDate): string
    {
        if (!$isoDate) {
            return '—';
        }
        $parts = explode('-', $isoDate);
        if (count($parts) !== 3) {
            return $isoDate;
        }
        return $parts[2] . '/' . $parts[1];
    }
}
