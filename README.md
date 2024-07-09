
# Projeto Contacts API

## Pré-Requisitos

- PHP 8 ou superior
- MySQL Server
- CodeIgniter 4
- Composer

## Visão Geral

Esta API permite gerenciar contatos, incluindo seus endereços, telefones e emails. Ela utiliza o formato JSON para as requisições e respostas e segue os princípios RESTful.

**Importante**: O campo CEP é obrigatório caso você envie algum endereço junto com o contato. O sistema buscará automaticamente os dados do endereço (logradouro, bairro, cidade, estado) utilizando a API do ViaCEP.

## Instalação

Para instalar o projeto, siga os passos abaixo:

1. Clone o repositório:
   ```bash
   git clone https://github.com/seu-usuario/nome-do-repositorio.git
   cd nome-do-repositorio

2. Instale as dependências do Composer:
    ```bash
    composer install

3. Crie um banco de dados MySQL para o projeto. Nesse exemplo, o nome do banco de dados é contacts.

4. Configure o arquivo .env na raiz do projeto com as variáveis de ambiente para o banco de dados:
    ```bash
    database.default.hostname = localhost
    database.default.database = contacts
    database.default.username = usuario_do_banco
    database.default.password = senha_do_usuario
    database.default.DBDriver = MySQLi
    database.default.port = 3306

5. Execute as migrações para criar as tabelas necessárias no banco de dados:
    ```bash
    php spark migrate

6. Inicie o servidor de desenvolvimento do CodeIgniter:
    ```bash
    php spark serve

7. Acesse a documentação da API no Postman e aprenda como executá-la [aqui](https://www.postman.com/winter-meteor-689499/workspace/)


## Endpoints da API

Nossa API possui os seguintes endpoints:

- GET /contacts: Lista todos os contatos.
- POST /contacts: Cria um novo contato.
- PUT /contacts/{id}: Atualiza um contato existente.
- DELETE /contacts/{id}: Deleta um contato.


## 🔗 Links

[Documentação API - Postman](https://www.postman.com/winter-meteor-689499/workspace/ci-contacts/overview)

