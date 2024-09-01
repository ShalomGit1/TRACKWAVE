<?php
require_once "database.php";

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $sql_delete = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_stmt_init($conn_login);
    if (mysqli_stmt_prepare($stmt, $sql_delete)) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn_login);
}

header("location: admin_dashboard.php");
exit;
?>
