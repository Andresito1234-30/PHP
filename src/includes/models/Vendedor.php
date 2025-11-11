<?php

class Vendedor {

    private $conn;

    private $table_name = "vendedor";

    public $id;

    public $vendedor;

    public $direccion;

    public $fechaventa;

    public function __construct($db) {

        $this->conn = $db;

    }

    // Leer todos los vendedores

    public function leer() {

        $query = "SELECT * FROM " . $this->table_name . " ORDER BY fechaventa DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;

    }

    // Leer un vendedor por ID

    public function leerUno() {

        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {

            $this->vendedor = $row['vendedor'];

            $this->direccion = $row['direccion'];

            $this->fechaventa = $row['fechaventa'];

            return true;

        }

        return false;

    }

    // Crear vendedor

    public function crear() {

        $query = "INSERT INTO " . $this->table_name . " 

                 SET vendedor=:vendedor, direccion=:direccion, fechaventa=:fechaventa";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos (sin id, ya que es autoincremental)

        $this->vendedor = htmlspecialchars(strip_tags($this->vendedor));

        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $this->fechaventa = htmlspecialchars(strip_tags($this->fechaventa));

        $stmt->bindParam(":vendedor", $this->vendedor);

        $stmt->bindParam(":direccion", $this->direccion);

        $stmt->bindParam(":fechaventa", $this->fechaventa);

        if($stmt->execute()) {

            return true;

        }

        return false;

    }

    // Actualizar vendedor

    public function actualizar() {

        $query = "UPDATE " . $this->table_name . " 

                 SET vendedor=:vendedor, direccion=:direccion, fechaventa=:fechaventa 

                 WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar datos

        $this->vendedor = htmlspecialchars(strip_tags($this->vendedor));

        $this->direccion = htmlspecialchars(strip_tags($this->direccion));

        $this->fechaventa = htmlspecialchars(strip_tags($this->fechaventa));

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":vendedor", $this->vendedor);

        $stmt->bindParam(":direccion", $this->direccion);

        $stmt->bindParam(":fechaventa", $this->fechaventa);

        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()) {

            return true;

        }

        return false;

    }

    // Eliminar vendedor

    public function eliminar() {

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(1, $this->id);

        if($stmt->execute()) {

            return true;

        }

        return false;

    }

    // Contar total de vendedores

    public function contar() {

        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total'];

    }

    // Verificar si ID existe

    public function idExiste() {

        $query = "SELECT id FROM " . $this->table_name . " WHERE id = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        

        return $stmt->rowCount() > 0;

    }

}

?>