<?php

$mysqli = new mysqli('127.0.0.1', 'root', '', 'delivery_db');

if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

$result = $mysqli->query('DESC ratings');

if ($result) {
    echo "Estructura de la tabla 'ratings':\n";
    echo str_repeat("-", 60) . "\n";
    while ($row = $result->fetch_assoc()) {
        printf("%-20s %-30s %s\n", $row['Field'], $row['Type'], $row['Null'] ?? '');
    }
    echo "\nForeign Keys:\n";
    echo str_repeat("-", 60) . "\n";
    $fk_result = $mysqli->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='ratings' AND REFERENCED_TABLE_NAME IS NOT NULL");
    while ($row = $fk_result->fetch_assoc()) {
        printf("%s: %s -> %s(%s)\n", $row['CONSTRAINT_NAME'], $row['COLUMN_NAME'], $row['REFERENCED_TABLE_NAME'], $row['REFERENCED_COLUMN_NAME']);
    }
} else {
    echo "Error en query: " . $mysqli->error;
}

$mysqli->close();
?>
