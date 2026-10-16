<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$projeto_id = $_SESSION['projeto_id'] ?? 1;
$task_id = $_GET['id'] ?? null;

if ($task_id) {
    $stmt = $pdo->prepare("DELETE FROM TAREFA WHERE IDT = ? AND IDP = ?");
    $stmt->execute([$task_id, $projeto_id]);
}

header("Location: list_task.php");
exit();
?>
