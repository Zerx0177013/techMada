<?php

namespace App\Models;

use CodeIgniter\Model;

class Departements extends Model
{
    protected $table = 'departements';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nom',
        'description'
    ];
}