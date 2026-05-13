<?php

namespace App\Models;

use CodeIgniter\Model;

class Conges extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'EmployeId',
        'TypeCongeId',
        'dateDebut',
        'dateFin',
        'nbJours',
        'motif',
        'statut',
        'commentaireRh',
        'createdAt',
        'TraitePar'
    ];
}