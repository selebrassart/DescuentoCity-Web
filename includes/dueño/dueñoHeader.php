<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Incluir navbar unificado -->
<?php include($_SERVER['DOCUMENT_ROOT'] . '/includes/navbar.php'); ?>