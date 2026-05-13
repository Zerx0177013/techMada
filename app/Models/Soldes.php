<?php

namespace App\Models;

use CodeIgniter\Model;

class Soldes extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'EmployeId',
        'TypeCongeId',
        'annee',
        'joursAttribues',
        'joursPris'
    ];
}