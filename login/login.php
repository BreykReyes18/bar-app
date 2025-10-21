<?php
session_start();
include '../conexion.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre_usuario=? AND clave=?");
    $stmt->bind_param("ss", $usuario, $clave);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if($resultado->num_rows > 0){
        $fila = $resultado->fetch_assoc();
        $_SESSION['usuario'] = $fila['nombre_usuario'];
        $_SESSION['tipo'] = $fila['tipo'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h4 class="text-center">Iniciar Sesión</h4>
          <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
          <?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label>Usuario</label>
              <input type="text" name="usuario" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Contraseña</label>
              <input type="password" name="clave" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
