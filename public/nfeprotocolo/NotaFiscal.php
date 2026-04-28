<?php
require_once __DIR__ . '/../../config.php';
require_once 'Boleto.php';

class NotaFiscal {
    public $id_banco;
    public $chave;
    public $numero = "";
    public $serie = "";
    public $cnpj_emit = "";
    public $mes = "";
    public $ano = "";
    public $fornecedor;
    public $valor = 0;
    public $data;
    public $horario;
    public $boletos = [];

    public function __construct($chave) {
        global $pdo;
        $this->chave = $chave;

        $this->SepararChave($chave);
        $this->fornecedor = $this->NomeFornecedor($this->cnpj_emit, $pdo);

        $this->data = date("d/m/Y");
        $this->horario = date("H:i:s");
    }

    private function SepararChave($chave) {
        for ($i = 1; $i <= 44; $i++) {
            if ($i == 3 || $i == 4)
                $this->ano .= $chave[$i - 1];
            else if ($i == 5 || $i == 6)
                $this->mes .= $chave[$i - 1];
            else if ($i >= 7 && $i <= 20)
                $this->cnpj_emit .= $chave[$i - 1];
            else if ($i >= 23 && $i <= 25)
                $this->serie .= $chave[$i - 1];
            else if ($i >= 26 && $i <= 34)
                $this->numero .= $chave[$i - 1];
        }

        $this->numero = intval($this->numero);
        $this->serie = intval($this->serie);
        $this->cnpj_emit = intval($this->cnpj_emit);
    }

    public function AdicionarBoleto($codigo) {
        $boleto = new Boleto($codigo);
        $this->boletos[] = $boleto;
        $this->valor += $boleto->valor;
    }

    public function AdicionarBoletoViaBanco($valor, $vencimento) {
        $boleto = new Boleto($valor, $vencimento);
        $this->boletos[] = $boleto;
    }

    private function NomeFornecedor($cnpj, $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT nome FROM fornecedor WHERE cnpj = ?");
            $stmt->execute([$cnpj]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $row['nome'];
            } else {
                return "FORNECEDOR NÃO ENCONTRADO!!!";
            }
        } catch (Exception $e) {
            return "FORNECEDOR NÃO ENCONTRADO!!!";
        }
    }
}
?>
