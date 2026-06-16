<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header("Location: frontend/dashboard.php");
} else {
    header("Location: frontend/login.php");
}
?>
