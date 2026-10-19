<?php
$host = 'db.cnkvkapszqxbc-vzvwae.supabase.co';
$db   = 'postgres';
$user = 'postgres';
$pass = 'Blackpink In Your Area 08-08-2016';
$port = '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>
