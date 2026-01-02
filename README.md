# Backend - Blog API

API REST desenvolvida em PHP 8.2 seguindo os princípios de Clean Architecture.

## Índice

- [Tecnologias](#tecnologias)
- [Configuração](#configuração)
- [Como Rodar](#como-rodar)
  - [Com Docker (Recomendado)](#com-docker-recomendado)
  - [Sem Docker](#sem-docker)
- [Variáveis de Ambiente](#variáveis-de-ambiente)
- [Rotas da API](#rotas-da-api)
- [Estrutura do Projeto](#estrutura-do-projeto)

## Tecnologias

- PHP 8.2
- MySQL 8.0
- PDO (PHP Data Objects)
- JWT (JSON Web Tokens) para autenticação
- Clean Architecture
- Docker & Docker Compose

## Configuração

### 1. Clone o repositório

```bash
git clone <seu-repositorio>
cd backend
```

### 2. Configure as variáveis de ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Edite o arquivo `.env` conforme necessário (veja [Variáveis de Ambiente](#variáveis-de-ambiente)).

## Como Rodar

### Com Docker (Recomendado)

#### Pré-requisitos
- Docker
- Docker Compose

#### Passos

1. **Construir e iniciar os containers**:
```bash
docker-compose up -d --build
```

2. **Verificar status dos serviços**:
```bash
docker-compose ps
```

3. **Acessar a API**:
- API: http://localhost:8000
- PHPMyAdmin: http://localhost:8080
- MySQL: localhost:3306

4. **Ver logs**:
```bash
# Todos os serviços
docker-compose logs -f

# Apenas backend
docker-compose logs -f backend

# Apenas MySQL
docker-compose logs -f mysql
```

5. **Parar os serviços**:
```bash
docker-compose down
```

6. **Parar e remover volumes (limpa o banco)**:
```bash
docker-compose down -v
```

### Sem Docker

#### Pré-requisitos
- PHP 8.2 ou superior
- MySQL 8.0 ou superior
- Composer
- Extensões PHP: `pdo`, `pdo_mysql`

#### Passos

1. **Instalar dependências**:
```bash
composer install
```

2. **Configurar banco de dados**:

Crie o banco de dados MySQL:
```sql
CREATE DATABASE blog_db;
```

Execute o schema SQL:
```bash
mysql -u root -p blog_db < src/Infra/Database/schemas.sql
```

3. **Configurar `.env`**:

Certifique-se de que as variáveis de banco estão corretas no `.env`:
```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=sua_senha
DB_NAME=blog_db
```

4. **Iniciar o servidor**:
```bash
composer server
# ou
php -S localhost:8000 -t src/Infra/Http
```

5. **Acessar a API**:
```
http://localhost:8000
```

## Variáveis de Ambiente

Arquivo `.env` na raiz do projeto backend:

### Aplicação
```env
# Ambiente da aplicação (development, production, test)
APP_ENV=development

# URL base da aplicação
APP_URL=http://localhost:8000
```

### Banco de Dados
```env
# Host do banco de dados
# Docker: mysql (nome do serviço)
# Local: localhost
DB_HOST=localhost

# Porta do MySQL
DB_PORT=3306

# Usuário do banco
DB_USER=root

# Senha do banco
DB_PASSWORD=sua_senha_aqui

# Nome do banco de dados
DB_NAME=blog_db
```

### JWT (Autenticação)
```env
# Chave secreta para assinar tokens JWT
# IMPORTANTE: Use uma chave forte com mínimo 32 caracteres
# Gere com: openssl rand -base64 32
JWT_SECRET=sua-chave-super-secreta-aqui-minimo-32-caracteres

# Tempo de expiração do token em segundos
# 86400 = 24 horas
JWT_EXPIRES_IN=86400
```

### CORS
```env
# Origem permitida para requisições CORS
# Desenvolvimento: http://localhost:3000
# Produção: https://seu-dominio.com
# Permitir todas: * (não recomendado em produção)
CORS_ORIGIN=http://localhost:3000
```

**Observação**: No Docker, essas variáveis são definidas no `docker-compose.yml` e sobrescrevem o `.env`.

## Rotas da API

### Base URL
```
http://localhost:8000
```

### Health Check

#### `GET /health`
Verifica se a API está rodando.

**Resposta**:
```json
{
  "status": "ok",
  "message": "API Running",
  "timestamp": "2024-01-15 10:30:00"
}
```

---

### Autenticação

#### `POST /auth/register`
Registra um novo usuário.

**Body**:
```json
{
  "name": "João Silva",
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Validações**:
- `name`: obrigatório, mínimo 3 caracteres
- `email`: obrigatório, formato de email válido
- `password`: obrigatório

**Resposta (201)**:
```json
{
  "message": "User registered successfully",
  "user": {
    "id": "uuid-aqui",
    "name": "João Silva",
    "email": "joao@example.com"
  }
}
```

**Erros**:
- `422`: Validação falhou
- `409`: Email já cadastrado

---

#### `POST /auth/login`
Autentica um usuário e retorna um token JWT.

**Body**:
```json
{
  "email": "joao@example.com",
  "password": "senha123"
}
```

**Validações**:
- `email`: obrigatório
- `password`: obrigatório

**Resposta (200)**:
```json
{
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": "uuid-aqui",
    "name": "João Silva",
    "email": "joao@example.com"
  }
}
```

**Erros**:
- `401`: Credenciais inválidas
- `422`: Validação falhou

---

### Posts (Públicas)

#### `GET /posts`
Lista todos os posts.

**Resposta (200)**:
```json
[
  {
    "id": "uuid-post",
    "title": "Meu Primeiro Post",
    "content": "Conteúdo do post...",
    "author_id": "uuid-autor",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  }
]
```

---

#### `GET /posts/{id}`
Obtém um post específico.

**Parâmetros**:
- `id`: UUID do post

**Resposta (200)**:
```json
{
  "id": "uuid-post",
  "title": "Meu Primeiro Post",
  "content": "Conteúdo do post...",
  "author_id": "uuid-autor",
  "created_at": "2024-01-15T10:30:00Z",
  "updated_at": "2024-01-15T10:30:00Z"
}
```

**Erros**:
- `404`: Post não encontrado

---

### Posts (Protegidas - Requer Autenticação)

**Header obrigatório**:
```
Authorization: Bearer {seu-token-jwt}
```

#### `POST /posts`
Cria um novo post.

**Body**:
```json
{
  "title": "Título do Post",
  "content": "Conteúdo completo do post..."
}
```

**Validações**:
- `title`: obrigatório, mínimo 3 caracteres
- `content`: obrigatório, mínimo 10 caracteres

**Resposta (201)**:
```json
{
  "message": "Post created successfully",
  "post": {
    "id": "uuid-post",
    "title": "Título do Post",
    "content": "Conteúdo completo do post...",
    "author_id": "uuid-autor",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T10:30:00Z"
  }
}
```

**Erros**:
- `401`: Token ausente ou inválido
- `422`: Validação falhou

---

#### `PUT /posts/{id}`
Atualiza um post existente.

**Parâmetros**:
- `id`: UUID do post

**Body**:
```json
{
  "title": "Novo Título",
  "content": "Novo conteúdo..."
}
```

**Validações**:
- `title`: opcional, se presente mínimo 3 caracteres
- `content`: opcional, se presente mínimo 10 caracteres

**Resposta (200)**:
```json
{
  "message": "Post updated successfully",
  "post": {
    "id": "uuid-post",
    "title": "Novo Título",
    "content": "Novo conteúdo...",
    "author_id": "uuid-autor",
    "created_at": "2024-01-15T10:30:00Z",
    "updated_at": "2024-01-15T15:30:00Z"
  }
}
```

**Erros**:
- `401`: Token ausente ou inválido
- `403`: Usuário não é o autor do post
- `404`: Post não encontrado
- `422`: Validação falhou

---

#### `DELETE /posts/{id}`
Deleta um post.

**Parâmetros**:
- `id`: UUID do post

**Resposta (200)**:
```json
{
  "message": "Post deleted successfully"
}
```

**Erros**:
- `401`: Token ausente ou inválido
- `403`: Usuário não é o autor do post
- `404`: Post não encontrado

---

## Autenticação JWT

Para acessar rotas protegidas, inclua o token JWT no header:

```bash
curl -H "Authorization: Bearer SEU_TOKEN_AQUI" http://localhost:8000/posts
```

**Exemplo completo**:

1. Registrar usuário:
```bash
curl -X POST http://localhost:8000/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"João","email":"joao@test.com","password":"123456"}'
```

2. Fazer login:
```bash
curl -X POST http://localhost:8000/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"joao@test.com","password":"123456"}'
```

3. Criar post (usando o token recebido):
```bash
curl -X POST http://localhost:8000/posts \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..." \
  -d '{"title":"Meu Post","content":"Conteúdo do post"}'
```

## Estrutura do Projeto

```
backend/
├── src/
│   ├── Core/                 # Value Objects e Exceções
│   │   ├── Exceptions/
│   │   └── ValueObjects/
│   ├── Domain/              # Camada de Domínio
│   │   ├── Entities/       # User, Post
│   │   ├── Repositories/   # Interfaces
│   │   └── UseCases/       # Lógica de negócio
│   └── Infra/              # Infraestrutura
│       ├── Auth/           # JWT Manager
│       ├── Database/       # PDO, Repositories
│       ├── Env/            # Validação de variáveis
│       └── Http/           # Controllers, Router
├── vendor/                  # Dependências Composer
├── .env.example            # Exemplo de variáveis
├── .env                    # Variáveis de ambiente (não versionado)
├── composer.json           # Dependências PHP
├── docker-compose.yml      # Configuração Docker
├── Dockerfile              # Build da aplicação
└── README.md              # Este arquivo
```

### Arquitetura de Repositórios

Os repositórios utilizam métodos semânticos separados para operações de criação e atualização:

- `create(Entity $entity)`: Persiste uma nova entidade no banco de dados
- `update(Entity $entity)`: Atualiza uma entidade existente no banco de dados

Esta separação garante:
- Queries SQL otimizadas para cada operação
- Maior clareza semântica no código
- Evita workarounds relacionados a limitações do PDO com prepared statements nativos

## Scripts Composer

```bash
# Iniciar servidor de desenvolvimento
composer server

# Verificar code style
composer cs-check

# Corrigir code style automaticamente
composer cs-fix
```

## Troubleshooting

### Erro: "Database connection failed"

**Solução com Docker**:
```bash
# Verificar se MySQL está saudável
docker-compose ps mysql

# Reiniciar serviços
docker-compose restart
```

**Solução sem Docker**:
- Verifique se o MySQL está rodando
- Confirme credenciais no `.env`
- Teste conexão: `mysql -h localhost -u root -p`

### Erro: "Unable to read environment file"

**Solução**:
- Com Docker: ignore, as variáveis vêm do `docker-compose.yml`
- Sem Docker: verifique se o arquivo `.env` existe na raiz do backend

### Erro: "Route not found"

**Solução**:
- Verifique se a rota está corretamente escrita
- Confira o método HTTP (GET, POST, PUT, DELETE)
- Veja logs: `docker-compose logs -f backend`

### Erro CORS

**Solução**:
- Ajuste `CORS_ORIGIN` no `.env` ou `docker-compose.yml`
- Para desenvolvimento: `CORS_ORIGIN=*`
- Para produção: `CORS_ORIGIN=https://seu-frontend.com`

## Licença

MIT
