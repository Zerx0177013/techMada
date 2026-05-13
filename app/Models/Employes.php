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

    public function getAllEmployes()
    {
        return $this->findAll();
    }

    public function createEmploye(array $data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->insert($data);
    }

    public function updateEmploye(int $id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return $this->update($id, $data);
    }
    public function deleteEmploye(int $id)
    {
        return $this->delete($id);
    }
}
