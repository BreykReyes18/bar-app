<?php
session_start();
include 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<!-- 🔹 Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Inventario</a>
    <div class="d-flex ms-auto">
      <?php if(isset($_SESSION['usuario'])): ?>
        <span class="navbar-text text-white me-3">
          <?= htmlspecialchars($_SESSION['usuario']) ?> (<?= htmlspecialchars($_SESSION['tipo']) ?>)
        </span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Salir</a>
      <?php else: ?>
        <a href="login/login.php" class="btn btn-outline-light btn-sm">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center mb-4">Lista de Productos</h2>
    <a href="agregar.php" class="btn btn-success mb-3">Agregar Producto</a>
    <a href="reporte.php" class="btn btn-primary mb-3">Generar Reporte</a>

    <!-- 🔎 Barra de búsqueda -->
    <input type="text" id="filtroDescripcion" class="form-control mb-3" placeholder="Filtrar por descripción...">

    <table id="tablaProductos" class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Día</th>
                <th>Total</th>
                <th>Descripción</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $totalGeneral = 0;
        $resultado = $conexion->query("SELECT * FROM productos ORDER BY creado_en DESC");
        while($fila = $resultado->fetch_assoc()):
            $totalPorProducto = $fila['precio'] * $fila['cantidad'];
            $totalGeneral += $totalPorProducto;
        ?>
            <tr>
                <td><?= htmlspecialchars($fila['nombre']) ?></td>
                <td class="precio"><?= number_format($fila['precio'], 2) ?></td>
                <td class="cantidad"><?= $fila['cantidad'] ?></td>
                <td><?= htmlspecialchars($fila['dia_semana']) ?></td>
                <td class="totalFila">C$ <?= number_format($totalPorProducto, 2) ?></td>
                <td class="descripcion"><?= htmlspecialchars($fila['descripcion']) ?></td>
                <td><?= date("d/m/Y H:i:s", strtotime($fila['creado_en'])) ?></td>
                <td>
                    <?php if(isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'administrador'): ?>
                        <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                        <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                    <?php else: ?>
                        <span class="text-muted">Solo lectura</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total General</strong></td>
                <td id="totalGeneral"><strong>C$ <?= number_format($totalGeneral, 2) ?></strong></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <td colspan="4"><strong>Total Filtrado</strong></td>
                <td id="totalFiltrado"><strong>C$ <?= number_format($totalGeneral, 2) ?></strong></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- 🔎 Script de filtrado -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("filtroDescripcion");
    const tabla = document.getElementById("tablaProductos").getElementsByTagName("tbody")[0];
    const totalFiltrado = document.getElementById("totalFiltrado");

    function filtrar() {
        const filtro = input.value.toLowerCase();
        let total = 0;

        for (let fila of tabla.rows) {
            const descripcion = fila.querySelector(".descripcion").innerText.toLowerCase();
            const cantidad = parseFloat(fila.querySelector(".cantidad").innerText) || 0;
            const precio = parseFloat(fila.querySelector(".precio").innerText.replace(",", "")) || 0;
            const subtotal = cantidad * precio;

            if (descripcion.includes(filtro)) {
                fila.style.display = "";
                total += subtotal;
            } else {
                fila.style.display = "none";
            }
        }
        totalFiltrado.innerHTML = "<strong>C$ " + total.toFixed(2) + "</strong>";
    }

    input.addEventListener("keyup", filtrar);
});
</script>
</body>
</html>
