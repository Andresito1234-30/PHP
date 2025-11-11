<?php

class Database {

    private $pdo;


    public function __construct() {

        $host = "db"; // localhost del docker

        $dbname = "ventas";

        $username = "Andres";

        $password = "Andres";


        try {

            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {

            die("Error de conexión: " . $e->getMessage());

        }

    }


    public function getConnection() {

        return $this->pdo;

    }

}