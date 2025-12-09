<?php
    session_start();
    if (isset($_SESSION['user'])) {
        header("Location: ../index.php");
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Gestión de Usuarios</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h1>Gestión de Usuarios</h1>

        <?php
            if (isset($_POST["submit"])) {
                $usuario = $_POST["username"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $passwordRepeat = $_POST["confirm_password"];

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $errors = array();

                if (empty($usuario) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
                    array_push($errors,"Todos los campos son requeridos");
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($errors,"Email introducido no es valido");
                }
                if (strlen($password)<8) {
                    array_push($errors,"La contraseña debe de tener más de 8 caracteres");
                }
                if ($password!==$passwordRepeat) {
                    array_push($errors,"Las contraseñas no coinciden");
                }


                require_once "database.php";
                $sql = "SELECT * FROM users WHERE email = '$email'";
                $resultado = mysqli_query($conn, $sql);
                $rowCount = mysqli_num_rows($resultado);
                if ($rowCount>0) {
                    array_push($errors,"Este correo ya esta siendo utilizado");
                }




                if (count($errors)>0){
                    foreach ($errors as $error) {
                        echo "<div>$error</div>";
                    }
                }else{
                    //Agregamos la información en la base de datos
                    $sql = "INSERT INTO users (username, email, password) VALUES ( ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
                    if ($prepareStmt) {
                        mysqli_stmt_bind_param($stmt,"sss", $usuario, $email, $passwordHash);
                        mysqli_stmt_execute($stmt);
                        echo "<div>Se ha registrado satisfactoriamente</div>";
                    }else{
                        die("Revisa revisa");
                    }
                }
            }
        ?>
        <form action="../views/register_form.php" method="post">
            <div class="form-group">
                <input type="text" name="username" placeholder="Nombre de Usuario" >
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Correo Electrónico" >
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Ingresar Contraseña" >
            </div>
            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" >
            </div>
            <div class="form-btn">
                <button type="submit" value="Register" name="submit">Registrarse</button>
            </div>
        </form>
        <a href="login_form.php">¿Ya tienes cuenta? Iniciar Sesión</a>
        <a href="../index.php">Volver a Inicio</a>
    </div>
</body>
</html>

