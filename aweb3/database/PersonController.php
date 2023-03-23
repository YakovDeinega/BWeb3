<?php

namespace Controller;

require "PersonGateway.php";

use PDO;
use TableGateways\PersonGateway;

class PersonController {

    /**
     * @var PDO
     */
    private $db;
    /**
     * @var string
     */
    private $requestMethod;
    /**
     * @var string
     */
    private $username;

    /**
     * @var PersonGateway
     */
    private $personGateway;

    public function __construct($db, $requestMethod, $username) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->username = $username;

        $this->personGateway = new PersonGateway($db);
    }

    public function processRequest() {
        switch ($this->requestMethod) {
            case 'GET':
                if ($this->username) {
                    $response = $this->getUser($this->username);
                }
                else {
                    $response = $this->getAllUsers();
                }
                break;
            case 'POST':
                $response = $this->createUserFromRequest();
                break;
            case 'PUT':
                $response = $this->updateUserFromRequest($this->username);
                break;
            case 'DELETE':
                $response = $this->deleteUser($this->username);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllUsers(): array
    {
        $result = $this->personGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id): array
    {
        $result = $this->personGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUserFromRequest(): array
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        if ($this->personGateway->find($input['username'])) {
            return $this->conflictResponse();
        }
        $this->personGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUserFromRequest($id): array
    {
        $result = $this->personGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validatePerson($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->personGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteUser($id): array
    {
        $result = $this->personGateway->find($id);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->personGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }

    private function validatePerson(array $input): bool
    {
        if (!isset($input['username'])) return false;
        if (!isset($input['name'])) return false;
        if (!isset($input['email'])) return false;
        if (!isset($input['birthday'])) return false;
        if (!isset($input['gender'])) return false;
        if (!isset($input['superpower'])) return false;
        return true;
    }

    private function unprocessableEntityResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function conflictResponse(): array {
        $response['status_code_header'] = 'HTTP/1.1 409 Conflict';
        $response['body'] = json_encode([
            'error' => 'User with given username already exists'
        ]);
        return $response;
    }
}