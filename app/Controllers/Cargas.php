<?php

namespace App\Controllers;

use Config\Database;
use PDO;
use PDOException;

class Cargas
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function getCategorias(){
        $sql = "SELECT * FROM categorias WHERE status_categoria = 1 AND tipo_categoria != 'outros'";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClimas(){
        $sql = "SELECT * FROM climas WHERE status_clima = 1 AND isCritico_clima = 1";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRiscos(){
        $sql = "SELECT * FROM riscos WHERE status_risco = 1";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $camposObrigatorios = ['origem', 'destino', 'distancia', 'categoria', 'valor', 'sinistros', 'clima', 'seguro'];

        foreach ($camposObrigatorios as $campo) {
            if (!isset($data[$campo]) || $data[$campo] === '' || $data[$campo] === null) {
                throw new PDOException("Campo obrigatório ausente ou vazio: {$campo}", 400);
            }
        }

        $origem    = $data['origem'] ?? null;
        $destino   = $data['destino'] ?? null;
        $distancia = $data['distancia'] ?? 0;
        $categoriaId = $data['categoria'] ?? null;
        $valor     = $data['valor'] ?? 0;
        $sinistros = $data['sinistros'] ?? 0;
        $climaId   = $data['clima'] ?? null;
        $seguro    = $data['seguro'] ?? false;


        $categorias = $this->getCategorias();
        $climas     = $this->getClimas();

        $categoria = null;
        foreach ($categorias as $cat) {
            if ($cat['id_categoria'] == $categoriaId) {
                $categoria = $cat;
                break;
            }
        }

        $clima = null;
        foreach ($climas as $c) {
            if ($c['id_clima'] == $climaId) {
                $clima = $c;
                break;
            }
        }

        $risco = 1;
        $motivos = [];
        $sugestoes = [];

        $this->verificarCategoria($categoria, $distancia, $risco, $motivos, $sugestoes);
        $this->verificarClima($clima, $risco, $motivos, $sugestoes);
        $this->verificarSinistros($sinistros, $risco, $motivos, $sugestoes);
        $this->verificarValorSemSeguro($valor, $seguro, $risco, $motivos, $sugestoes);

        if($risco > 4){
            $risco = 4;
        }

        $descricaoRisco = match (true) {
            $risco <= 1 => 'Baixo',
            $risco == 2 => 'Moderado',
            $risco == 3 => 'Alto',
            default => 'Crítico'
        };

        $response = [
            "nivel_risco" => $risco,
            "descricao_risco" => $descricaoRisco,
            "motivo" => implode("\n", $motivos),
            "sugestoes" => implode("\n", $sugestoes),
        ];

        $idCarga = $this->gravarCarga($data, $response);
        $response['id_carga'] = $idCarga;

        header('Content-Type: application/json');
        echo json_encode($response);
        exit;

    }

    public function gravarCarga(array $data, array $analise): int
    {
        try {
            $sql = "INSERT INTO cargas (
                cidadeOrigem_carga,
                ufOrigem_carga,
                cidadeDestino_carga,
                ufDestino_carga,
                distanciaRota_carga,
                codCategoria_carga,
                valor_carga,
                nroSinistros_carga,
                clima_carga,
                seguro_carga,
                nivelRisco_carga,
                motivosRisco_carga,
                sugestoesRisco_carga,
                status_carga
            ) VALUES (
                :cidadeOrigem, :ufOrigem, :cidadeDestino, :ufDestino,
                :distancia, :categoria, :valor, :sinistros, :clima,
                :seguro, :risco, :motivos, :sugestoes, :status
            )";

            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                'cidadeOrigem'  => $data['origem'],
                'ufOrigem'      => $data['uf_origem'],
                'cidadeDestino' => $data['destino'],
                'ufDestino'     => $data['uf_destino'],
                'distancia'     => $data['distancia'],
                'categoria'     => $data['categoria'],
                'valor'         => $data['valor'],
                'sinistros'     => $data['sinistros'],
                'clima'         => $data['clima'],
                'seguro'        => $data['seguro'] ? 1 : 0,
                'risco'         => $analise['nivel_risco'],
                'motivos'       => $analise['motivo'],
                'sugestoes'     => $analise['sugestoes'],
                'status'        => 1
            ]);

            return (int) $this->conn->lastInsertId();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "erro" => "erro",
                "message" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
            exit;
        }
    }

    private function verificarCategoria($categoria, $distancia, &$risco, &$motivos, &$sugestoes)
    {
        if (!$categoria) return;

        $tipo = strtolower($categoria['tipo_categoria'] ?? '');

        if (str_contains($tipo, 'quimico')) {
            $risco++;
            $motivos[] = "Produtos Químicos Perigosos";
            $sugestoes[] = "Sugerir seguro adicional";
        }

        if (str_contains($tipo, 'alimento') && $distancia > 300) {
            $risco++;
            $motivos[] = "Alimentos Perecíveis com distância > 300km";
            $sugestoes[] = "Sugerir câmara fria mais potente";
        }

        if (str_contains($tipo, 'eletronico') && $distancia > 50000) {
            $risco++;
            $motivos[] = "Eletrônicos Sensíveis com distância > 50.000km";
            $sugestoes[] = "Sugerir escolta armada";
        }
    }

    private function verificarClima($clima, &$risco, &$motivos, &$sugestoes)
    {
        if (!empty($clima['isCritico_clima'])) {
            $risco++;
            $motivos[] = "Clima crítico: " . ($clima['nome_clima'] ?? 'Não informado');
            $sugestoes[] = "Reavaliar rota e fazer inspeção no veículo";
        }
    }

    private function verificarSinistros($sinistros, &$risco, &$motivos, &$sugestoes)
    {
        if ($sinistros > 5 && $sinistros <= 10) {
            $risco++;
            $motivos[] = "Histórico de sinistros (6 a 10)";
            $sugestoes[] = "Sugerir seguro adicional";
        } elseif ($sinistros > 10) {
            $risco += 2;
            $motivos[] = "Histórico de sinistros (mais de 10)";
            $sugestoes[] = "Sugerir seguro adicional";
        }
    }

    private function verificarValorSemSeguro($valor, $seguro, &$risco, &$motivos, &$sugestoes)
    {
        if ($valor > 200000 && !$seguro) {
            $risco = max($risco, 4);
            $motivos[] = "Carga de alto valor (> R$200.000) sem seguro";
            $sugestoes[] = "Sugerir escolta armada";
        }
    }
}
