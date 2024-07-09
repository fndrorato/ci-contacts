<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Config\Services;
use App\Models\ContactModel;
use App\Models\AddressModel;
use App\Models\PhoneModel;
use App\Models\EmailModel;

class Contacts extends ResourceController
{
    protected $modelName = 'App\Models\ContactModel';
    protected $format = 'json';

    public function index()
    {
        $contacts = $this->model->findAll();

        foreach ($contacts as &$contact) {
            $contact = $this->loadRelationships($contact);
        }

        return $this->respond($contacts);

    }

    public function show($id = null)
    {

        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->failNotFound('Contato não encontrado');
        }

        $contact = $this->loadRelationships($contact);

        return $this->respond($contact);

    }    

    public function create()
    {
        $data = $this->request->getJSON(true);

        // Validação dos dados
        if (!$this->validateContactData($data)) {
            return $this->failValidationErrors(Services::validation()->getErrors());
        }

        $data = $this->sanitizeData($data);

        // Consultar a API ViaCEP para preencher os dados do endereço
        if (isset($data['addresses'])) {
            foreach ($data['addresses'] as &$address) {
                if (isset($address['zip_code'])) {
                    $cepData = $this->consultaCEP($address['zip_code']);
                    if ($cepData && !isset($cepData['erro'])) {
                        $address['street_address'] = $cepData['logradouro'] ?? null;
                        $address['address_line'] = $cepData['complemento'] ?? null;
                        $address['neighborhood'] = $cepData['bairro'] ?? null;
                        $address['city'] = $cepData['localidade'] ?? null;
                        $address['state'] = $cepData['uf'] ?? null;
                    }
                }
            }
        }        

        // Iniciar transação para garantir a integridade dos dados
        $this->model->db->transBegin();

        try {
            // Criar o contato principal
            $contactId = $this->model->insert($data);

            // Inserir endereços, telefones e emails relacionados
            $this->insertRelatedData($contactId, $data);

            $this->model->db->transCommit();
            return $this->respondCreated($this->model->find($contactId));
        } catch (\Exception $e) {
            $this->model->db->transRollback();
            return $this->failServerError('Erro ao criar o contato: ' . $e->getMessage());
        }
    }

    public function update($id = null)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->failNotFound('Contato não encontrado');
        }

        $data = $this->request->getJSON(true);

        // Validação dos dados
        if (!$this->validateContactData($data)) {
            return $this->failValidationErrors(Services::validation()->getErrors());
        }

        $data = $this->sanitizeData($data);

        // Consultar a API ViaCEP para preencher os dados do endereço
        if (isset($data['addresses'])) {
            foreach ($data['addresses'] as &$address) {
                if (isset($address['zip_code'])) {
                    $cepData = $this->consultaCEP($address['zip_code']);
                    if ($cepData && !isset($cepData['erro'])) {
                        $address['street_address'] = $cepData['logradouro'] ?? null;
                        $address['address_line'] = $cepData['complemento'] ?? null;
                        $address['neighborhood'] = $cepData['bairro'] ?? null;
                        $address['city'] = $cepData['localidade'] ?? null;
                        $address['state'] = $cepData['uf'] ?? null;
                    }
                }
            }
        }          

        // Iniciar transação
        $this->model->db->transBegin();

        try {
            // Atualizar o contato principal
            $this->model->update($id, $data);

            // Atualizar dados relacionados (endereços, telefones, emails)
            $this->updateRelatedData($id, $data);

            $this->model->db->transCommit();
            return $this->respondUpdated($this->model->find($id));
        } catch (\Exception $e) {
            $this->model->db->transRollback();
            return $this->failServerError('Erro ao atualizar o contato: ' . $e->getMessage());
        }
    }

    public function delete($id = null)
    {
        $contact = $this->model->find($id);
        if (!$contact) {
            return $this->failNotFound('Contato não encontrado');
        }

        if ($this->model->delete($id)) {
            return $this->respondNoContent();
            // return $this->respondDeleted(['id' => $id]); // Os dados relacionados serão excluídos em cascata
        } else {
            return $this->failServerError('Erro ao excluir o contato');
        }
    }

    private function validateContactData($data)
    {
        $validationRules = [
            'name' => 'required|string',
            'description' => 'permit_empty|string',
        ];

        $validator = Services::validation(); 

        if (!$validator->setRules($validationRules)->run($data)) {
            return false;
        }        

        // Validação dos endereços
        if (isset($data['addresses'])) {
            if (!is_array($data['addresses'])) {
                $validator->setError('addresses', 'The addresses field must be an array.');
                return false;
            }

            foreach ($data['addresses'] as $address) {
                $addressModel = new AddressModel();
                if (!$addressModel->validate($address)) {
                    $validator->setErrors($addressModel->errors());
                    return false;
                }
            }
        }

        // Validação dos telefones
        if (isset($data['phones'])) {
            if (!is_array($data['phones'])) {
                $validator->setError('phones', 'The phones field must be an array.');
                return false;
            }

            foreach ($data['phones'] as $phone) {
                $phoneModel = new PhoneModel();
                if (!$phoneModel->validate($phone)) {
                    $validator->setErrors($phoneModel->errors());
                    return false;
                }
            }
        }

        // Validação dos emails
        if (isset($data['emails'])) {
            if (!is_array($data['emails'])) {
                $validator->setError('emails', 'The emails field must be an array.');
                return false;
            }

            foreach ($data['emails'] as $email) {
                $emailModel = new EmailModel();
                if (!$emailModel->validate($email)) {
                    $validator->setErrors($emailModel->errors());
                    return false;
                }
            }
        }        

        return true;
    }    
    

    // Métodos auxiliares para inserir e atualizar dados relacionados
    private function insertRelatedData($contactId, $data)
    {
        if (isset($data['addresses'])) {
            foreach ($data['addresses'] as $address) {
                $address['id_contact'] = $contactId;
                (new AddressModel())->insert($address);
            }
        }

        if (isset($data['phones'])) {
            foreach ($data['phones'] as $phone) {
                $phone['id_contact'] = $contactId;
                (new PhoneModel())->insert($phone);
            }
        }

        if (isset($data['emails'])) {
            foreach ($data['emails'] as $email) {
                $email['id_contact'] = $contactId;
                (new EmailModel())->insert($email);
            }
        }        
    }

    private function updateRelatedData($contactId, $data)
    {
        // Atualizar endereços
        $this->updateRelated($contactId, $data['addresses'] ?? [], new AddressModel(), 'id_contact');

        // Atualizar telefones
        $this->updateRelated($contactId, $data['phones'] ?? [], new PhoneModel(), 'id_contact');

        // Atualizar emails
        $this->updateRelated($contactId, $data['emails'] ?? [], new EmailModel(), 'id_contact');
    }

    private function updateRelated($contactId, $newData, $model, $fk)
    {
        $existingData = $model->where($fk, $contactId)->findAll();
        
        // Encontrar IDs a serem removidos
        $idsToRemove = array_diff(array_column($existingData, 'id'), array_column($newData, 'id'));
        if (!empty($idsToRemove)) {
            $model->delete($idsToRemove);
        }

        // Inserir ou atualizar os dados
        foreach ($newData as $item) {
            $item[$fk] = $contactId; // Garantir que o id_contact esteja correto

            if (isset($item['id'])) {
                $model->update($item['id'], $item);
            } else {
                $model->insert($item);
            }
        }
    }   
    
    private function sanitizeData($data)
    {
        $security = Services::security();
        foreach ($data as $key => $value) {
            $data[$key] = is_array($value) ? $this->sanitizeData($value) : $security->sanitizeFilename($value);
        }
        return $data;
    }  
    
    private function consultaCEP($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep); // Remover caracteres não numéricos
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);
    
        return json_decode($response, true);
    }

    private function loadRelationships($contact)
    {
        // Carregar endereços
        $addressModel = new AddressModel();
        $contact['addresses'] = $addressModel->where('id_contact', $contact['id'])->findAll();

        // Carregar telefones
        $phoneModel = new PhoneModel();
        $contact['phones'] = $phoneModel->where('id_contact', $contact['id'])->findAll();

        // Carregar emails
        $emailModel = new EmailModel();
        $contact['emails'] = $emailModel->where('id_contact', $contact['id'])->findAll();

        return $contact;
    }   
        
}
