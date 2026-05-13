<?php

namespace App\Models;

use CodeIgniter\Model;

class TypesConge extends Model
{
    protected $table = 'typesConge';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'libelle',
        'joursAnnuels',
        'deductible'
    ];
}