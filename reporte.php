<?php
require('./fpdf/fpdf.php');
include('conexion.php');

// para compatibilidad con $conn
$conn = $conexion;

// === REPORTE GENERAL ===

// Consulta agrupada por nombre y día_semana
$sql = "
SELECT 
    nombre,
    dia_semana,
    SUM(cantidad) AS total_cantidad
FROM productos
GROUP BY nombre, dia_semana
ORDER BY dia_semana, nombre;
";

$result = $conn->query($sql);

// === Clase personalizada para PDF ===
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, utf8_decode('Reporte General de Productos'), 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(60, 10, utf8_decode('Nombre'), 1, 0, 'C');
        $this->Cell(40, 10, utf8_decode('Día'), 1, 0, 'C');
        $this->Cell(40, 10, utf8_decode('Cantidad Total'), 1, 0, 'C');
        $this->Cell(30, 10, utf8_decode('Precio'), 1, 1, 'C');
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// === Crear PDF general ===
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 11);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(60, 10, utf8_decode($row['nombre']), 1, 0, 'C');
    $pdf->Cell(40, 10, utf8_decode($row['dia_semana']), 1, 0, 'C');
    $pdf->Cell(40, 10, $row['total_cantidad'], 1, 0, 'C');
    $pdf->Cell(30, 10, utf8_decode(''), 1, 1, 'C');
}

// Guardar el PDF general
$pdf->Output('F', 'reporte_general.pdf');


// === REPORTE POR DESCRIPCIÓN ===
$sql_descripciones = "SELECT DISTINCT descripcion FROM productos WHERE descripcion IS NOT NULL;";
$descripciones = $conn->query($sql_descripciones);

while ($desc = $descripciones->fetch_assoc()) {
    $descripcion = $desc['descripcion'];

    $sql2 = "
    SELECT 
        nombre,
        dia_semana,
        SUM(cantidad) AS total_cantidad
    FROM productos
    WHERE descripcion = '$descripcion'
    GROUP BY nombre, dia_semana
    ORDER BY dia_semana, nombre;
    ";
    $result2 = $conn->query($sql2);

    $pdf2 = new PDF();
    $pdf2->AddPage();
    $pdf2->SetFont('Arial', 'B', 13);
    // $pdf2->Cell(0, 10, utf8_decode("Descripción: $descripcion"), 0, 1, 'C');
    $pdf2->Ln(5);
    $pdf2->SetFont('Arial', '', 11);

    while ($row = $result2->fetch_assoc()) {
        $pdf2->Cell(60, 10, utf8_decode($row['nombre']), 1, 0, 'C');
        $pdf2->Cell(40, 10, utf8_decode($row['dia_semana']), 1, 0, 'C');
        $pdf2->Cell(40, 10, $row['total_cantidad'], 1, 0, 'C');
        $pdf2->Cell(30, 10, utf8_decode(''), 1, 1, 'C');
    }

    $nombreArchivo = 'reporte_' . preg_replace('/\s+/', '_', $descripcion) . '.pdf';
    $pdf2->Output('F', $nombreArchivo);
}

echo "Los Reportes se han generado correctamente en la carpeta del proyecto.";
?>
