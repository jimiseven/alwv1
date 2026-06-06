<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/config.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

mysqli_set_charset($conn, 'utf8mb4');

function quoteIdentifier($identifier) {
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function sqlValue($conn, $value) {
    if ($value === null) {
        return 'NULL';
    }

    return "'" . mysqli_real_escape_string($conn, (string) $value) . "'";
}

function writeLine($text = '') {
    echo $text . "\n";
}

$databaseName = DB_NAME;
$fileName = 'backup_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $databaseName) . '_' . date('Y-m-d_H-i-s') . '.sql';

header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Pragma: no-cache');
header('Expires: 0');

writeLine('-- Backup de base de datos');
writeLine('-- Base de datos: ' . $databaseName);
writeLine('-- Fecha: ' . date('Y-m-d H:i:s'));
writeLine('-- Generado desde Sistema ALW');
writeLine();
writeLine('SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
writeLine('SET time_zone = "+00:00";');
writeLine('SET NAMES utf8mb4;');
writeLine('SET FOREIGN_KEY_CHECKS = 0;');
writeLine();

$tables = [];
$views = [];
$result = mysqli_query($conn, 'SHOW FULL TABLES');

if (!$result) {
    writeLine('-- Error al obtener tablas.');
    exit;
}

while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
    if (($row[1] ?? '') === 'VIEW') {
        $views[] = $row[0];
    } else {
        $tables[] = $row[0];
    }
}

foreach ($tables as $table) {
    $quotedTable = quoteIdentifier($table);

    writeLine('-- --------------------------------------------------------');
    writeLine('-- Estructura de tabla para ' . $quotedTable);
    writeLine('-- --------------------------------------------------------');
    writeLine();
    writeLine('DROP TABLE IF EXISTS ' . $quotedTable . ';');

    $createResult = mysqli_query($conn, 'SHOW CREATE TABLE ' . $quotedTable);
    if ($createResult && ($createRow = mysqli_fetch_assoc($createResult))) {
        writeLine($createRow['Create Table'] . ';');
    }
    writeLine();

    $dataResult = mysqli_query($conn, 'SELECT * FROM ' . $quotedTable, MYSQLI_USE_RESULT);
    if (!$dataResult) {
        writeLine('-- No se pudieron exportar datos de ' . $quotedTable);
        writeLine();
        continue;
    }

    $fields = mysqli_fetch_fields($dataResult);
    $columns = array_map(function ($field) {
        return quoteIdentifier($field->name);
    }, $fields);

    $rowCount = 0;
    while ($row = mysqli_fetch_assoc($dataResult)) {
        if ($rowCount === 0) {
            writeLine('-- Datos de tabla ' . $quotedTable);
        }

        $values = [];
        foreach ($fields as $field) {
            $values[] = sqlValue($conn, $row[$field->name] ?? null);
        }

        writeLine('INSERT INTO ' . $quotedTable . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ');');
        $rowCount++;
    }
    mysqli_free_result($dataResult);

    if ($rowCount > 0) {
        writeLine();
    }
}

foreach ($views as $view) {
    $quotedView = quoteIdentifier($view);

    writeLine('-- --------------------------------------------------------');
    writeLine('-- Estructura de vista para ' . $quotedView);
    writeLine('-- --------------------------------------------------------');
    writeLine();
    writeLine('DROP VIEW IF EXISTS ' . $quotedView . ';');

    $createResult = mysqli_query($conn, 'SHOW CREATE VIEW ' . $quotedView);
    if ($createResult && ($createRow = mysqli_fetch_assoc($createResult))) {
        writeLine($createRow['Create View'] . ';');
    }
    writeLine();
}

$triggerResult = mysqli_query($conn, 'SHOW TRIGGERS');
if ($triggerResult && mysqli_num_rows($triggerResult) > 0) {
    writeLine('DELIMITER ;;');
    while ($trigger = mysqli_fetch_assoc($triggerResult)) {
        $triggerName = $trigger['Trigger'] ?? '';
        if ($triggerName === '') {
            continue;
        }

        $createTriggerResult = mysqli_query($conn, 'SHOW CREATE TRIGGER ' . quoteIdentifier($triggerName));
        if ($createTriggerResult && ($createTrigger = mysqli_fetch_assoc($createTriggerResult))) {
            writeLine('DROP TRIGGER IF EXISTS ' . quoteIdentifier($triggerName) . ';;');
            writeLine($createTrigger['SQL Original Statement'] . ';;');
            writeLine();
        }
    }
    writeLine('DELIMITER ;');
    writeLine();
}

writeLine('SET FOREIGN_KEY_CHECKS = 1;');
writeLine('-- Fin del backup');

mysqli_close($conn);
exit;
?>
