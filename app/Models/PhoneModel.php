<?php

namespace App\Models;

use CodeIgniter\Model;

class PhoneModel extends Model
{
    protected $table = 'phone';
    protected $allowedFields = ['id_contact', 'phone'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id_contact' => 'required|integer',
        'phone' => 'required|string',
    ];      

    public function contact()
    {
        return $this->belongsTo(ContactModel::class, 'id_contact');
    }
}
