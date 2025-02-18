# Tecnofit API

API para gerenciamento de recordes pessoais de movimentos (exercícios).

## Funcionalidades

- Consulta de ranking por movimento
- Paginação de resultados
- Cache com fallback em memória
- Ordenação por valor (peso) do recorde

## Pré-requisitos

- [PHP](https://www.php.net/downloads) (versão 8 ou superior)
- [Composer](https://getcomposer.org/download/)
- [MySQL](https://www.mysql.com/downloads/) ou outro banco de dados compatível

## Instalação

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/spindart/tecnofit.git
   cd tecnofit
   ```

2. **Instale as dependências:**

   ```bash
   composer install
   ```

3. **Configure o banco de dados:**

   - Crie um banco de dados no MySQL chamado `tecnofit`
   - Edite o arquivo `config/config.php` para atualizar a conexão PDO e para que funcione o phinx (migrations e seeds):


4. **Execute as migrações:**

   ```bash
   composer migrate
   ```

5. **Popule o banco de dados com dados iniciais:**

   ```bash
   composer seed
   ```

## Uso Local

1. **Configure o banco de dados MySQL localmente**
   - Crie um banco de dados chamado `tecnofit`
   - Configure o arquivo na pasta config/config.php com os dados para conexão PDO.

2. **Execute as migrações e seeds:**
   ```bash
   composer migrate
   composer seed
   ```

3. **Inicie o servidor PHP:**
   ```bash
   php -S localhost:8000 -t public
   ```

4. **Acesse:**
   - API: http://localhost:8000/movements/1/ranking
   - Documentação: http://localhost:8000/docs

## Uso com Docker

1. **Configure o arquivo config.php em config/config.php:**
   - Modifique (descomente os dados de conexão com o Docker e comente os Localhost)

2. **Inicie os containers:**
   ```bash
   docker-compose up -d
   ```

3. **Acesse:**
   - API: http://localhost/movements/1/ranking
   - Documentação: http://localhost/docs

## Endpoints Disponíveis

### GET /movements/{id}/ranking

Retorna o ranking de um movimento específico, incluindo:
- Nome do movimento
- Lista ordenada de usuários
- Recorde pessoal de cada usuário
- Posição no ranking
- Data do recorde

Exemplo de resposta:
```json
{
    "movement": "Deadlift",
    "ranking": [
        {
            "position": 1,
            "user": "Jose",
            "value": 190.0,
            "date": "2021-01-06 00:00:00"
        },
        {
            "position": 2,
            "user": "Joao",
            "value": 180.0,
            "date": "2021-01-02 00:00:00"
        }
    ]
}
```

## Estrutura do Projeto

```
.
├── config/
│   └── container.php
├── db/
│   ├── migrations/
│   └── seeds/
├── public/
│   └── index.php
├── src/
│   ├── Application/
│   ├── Domain/
│   └── Infrastructure/
└── tests/
    └── Unit/
```

## Testes

Para executar os testes unitários:

```bash
composer test
```

## Documentação da API

A API é documentada usando OpenAPI/Swagger. Você pode acessar a documentação de duas formas:

### Ambiente Local
- Interface Swagger UI: http://localhost:8000/docs
- Especificação OpenAPI: http://localhost:8000/swagger.php

### Ambiente Docker
- Interface Swagger UI: http://localhost/docs
- Especificação OpenAPI: http://localhost/swagger.php

## Uso da API

### Consultar Ranking de Movimento

```http
GET /movements/{id}/ranking?page=1&limit=10
```

#### Parâmetros

- `id` (obrigatório): ID do movimento
- `page` (opcional): Página atual (padrão: 1)
- `limit` (opcional): Itens por página (padrão: 10)

#### Exemplo de Resposta

```json
{
    "movement": "Deadlift",
    "ranking": [
        {
            "position": 1,
            "user": "John",
            "value": 200.0,
            "date": "2021-01-01 00:00:00"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total_items": 50,
        "total_pages": 5
    }
}
```


## Tecnologias

- PHP 8.0+
- MySQL 5.7
- Redis
- Docker
- Slim Framework
- PHPUnit