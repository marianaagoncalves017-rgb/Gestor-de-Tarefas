<?php
// Credenciais de ligação utilizando o Session Pooler do Supabase
$host     = 'aws-0-eu-central-1.pooler.supabase.com';
$port     = '6543'; // Alterado para o porto 6543 (Porto alternativo do Pooler)
$dbname   = 'postgres';
$user     = 'postgres.cnkvkapszqxbclvzvwae';
$password = 'Blackpink In Your Area 08-08-2016';
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro na ligação à base de dados: " . $e->getMessage());
}
?>
