<?php

require 'conexionpdo.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];

    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);


    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");

    if ($stmt->execute([$username, $password])) {

        echo "Usuario registrado correctamente. <a href='login.php'>Iniciar sesión</a>";

    } else {

        echo "Error al registrar usuario.";

    }

}

?>


<form method="POST">

    <input type="text" name="username" placeholder="Usuario" required><br>

    <input type="password" name="password" placeholder="Contraseña" required><br>

    <button type="submit">Registrar</button>

</form>