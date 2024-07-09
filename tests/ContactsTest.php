<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use App\Models\ContactModel;

class ContactsTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $contact;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar um contato de teste no banco de dados
        $this->contact = [
            'id' => 1,
            'name' => 'João da Silva',
            'description' => 'Teste de contato',
            'addresses' => [
                ['zip_code' => '12345-678', 'city' => 'Cidade Teste']
            ],
            'phones' => [
                ['phone' => '123456789']
            ],
            'emails' => [
                ['email' => 'joaosilva@emailteste.com.br']
            ]
        ];
        $contactModel = new ContactModel();
        $contactId = $contactModel->insert($this->contact);
        $this->contact['id'] = $contactId;
    }

    public function testIndex()
    {
        $result = $this->call('get', 'contacts');
        $result->assertStatus(200);
        $result->assertJSONFragment([
            'name' => $this->contact['name']
        ]);
    }

    public function testShow()
    {
        $result = $this->call('get', "contacts/{$this->contact['id']}");
        $result->assertStatus(200);
        $expectedContact = [
            'id' => $this->contact['id'],
            'name' => $this->contact['name'],
            'description' => $this->contact['description'],
            'addresses' => $this->contact['addresses'],
            'phones' => $this->contact['phones'],
            'emails' => $this->contact['emails']
        ];
    
        $result->assertJSONFragment($expectedContact);
    }

    public function testCreate()
    {
        $newContact = [
            'name' => 'Maria Santos',
            'description' => 'Novo contato',
        ];

        $result = $this->call('post', 'contacts', $newContact);
        $result->assertStatus(201); 
        $result->assertJSONFragment($newContact);
    }

    public function testUpdate()
    {
        $updatedContact = [
            'name' => 'João da Silva Atualizado',
        ];

        $result = $this->call('put', "contacts/{$this->contact['id']}", $updatedContact);
        $result->assertStatus(200);
        $result->assertJSONFragment($updatedContact);
    }

    public function testDelete()
    {
        $result = $this->call('delete', "contacts/{$this->contact['id']}");
        $result->assertStatus(204);
    }
}