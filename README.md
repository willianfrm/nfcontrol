# 🧾 NFControl

Sistema corporativo para protocolo e recebimento de notas fiscais, voltado ao varejo com CPD centralizado. Oferece rastreamento em tempo real via protocolo e painel de monitoramento ao vivo para acompanhamento das operações.


## 🚀 Funcionalidades

- 🧾 Protocolo e registro de notas fiscais:
  - Permite que o conferente ou encarregado de portaria de recebimento protocole a nota fiscal, gerando registros para consultas futuras e ao mesmo tempo já envie a nota para processamento por parte do CPD.

- 📥 Processamento de nota fiscal para CPD centralizado:
  - O setor de CPD (Portaria eletronica) onde faz o processamento da nota fiscal (conferencia de pedidos, cadastros, impostos e etc.), recebe em tempo real e processa a nota dando retorno para os setores ligados (Portaria de Recebimento, Comercial, Cadastro e Físcal) atráves do painel de acompanhamento, ideal para lojas com CPD centralizado em uma Matriz.

- 📊 Painel de monitoramento do recebimento em tempo real:
  - Painel informativo em tempo real, detalhando tempo de espera e status atual do processamento dos fornecedores.

- 🏪 Controle por loja:
  - Pode ser usado por multifiliais.


## ⚙️ Requisitos, Instalação e Configuração
- Requisitos:
  - 
Servidor de sua preferência (Windows ou Linux), Apache com PHP 7.0 ou superior, Mysql ou MariaDB, XAMPP ou WAMPP server serão uma boa escolha.

- Instalação e Configuração:
  -
1. Clone o repositório
2. Configure o banco de dados utilizando o arquivo:
   banco.sql
3. Ajuste as configurações no arquivo:
   config.php
4. Execute o projeto em um servidor local


## ▶️ Como utilizar

1. Acesse o sistema pelo navegador
2. Realize o recebimento de notas fiscais
3. Acompanhe os registros no painel em tempo real


## 📂 Estrutura do Projeto

public/ → arquivos acessíveis via navegador  
database/ → scripts do banco de dados  
docs/ → imagens e documentação  
