<?php
$dbml_file = __DIR__ . '/database.dbml';
$sql_file = __DIR__ . '/build/00-database.sql';

if (file_exists($sql_file)) {
    unlink($sql_file);
}

echo "Build dbml to mysql \n";
exec('dbml2sql ' . $dbml_file . ' -o ' . $sql_file . ' --mysql');

$sql_string = file_get_contents($sql_file);

echo "Convert created_at and updated_at \n";
$sql_string = preg_replace('/\(current_timestamp/i', 'current_timestamp', $sql_string);
$sql_string = preg_replace('/timestamp\(\)\)/i', 'timestamp()', $sql_string);

echo "Add collate \n";
$sql_string = preg_replace('/^\);/mi', ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;', $sql_string);

$prepend = implode("\n", [
    'SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT;',
    'SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS;',
    'SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION;',
    'SET NAMES utf8mb4;',
    'SET @OLD_TIME_ZONE = @@TIME_ZONE;',
    'SET TIME_ZONE = \'+00:00\';',
    'SET @OLD_UNIQUE_CHECKS = @@UNIQUE_CHECKS, UNIQUE_CHECKS = 0;',
    'SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS = 0;',
    'SET @OLD_SQL_MODE = @@SQL_MODE, SQL_MODE = \'NO_AUTO_VALUE_ON_ZERO\';',
    'SET @OLD_SQL_NOTES = @@SQL_NOTES, SQL_NOTES = 0;',
]);

$append = implode("\n", [
    'SET TIME_ZONE = @OLD_TIME_ZONE;',
    'SET SQL_MODE = @OLD_SQL_MODE;',
    'SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;',
    'SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;',
    'SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT;',
    'SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS;',
    'SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION;',
    'SET SQL_NOTES = @OLD_SQL_NOTES;',
]);

$drops = [];

preg_match_all('/^CREATE TABLE `([a-zA-Z\_]+)` \(/mi', $sql_string, $tables);
if (isset($tables[1])) {
    foreach ($tables[1] as $table) {
        $drops[] = "DROP TABLES IF EXISTS `{$table}`;";
    }

    $drops = "-- Drop Tables\n" . implode("\n", $drops);
}

$sql_string = preg_replace('/^-- Generated at: (.*)\n\n/mi', "-- Generated at: $1\n\n$prepend\n\n$drops\n\n", $sql_string);

file_put_contents($sql_file, $sql_string . "\n" . $append);

if (file_exists("$sql_file-r")) {
    unlink("$sql_file-r");
}

file_put_contents(
    __DIR__ . '/build/99-complete.sql',
    implode("\n\n\n", [
        file_get_contents($sql_file),
        file_get_contents(__DIR__ . '/build/01-sync-data.sql'),
        file_get_contents(__DIR__ . '/build/02-insert-data.sql')
    ])
);
