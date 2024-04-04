<?php
session_start();

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Função para conectar ao banco de dados
function conectarBancoDados() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "pixel";

    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    return $conn;
}

// Verificar se o formulário de login foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email']) && isset($_POST['senha'])) {
    // Dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Conectar ao banco de dados
    $conn = conectarBancoDados();

    // Preparar a consulta SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);

    // Executar a consulta
    $stmt->execute();

    // Obter o resultado da consulta
    $result = $stmt->get_result();

    // Verificar se o usuário foi encontrado
    if ($result->num_rows == 1) {
        // Obter os dados do usuário
        $usuario = $result->fetch_assoc();

        // Verificar se a senha está correta
        if (password_verify($senha, $usuario['senha'])) {
            // Definir a variável de sessão para indicar que o usuário está logado
            $_SESSION['logged_in'] = true;

            // Redirecionar o usuário para a página de sucesso após o login
            header("Location: pagina_principal.html");
            exit;
        } else {
            // Senha incorreta
            echo "Senha incorreta. Por favor, tente novamente.";
        }
    } else {
        // Usuário não encontrado
        echo "Usuário não encontrado. Por favor, verifique seu e-mail.";
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
}
?>
