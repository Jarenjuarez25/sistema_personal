<?php

require_once(__DIR__ . "/../config/conexion.php");

$id = $_GET['id'];

$query = "DELETE FROM personal_militar 
WHERE id_personal = $1";

$result = pg_query_params(
    $conn,
    $query,
    array($id)
);

if ($result) {

    header("Location: ../../frontend/personal.php");
    exit;

} else {

    echo "Error al eliminar";

}
?>