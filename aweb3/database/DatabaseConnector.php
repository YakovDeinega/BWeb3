<?php
namespace System;
use PDO;
use PDOException;

class DatabaseConnector {
    private $m_dbConnection = null;

    public function __construct() {
        $host = '212.192.134.20';
        $port = '22';
        $db = 'u54369';
        $user = 'u54369';
        $password = '9133627';

        try {
            $this->m_dbConnection = new PDO(
                "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db",
                $user,
                $password
            );
        }
        catch (PDOException $exception) {
            exit($exception->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->m_dbConnection;
    }
}
