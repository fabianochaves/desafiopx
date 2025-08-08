# 🚚 Sistema de Avaliação de Risco de Cargas

Este projeto é uma API REST desenvolvida em **PHP puro**, com foco na **análise de risco de transporte de cargas**.

---

## 📦 Tecnologias Utilizadas

- PHP 8.1 (via Docker)
- MySQL (via Docker)
- Docker + Docker Compose
- Composer
- Dotenv
- Insomnia/Postman para testes

---

## 🐳 Como subir o ambiente com Docker

OBS: Com o docker em execução

1. **Clone o repositório e entre no mesmo:**

```bash
git clone https://github.com/fabianochaves/desafiopx.git
```

```bash
cd desafiopx
```

2. **Suba os Containers:**

```bash
docker-compose up -d
```

3. **Acesse o Container PHP:**
```bash
docker exec -it php_app bash
```

4. **Instale as dependências do composer:**
```bash
composer install
```

🧱 Banco de Dados
🔸 Dump inicial:

O arquivo dump.sql contém toda a estrutura e dados iniciais:

Tabela cargas

Tabela categorias

Tabela climas

Tabela riscos

Tabela logs

Se o banco não subir automaticamente, você pode executá-lo manualmente:
```bash
docker exec -i mysql mysql -u root -proot projeto_start < dump.sql
```

Ou então manualmente copiando os comandos que estão no arquivo dump.sql

🔧 Estrutura do Projeto
Caminho	Descrição
docker-compose.yml	Sobe os containers PHP + MySQL
Dockerfile	Imagem PHP com extensões
dump.sql	Script de criação do banco de dados
public/index.php	Arquivo de rotas principais
app/Controllers/Cargas.php	Controller principal para análise de risco
config/Database.php	Classe de conexão com o banco
.env	Variáveis de ambiente do banco de dados

🧪 Teste com Insomnia ou Postman
🔹 Rota:

POST http://localhost:8000/cargas

🔹 Exemplo de Body (JSON):

{
  "origem": "São Paulo",
  "uf_origem": "SP",
  "destino": "Rio de Janeiro",
  "uf_destino": "RJ",
  "distancia": 500,
  "categoria": 1,
  "valor": 300000,
  "sinistros": 11,
  "clima": 2,
  "seguro": false
}

🔹 Resposta esperada:

{
  "nivel_risco": X,
  "descricao_risco": "XXX",
  "motivo": "XXXX",
  "sugestoes": "XXXX",
  "id_carga": X
}

📌 Observações
As categorias e climas vêm do banco (categorias, climas) e são usados nas regras de cálculo.

A lógica de risco considera fatores como: tipo da carga, distância, sinistros, clima e seguro.

A resposta sempre inclui o nível de risco e sugestões.

O registro é gravado automaticamente na tabela cargas.