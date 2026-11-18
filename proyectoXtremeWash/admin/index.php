<?php
require_once __DIR__ . '/models/sistema.class.php';

$app = new Sistema();
$app->checarRol('Administrador'); // Valida sesión y rol de forma centralizada

$pdo = $app->db; // Acceso a la conexión PDO

// Tarjetas KPI
$totalClientes = $pdo->query("SELECT COUNT(*) FROM clientes")->fetchColumn();
$ordenesActivas = $pdo->query("SELECT COUNT(*) FROM ordenes_visita WHERE estado IN ('Pendiente', 'En Proceso')")->fetchColumn();
$totalVehiculos = $pdo->query("SELECT COUNT(*) FROM convenios_flotilla")->fetchColumn();
$totalServicios = $pdo->query("SELECT COUNT(*) FROM servicios")->fetchColumn();
$totalCotizaciones = $pdo->query("SELECT COUNT(*) FROM cotizaciones")->fetchColumn();

// Gráficas
$stmtEstado = $pdo->query("SELECT estado, COUNT(*) AS cantidad FROM ordenes_visita GROUP BY estado");
$dataEstado = $stmtEstado->fetchAll(PDO::FETCH_ASSOC);

$stmtTipo = $pdo->query("SELECT tipo_vehiculos, COUNT(*) AS cantidad FROM convenios_flotilla GROUP BY tipo_vehiculos");
$dataTipo = $stmtTipo->fetchAll(PDO::FETCH_ASSOC);

$stmtServ = $pdo->query("SELECT nombre, precio FROM servicios ORDER BY precio DESC");
$dataServ = $stmtServ->fetchAll(PDO::FETCH_ASSOC);

$stmtCotizaciones = $pdo->query("SELECT nombre_empresa, COUNT(*) AS nombre FROM cotizaciones GROUP BY nombre_empresa");
$dataCotizaciones = $stmtCotizaciones->fetchAll(PDO::FETCH_ASSOC);

// 3. RENDERIZAR PLANTILLAS
include_once __DIR__ . '/../templates/admin/header.html';
include_once __DIR__ . '/../templates/admin/index.html';
include_once __DIR__ . '/../templates/admin/footer.html';
?>