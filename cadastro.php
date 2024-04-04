<?php
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

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Conectar ao banco de dados
    $conn = conectarBancoDados();

    // Preparar a consulta SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $email, $senha);

    // Executar a consulta
    if ($stmt->execute() === TRUE) {
        // Redireciona para a página principal
        header("Location: pagina principal.html");
        exit(); // Certifique-se de sair do script após o redirecionamento
    } else {
        echo "Erro ao cadastrar usuário: " . $conn->error;
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
}
?>
