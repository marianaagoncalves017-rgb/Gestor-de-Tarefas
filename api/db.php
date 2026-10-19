<?php
// Credenciais oficiais do Session Pooler (IPv4 compatível)
$host     = 'aws-0-eu-west-1.pooler.supabase.com';
$port     = '5432';
$dbname   = 'postgres';
$user     = 'postgres.cnkvkapszqxbclvzvwae';
$password = 'Blackpink In Your Area 08-08-2016'; // Substitui pela tua palavra-passe do Supabase

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>
