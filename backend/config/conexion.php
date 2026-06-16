<?php
$host = "localhost";
$port = "5432";
$dbname = "bd_personal_brigada";
$user = "postgres";
$password = "aurajuarez2019J";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Error de conexión");
}

?>