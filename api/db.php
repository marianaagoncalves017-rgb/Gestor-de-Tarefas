<?php
$host = 'db.cnkvkapszqxbclvzvwae.supabase.co';
$db   = 'postgres';
$user = 'postgres.cnkvkapszqxbclvzvwae';
$pass = 'Blackpink In Your Area 08-08-2016';
$port = '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>
