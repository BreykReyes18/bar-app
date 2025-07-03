<!-- index.php -->
<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Lista de Productos</h2>
    <a href="agregar.php" class="btn btn-success mb-3">Agregar Producto</a>
    <table class="table table-bordered table-hover">
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
                <td><?= number_format($fila['precio'], 2) ?></td>
                <td><?= $fila['cantidad'] ?></td>
                <td><?= $fila['dia_semana'] ?></td>
                <td>C$ <?= number_format($totalPorProducto, 2) ?></td>
                <td><?= htmlspecialchars($fila['descripcion']) ?></td>
                <td><?= date("d/m/Y H:i:s", strtotime($fila['creado_en'])) ?></td>
                <td>
                    <a href="editar.php?id=<?= $fila['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="eliminar.php?id=<?= $fila['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar producto?')">Eliminar</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total General</strong></td>
                <td><strong>C$ <?= number_format($totalGeneral, 2) ?></strong></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
