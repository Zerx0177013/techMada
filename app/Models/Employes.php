<?php

namespace App\Models;

use CodeIgniter\Model;

class Employes extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    protected $allowedFields    = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'DepartementId',
        'dateEmbauche',
        'actif'
    ];

    public function getInformation(int $id)
    {
        return $this->where('id', $id)->first();
    }
}
