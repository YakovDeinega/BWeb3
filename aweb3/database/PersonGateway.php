<?php

namespace TableGateways;

use PDO;
use PDOException;

class PersonGateway {


    /**
     * @var PDO
     */
    private $m_db = null;

    public function __construct($db) {
        $this->m_db = $db;
    }

    public function findAll() {
        $statement = "
            SELECT
                username, name, email, birthday, gender, superpower, biography, created_at
            FROM
                users;
        ";

        try {
            $statement = $this->m_db->query($statement);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $exception) {
                exit($exception->getMessage());
        }
    }

    public function find($username) {
        $statement = "
            SELECT
                username, name, email, birthday, gender, superpower, biography, created_at
            FROM
                users
            WHERE username = ?;
        ";

        try {
            $statement = $this->m_db->prepare($statement);
            $statement->execute([$username]);
            return $statement->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function insert(Array $input) {
        $statement = "
            INSERT INTO users 
                (username, name, email, birthday, gender, superpower, biography)
            VALUES
                (:username, :name, :email, :birthday, :gender, :superpower, :biography);
        ";

        try {
            $statement = $this->m_db->prepare($statement);
            $statement->execute([
                'username' => $input['username'],
                'name' => $input['name'],
                'email' => $input['email'],
                'birthday' => $input['birthday'],
                'gender' => $input['gender'],
                'superpower' => $input['superpower'],
                'biography' => $input['biography'] ?? null,
            ]);
            return $statement->rowCount();
        } catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function update($username, Array $input) {
        $statement = "
            UPDATE users
            SET
                username = :username,
                name = :name,
                email = :email,
                birthday = :birthday,
                gender = :gender,
                superpower = :superpower,
                biography = :biography
            WHERE
                username = :username;
        ";

        try {
            $statement = $this->m_db->prepare($statement);
            $statement->execute([
                'username' => $input['username'],
                'name' => $input['name'],
                'email' => $input['email'],
                'birthday' => $input['birthday'],
                'gender' => $input['gender'],
                'superpower' => $input['superpower'],
                'biography' => $input['biography'] ?? null,
            ]);
        }
        catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function delete($username) {
        $statement = "
            DELETE FROM users
            WHERE username = :username;
        ";

        try {
            $statement = $this->m_db->prepare($statement);
            $statement->execute([
                'username' => $username
            ]);
            return $statement->rowCount();
        }
        catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }
}