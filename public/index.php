<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema NFControl</title>

    <!-- Bootstrap -->
    <link href="bootstrap-5.1.3-dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
        }

        .card-menu {
            transition: 0.3s;
            cursor: pointer;
            border-radius: 12px;
        }

        .card-menu:hover {
            transform: scale(1.03);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .card-title {
            font-weight: bold;
        }

        .container {
            margin-top: 60px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="text-center mb-5">
        <h1>Sistema NFControl - <?= $lojanome?></h1>
        <p class="text-muted">Selecione uma das opções abaixo</p>
    </div>

    <div class="row g-4">

        <!-- Recebimento Protocolo -->
        <div class="col-md-6">
            <div class="card card-menu h-100 text-center p-3">
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title">Recebimento Protocolo</h4>
                    <p class="card-text mt-3">
                        Programa para uso da portaria de recebimento, protocolar as notas fiscais à medida da chegada dos fornecedores.
                    </p>
                    <div class="mt-auto">
                        <a href="./nfeprotocolo" class="btn btn-primary w-100">Entrar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recebimento CPD -->
        <div class="col-md-6">
            <div class="card card-menu h-100 text-center p-3">
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title">Recebimento CPD</h4>
                    <p class="card-text mt-3">
                        Programa para uso do operador de CPD, onde visualizará as notas à medida que forem protocoladas para processá-las e dar retorno quando apta ao recebimento.
                    </p>
                    <div class="mt-auto">
                        <a href="./recebimento" class="btn btn-success w-100">Entrar</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel -->
        <div class="col-md-6">
            <div class="card card-menu h-100 text-center p-3">
                <div class="card-body d-flex flex-column">
                    <h4 class="card-title">Painel</h4>
                    <p class="card-text mt-3">
                        Painel informativo de recebimento ao vivo, ideal para exibição em TV para os setores envolvidos.
                    </p>
                    <div class="mt-auto">
                        <a href="./painel" class="btn btn-dark w-100">Entrar</a>
                    </div>
                </div>
            </div>
        </div>
		
		<!-- Cadastro de Fornecedor -->
		<div class="col-md-6">
			<div class="card card-menu h-100 text-center p-3">
				<div class="card-body d-flex flex-column">
					<h4 class="card-title">Cadastro de Fornecedor</h4>
					<p class="card-text mt-3">
						Cadastro dos fornecedores, incluindo CNPJ e Razão Social.
					</p>
					<div class="mt-auto">
						<a href="./fornecedor" class="btn btn-warning w-100">Entrar</a>
					</div>
				</div>
			</div>
		</div>

    </div>
</div>

</body>
</html>