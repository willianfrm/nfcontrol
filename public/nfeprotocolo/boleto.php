<?php
class Boleto {
    private $codigo;
    public $valor;
    private $fator_de_vencimento;
    public $vencimento;

    // Construtor com código completo do boleto
    public function __construct($codigoOrValor, $vencimento = null) {
        if ($vencimento === null) {
            // Caso seja o código digitável
            $this->codigo = $codigoOrValor;
            $this->SepararCodigo($codigoOrValor);

            // Regra do fator de vencimento (base 22/02/2025 - subtrai 1000)
            $database = new DateTime("2025-02-22");
            $dias = $this->fator_de_vencimento - 1000;
            $database->modify("+$dias days");
            $this->vencimento = $database->format("d/m/Y");
        } else {
            // Caso venha direto do banco
            $this->valor = floatval(str_replace(",", ".", $codigoOrValor));
            $this->vencimento = $vencimento;
        }
    }

    private function SepararCodigo($codigo) {
        $fvenc = "";
        $val = "";
        $cent = "";

        for ($i = 1; $i <= 44; $i++) {
            if ($i >= 6 && $i <= 9)
                $fvenc .= $codigo[$i - 1];
            else if ($i >= 10 && $i <= 17)
                $val .= $codigo[$i - 1];
            else if ($i == 18 || $i == 19)
                $cent .= $codigo[$i - 1];
        }

        $this->fator_de_vencimento = intval($fvenc);
        $this->valor = floatval($val . "." . $cent);
    }
}
?>
