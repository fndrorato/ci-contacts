<?php

namespace App\Models;

use CodeIgniter\Model;
// use App\Models\Traits\HasRelationships;

class ContactModel extends Model
{
    // use HasRelationships;
    
    protected $table = 'contacts';
    protected $allowedFields = ['name', 'description'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'name' => 'required|string',
        'description' => 'permit_empty|string',
    ];    

    public function addresses()
    {
        return $this->hasMany(AddressModel::class, 'id_contact');
    }

    public function phones()
    {
        return $this->hasMany(PhoneModel::class, 'id_contact');
    }

    public function emails()
    {
        return $this->hasMany(EmailModel::class, 'id_contact');
    }
}
