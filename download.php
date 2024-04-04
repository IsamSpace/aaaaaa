<?php
// Verifica se o parâmetro 'arquivo' foi enviado na URL
if(isset($_GET['arquivo'])) {
    // Caminho para a pasta onde os arquivos estão armazenados
    $pasta = 'uploads/';
    
    // Obtém o nome do arquivo a ser baixado a partir do parâmetro 'arquivo'
    $nomeArquivo = $_GET['arquivo'];
    
    // Caminho completo para o arquivo
    $caminhoArquivo = $pasta . $nomeArquivo;
    
    // Verifica se o arquivo existe
    if(file_exists($caminhoArquivo)) {
        // Define os cabeçalhos HTTP para forçar o download do arquivo
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($caminhoArquivo) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($caminhoArquivo));
        
        // Envia o conteúdo do arquivo para o navegador
        readfile($caminhoArquivo);
        exit;
    } else {
        // Se o arquivo não existir, exibe uma mensagem de erro
        echo "O arquivo não existe.";
    }
} else {
    // Se o parâmetro 'arquivo' não foi especificado na URL, exibe uma mensagem de erro
    echo "Nome do arquivo não especificado.";
}
?>


