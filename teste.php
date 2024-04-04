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

// Verifica se o usuário está logado
if (!isset($_SESSION['logged_in'])) {
    // Redirecionar para o arquivo de cadastro
    header("Location: cadastro.html");
    exit; // Certifique-se de sair após o redirecionamento
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



// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Conectar ao banco de dados
    $conn = conectarBancoDados();

    // Preparar a consulta SQL
    $stmt = $conn->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $senha);

    // Executar a consulta
    if ($stmt->execute() === TRUE) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar usuário: " . $conn->error;
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
}



// Configurações do servidor SMTP do Google
$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUser = 'pixelsharequality@gmail.com'; // Seu e-mail do Gmail
$smtpPassword = 'pkiw zuaf ckgq ycmr'; // Sua senha do Gmail
$smtpEncryption = 'tls';

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Criar pasta uploads se não existir
    $diretorioArquivos = 'uploads/';
    if (!file_exists($diretorioArquivos)) {
        mkdir($diretorioArquivos, 0777, true);
    }

    // Dados do formulário
    $destinatario = $_POST['email_destinatario'];
    $nomeDestinatario = $_POST['nome_destinatario'];
    $remetente = 'seu_email@exemplo.com'; // Substitua pelo seu e-mail
    $nomeRemetente = 'PIXEL SHARE QUALITY';

    // Upload do arquivo
    $nomeArquivo = basename($_FILES['fileToUpload']['name']);
    $caminhoArquivo = $diretorioArquivos . $nomeArquivo;
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $caminhoArquivo)) {
        // Define o caminho do diretório de upload
        $diretorioArquivos = 'uploads/';

        // Conectar ao banco de dados
        $conn = conectarBancoDados();

        // Lê o conteúdo do arquivo
        $conteudoArquivo = file_get_contents($caminhoArquivo);

        // Prepara o conteúdo do arquivo para ser armazenado no banco de dados
        $conteudoArquivoBanco = $conn->real_escape_string($conteudoArquivo);

        // Insere os detalhes do arquivo no banco de dados
        $sql = "INSERT INTO arquivos (nome_arquivo, conteudo_arquivo, destinatario, remetente) VALUES ('$nomeArquivo', '$conteudoArquivoBanco', '$destinatario', '$remetente')";

        if ($conn->query($sql) === TRUE) {
            // Instanciar a classe PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configurações do servidor SMTP
                $mail->isSMTP();
                $mail->Host = $smtpHost;
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUser;
                $mail->Password = $smtpPassword;
                $mail->Port = $smtpPort;
                $mail->SMTPSecure = $smtpEncryption;

                // Configurações do e-mail
                $mail->setFrom($remetente, $nomeRemetente);
                $mail->addAddress($destinatario, $nomeDestinatario);
               
$mail->isHTML(true);
$mail->Body = "
<html>
<head>
<style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            color: #333;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        p {
            margin-bottom: 10px;
        }
        .link-download {
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            display: block;
            width: fit-content;
            margin: 0 auto;
            font-size: 16px;
        }
        .link-download:hover {
            background-color: #0052cc;
        }
        .logo {
            max-width: 200px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        /* Caixa preta */
        .black-box {
            background-color: #000;
            color: #fff;
            padding: 50px;
            margin-bottom: 20px;
            border-radius: 5px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .item {
            margin-bottom: 20px;
        }
        .item-text {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .ad {
            text-align: center;
            font-size: 18px;
        }
        .emoji {
            font-size: 24px;
        }
      /* Estilo da imagem */
        .img {
            position: absolute; /* Define posição absoluta */
            bottom: 20px; /* Define margem inferior */
            left: 50%; /* Define a metade do container */
            transform: translateX(-50%); /* Centraliza horizontalmente */
        }
    </style>
</head>
<body>
    <h1>Arquivo Compartilhado via Sistema de Envio PIXEL SHARE QUALITY</h1>
    <div class='black-box'>
        <h2>Olá 😁🖖🏻 $nomeDestinatario,</h2>
        <p>O remetente $nomeRemetente compartilhou um arquivo muito importante com você.</p>
        <p>Clique no link abaixo 👇🏻👇🏻 para fazer o download:</p>
        <p>A não esqueça o link espira em 24 horas Então nao deixe para depois</p>
        <p><a class='link-download' href='http://localhost/pixel.php/download.phparquivo=$nomeArquivo'>Download do Arquivo</a></p>
        <img src='uploads/logo.png' 
    </div>
    <div class='black-box'>
        <h2> 👇🏻venha conhecer  Pixel Share Quality👇🏻 </h2>
        <p>🚀 Bem-vindo ao <strong>Pixel Share Quality</strong>! 🚀</p>
        <p>Simplificando o compartilhamento de arquivos com segurança e facilidade.</p>
        <p>💼 Faça transferências ilimitadas de arquivos, grandes ou pequenos, de forma rápida e segura.</p>
        <p>💡 Compartilhe documentos importantes, fotos, vídeos e muito mais com apenas alguns cliques.</p>
        <p>💻 Aproveite nossa plataforma intuitiva e amigável, projetada para tornar o compartilhamento de arquivos uma experiência simples e agradável.</p>
        <p>🔒 Sua privacidade é nossa prioridade. Todas as transferências são protegidas com criptografia de ponta a ponta.</p>
        <p>Experimente o <strong>Pixel Share Quality</strong> hoje mesmo e descubra como podemos simplificar sua vida digital! 💻🔗📁</p>
    </div>
</body>
</html>
";


                // Enviar o e-mail
                $mail->send();
                echo 'Arquivo enviado com sucesso!';
            } catch (Exception $e) {
                echo 'Erro ao enviar o e-mail: ', $mail->ErrorInfo;
            }
        } else {
            echo "Erro ao enviar o arquivo: " . $conn->error;
        }

        // Fechar a conexão com o banco de dados
        if ($conn) {
            $conn->close();
        }
    } else {
        echo "Erro ao mover o arquivo para o diretório de uploads.";
    }
}

// Verifica se o arquivo foi enviado com sucesso
if(isset($_FILES['fileToUpload'])) {
    // Criar pasta uploads se não existir
    $diretorioArquivos = 'uploads/';
    if (!file_exists($diretorioArquivos)) {
        mkdir($diretorioArquivos, 0777, true);
    }

    // Dados do arquivo enviado
    $nomeArquivo = basename($_FILES['fileToUpload']['name']);
    $caminhoArquivo = $diretorioArquivos . $nomeArquivo;
    
    // Move o arquivo para a pasta de uploads
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $caminhoArquivo)) {
        echo "Arquivo enviado com sucesso!";
    } else {
        echo "Erro ao enviar o arquivo.";
    }
}

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['logged_in'])) {
    // Redirecionar para o arquivo de cadastro
    header("Location: cadastro.html");
    exit; // Certifique-se de sair após o redirecionamento
}


