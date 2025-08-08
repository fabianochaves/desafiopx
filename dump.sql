CREATE TABLE `cargas` (
  `id_carga` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `cidadeOrigem_carga` varchar(30) NOT NULL,
  `ufOrigem_carga` varchar(2) NOT NULL,
  `cidadeDestino_carga` varchar(30) NOT NULL,
  `ufDestino_carga` varchar(2) NOT NULL,
  `distanciaRota_carga` int(11) NOT NULL,
  `codCategoria_carga` int(11) NOT NULL,
  `valor_carga` decimal(10,2) NOT NULL,
  `nroSinistros_carga` int(11) NOT NULL,
  `clima_carga` varchar(100) NOT NULL,
  `seguro_carga` tinyint(1) NOT NULL,
  `nivelRisco_carga` VARCHAR(20) NULL,
  `motivosRisco_carga` VARCHAR(500) NULL,
  `sugestoesRisco_carga` VARCHAR(500) NULL,
  `status_carga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome_categoria` varchar(100) NOT NULL,
  `tipo_categoria` varchar(20) NOT NULL,
  `status_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `categorias` (`id_categoria`, `nome_categoria`,`tipo_categoria`, `status_categoria`) VALUES
(1, 'Eletrônicos Sensíveis', 'eletronico', 1),
(2, 'Alimentos Perecíveis', 'alimento', 1),
(3, 'Produtos Químicos Perigosos', 'quimico', 1),
(4, 'Carga Geral Seca', 'outros', 1),
(5, 'Automotiva', 'outros', 1);


CREATE TABLE `climas` (
  `id_clima` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome_clima` varchar(50) NOT NULL,
  `status_clima` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `climas` (`id_clima`, `nome_clima`, `isCritico_clima`, `status_clima`) VALUES
(1, 'Estável', 0, 1),
(2, 'Chuva Moderada', 0, 1),
(3, 'Chuva Forte com Ventos', 1, 1),
(4, 'Neve/Gelo', 1, 1);

CREATE TABLE `riscos` (
  `id_risco` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nome_risco` varchar(50) NOT NULL,
  `status_risco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `riscos` (`id_risco`, `nome_risco`, `status_risco`) VALUES
(1, 'Baixo', 1),
(2, 'Médio', 1),
(3, 'Alto', 1),
(4, 'Crítico', 1);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mensagem TEXT NOT NULL,
    contexto VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
