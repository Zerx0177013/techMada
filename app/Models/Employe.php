<?php

namespace App\Models;

use CodeIgniter\Model;

class Employe extends Model
{
    protected $table = 'employe';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;

    // protected $allowedFields    = [
    //     'nom',
    //     'email',
    //     'mot_de_passe',
    //     'role_id'
    // ];

    public function getInformation($id)
    {
        return $this->where('id', $id)->first();
    }
}
