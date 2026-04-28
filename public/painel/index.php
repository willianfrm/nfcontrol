<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Recebimento</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

<iframe id="painelFrame"></iframe>

<script>
    const tempo = 30000; // 30 segundos

    const frame = document.getElementById("painelFrame");

    function atualizaPainel() {
        frame.src = "painel.php";
    }

    // Carrega o primeiro imediatamente
    atualizaPainel();

    // Atualiza a cada 30 seg
    setInterval(atualizaPainel, tempo);
</script>

</body>
</html>