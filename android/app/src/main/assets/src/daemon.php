<?php
// daemon.php - Script de prueba para ejecución en segundo plano (Demonios)

// Solo registramos la fecha y hora para probar que el demonio está vivo
$logFile = sys_get_temp_dir() . '/daemon_log.txt';
$taskName = isset($_GET['task']) ? $_GET['task'] : 'unknown_task';
$time = date('Y-m-d H:i:s');

$message = "[{$time}] Demonio Phphone ejecutado exitosamente. Tarea: {$taskName}\n";

// Usamos FILE_APPEND para no sobrescribir el archivo y ver el historial
file_put_contents($logFile, $message, FILE_APPEND);

// Enviamos una respuesta exitosa
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Daemon log written to ' . $logFile]);
