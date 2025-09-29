<?php
require_once 'config/database.php';

startSecureSession();

// Destroy session and redirect to index
session_unset();
session_destroy();

header('Location: index.php');
exit();
?>