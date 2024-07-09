<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailModel extends Model
{
    protected $table = 'email';
    protected $allowedFields = ['id_contact', 'email'];
    protected $useTimestamps = true;
     
    protected $validationRules = [
        'id_contact' => 'required|integer',
        'email' => 'required|valid_email', 
    ];       

    public function contact()
    {
        return $this->belongsTo(ContactModel::class, 'id_contact');
    }
}
