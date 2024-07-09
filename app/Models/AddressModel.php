<?php

namespace App\Models;

use CodeIgniter\Model;

class AddressModel extends Model
{
    protected $table = 'address';
    protected $allowedFields = [
        'id_contact', 'zip_code', 'country', 'state', 'street_address', 
        'address_number', 'city', 'address_line', 'neighborhood'
    ];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id_contact' => 'required|integer',
        'zip_code' => 'required|string',
    ];    

    public function contact()
    {
        return $this->belongsTo(ContactModel::class, 'id_contact');
    }
}
