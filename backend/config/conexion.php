<?php

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'bd_brigada';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: 'aurajuarez2019J';
$sslmode = getenv('DB_SSLMODE') ?: 'disable';

$conn = pg_connect("
host=$host
port=$port
dbname=$dbname
user=$user
password=$password
sslmode=$sslmode
");

if (!$conn) {
    die("Error de conexión");
}