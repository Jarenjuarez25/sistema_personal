<?php

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'bd_personal_brigada';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: 'aurajuarez2019J';

$conn = pg_connect("
host=$host
port=$port
dbname=$dbname
user=$user
password=$password
sslmode=require
");

if (!$conn) {
    die("Error de conexión");
}