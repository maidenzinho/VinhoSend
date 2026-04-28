# VinhoSend — Trabalho com PHP, MySQL, HTML, CSS e JS

Sistema mantido como **VinhoSend**, agora com os requisitos do Trabalho 2 RA2 implementados em PHP + MySQL para rodar no XAMPP.

## O que este projeto tem

- Tela inicial original do VinhoSend.
- Cadastro de usuário com validação.
- Login com senha armazenada por hash.
- Bloqueio temporário após 5 tentativas inválidas.
- Sessão PHP com cookie `HttpOnly` e `SameSite=Lax`.
- CRUD principal de vinhos.
- Banco MySQL real.
- Código organizado em MVC.
- 4+ Design Patterns aplicados.
- Requisitos de segurança documentados.
- STRIDE e DFDs em imagens.
- README com instalação, banco, requisitos e trechos de código.

---

## Como conectar pelo XAMPP

### 1. Coloque o projeto na pasta do XAMPP

Copie a pasta do projeto para:

```text
C:\xampp\htdocs\VinhoSend
```

A estrutura deve ficar assim:

```text
C:\xampp\htdocs\VinhoSend\index.html
C:\xampp\htdocs\VinhoSend\login.php
C:\xampp\htdocs\VinhoSend\registro.php
C:\xampp\htdocs\VinhoSend\painel.php
```

### 2. Ligue o Apache e o MySQL

Abra o **XAMPP Control Panel** e clique em:

```text
Start Apache
Start MySQL
```

### 3. Configure o banco

Abra:

```text
http://localhost/phpmyadmin
```

Depois importe o arquivo:

```text
database/schema.sql
```

Ou acesse direto no navegador:

```text
http://localhost/VinhoSend/instalar_banco.php
```

Esse arquivo cria automaticamente:

```text
Banco: vinhosend_ra2
Tabelas: usuarios, vinhos, auditoria
```

### 4. Confira a conexão

O arquivo de conexão está em:

```text
config/config.php
```

Configuração padrão para XAMPP:

```php
const DB_HOST = '127.0.0.1';
const DB_PORTA = '3306';
const DB_NOME = 'vinhosend_ra2';
const DB_USUARIO = 'root';
const DB_SENHA = '';
```

No XAMPP padrão, o usuário é `root` e a senha fica vazia.

### 5. Acesse o site

```text
http://localhost/VinhoSend/index.html
```

---

## Prints / telas

### Tela inicial

![Tela inicial](docs/imagens/tela-inicial.png)

### Cadastro de usuário

![Cadastro](docs/imagens/tela-cadastro.png)

### Login

![Login](docs/imagens/tela-login.png)

### CRUD de vinhos

![CRUD](docs/imagens/tela-crud.png)

---

## Estrutura de arquivos

```text
VinhoSend/
├── assets/
│   ├── logo-taca-branca.svg
│   └── wine-hero.png
├── config/
│   ├── Conexao.php
│   ├── config.php
│   ├── seguranca.php
│   └── sessao.php
├── controladores/
│   ├── cadastrar_usuario.php
│   ├── entrar.php
│   ├── excluir_vinho.php
│   ├── sair.php
│   └── salvar_vinho.php
├── database/
│   └── schema.sql
├── docs/
│   └── imagens/
├── modelos/
│   ├── Usuario.php
│   └── Vinho.php
├── repositorios/
│   ├── AuditoriaRepositorio.php
│   ├── UsuarioRepositorio.php
│   └── VinhoRepositorio.php
├── servicos/
│   └── Validador.php
├── visoes/
│   └── parciais/
│       ├── cabecalho.php
│       └── rodape.php
├── index.html
├── instalar_banco.php
├── login.php
├── painel.php
├── registro.php
├── script.js
├── styles.css
└── README.md
```

---

## Banco de dados

Arquivo:

```text
database/schema.sql
```

Tabelas:

### usuarios

Guarda os usuários cadastrados.

Campos principais:

```text
id
nome
email
senha_hash
tentativas_login
bloqueado_ate
criado_em
```

### vinhos

Modelo de domínio principal do sistema.

Campos principais:

```text
id
usuario_id
nome
tipo
pais
safra
nota
descricao
criado_em
atualizado_em
```

### auditoria

Guarda ações relevantes do sistema.

Campos principais:

```text
id
usuario_id
acao
detalhes
ip
criado_em
```

---

## Requisitos de segurança implementados

## Grupo A — STRIDE

### Requisito A1 — Proteger cadastro e autenticação contra falsificação de identidade

O sistema deve autenticar usuários com senha protegida por hash e controlar falhas de login.

Referência STRIDE principal:

```text
S — Spoofing
```

DFD:

![DFD A1](docs/imagens/dfd-a1.png)

Mitigações aplicadas:

- `password_hash`
- `password_verify`
- bloqueio após 5 falhas
- sessão regenerada após login
- mensagens genéricas de erro

Arquivos relacionados:

```text
controladores/cadastrar_usuario.php
controladores/entrar.php
repositorios/UsuarioRepositorio.php
```

---

### Requisito A2 — Proteger o CRUD de vinhos contra alteração indevida

O sistema deve permitir alteração e exclusão apenas dos vinhos pertencentes ao usuário autenticado.

Referência STRIDE principal:

```text
T — Tampering
E — Elevation of Privilege
```

DFD:

![DFD A2](docs/imagens/dfd-a2.png)

Mitigações aplicadas:

- `exigir_login()`
- filtro por `usuario_id`
- consultas preparadas
- token CSRF em formulários

Arquivos relacionados:

```text
painel.php
controladores/salvar_vinho.php
controladores/excluir_vinho.php
repositorios/VinhoRepositorio.php
```

---

### Requisito A3 — Registrar ações importantes para auditoria

O sistema deve registrar cadastro, login, falha de login, logout e alterações no CRUD.

Referência STRIDE principal:

```text
R — Repudiation
```

DFD:

![DFD A3](docs/imagens/dfd-a3.png)

Mitigações aplicadas:

- tabela `auditoria`
- registro de ação, usuário, IP e horário
- auditoria de login e CRUD

Arquivo relacionado:

```text
repositorios/AuditoriaRepositorio.php
```

---

## Grupo B — Codificação segura

### B1 — Senhas armazenadas com hash seguro

Referência:

```text
OWASP ASVS V2 — Authentication
OWASP Password Storage Cheat Sheet
```

Trecho de código:

```php
$algoritmo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
$senhaHash = password_hash($senha, $algoritmo);
```

Arquivo:

```text
controladores/cadastrar_usuario.php
```

Como cumpre o requisito:

A senha nunca é salva em texto puro. O sistema salva apenas o hash gerado por algoritmo seguro.

---

### B2 — Consultas SQL com prepared statements

Referência:

```text
OWASP ASVS V5 — Validation, Sanitization and Encoding
OWASP SQL Injection Prevention Cheat Sheet
CWE-89
```

Trecho de código:

```php
$stmt = Conexao::obter()->prepare('SELECT * FROM usuarios WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
```

Arquivo:

```text
repositorios/UsuarioRepositorio.php
```

Como cumpre o requisito:

Os dados do usuário não são concatenados diretamente na SQL. Isso reduz risco de SQL Injection.

---

### B3 — Proteção contra CSRF

Referência:

```text
OWASP ASVS V4 — Access Control
OWASP CSRF Prevention Cheat Sheet
CWE-352
```

Trecho de código:

```php
function validar_csrf(): void
{
    $tokenEnviado = $_POST['csrf_token'] ?? '';
    $tokenSessao = $_SESSION[CSRF_NOME] ?? '';
    if (!hash_equals($tokenSessao, $tokenEnviado)) {
        http_response_code(403);
        exit('Requisição recusada por falha de segurança CSRF.');
    }
}
```

Arquivo:

```text
config/seguranca.php
```

Como cumpre o requisito:

Cada formulário sensível envia um token de sessão. O servidor recusa requisições sem token válido.

---

## Grupo C — Livre escolha

### C1 — Bloqueio após tentativas inválidas

Referência:

```text
CWE-307 — Improper Restriction of Excessive Authentication Attempts
```

Implementação:

```text
MAX_TENTATIVAS_LOGIN = 5
MINUTOS_BLOQUEIO_LOGIN = 15
```

Arquivo:

```text
config/config.php
controladores/entrar.php
```

---

### C2 — Saída HTML escapada contra XSS

Referência:

```text
CWE-79 — Cross-site Scripting
OWASP XSS Prevention Cheat Sheet
```

Trecho:

```php
function escapar(?string $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
```

Arquivo:

```text
config/seguranca.php
```

---

### C3 — Sessão com proteção básica

Referência:

```text
OWASP Session Management Cheat Sheet
```

Trecho:

```php
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax'
]);
```

Arquivo:

```text
config/sessao.php
```

---

### C4 — Auditoria de eventos sensíveis

Referência:

```text
NIST SP 800-53 AU-2 — Event Logging
```

Trecho:

```php
$stmt = Conexao::obter()->prepare(
    'INSERT INTO auditoria (usuario_id, acao, detalhes, ip) VALUES (?, ?, ?, ?)'
);
```

Arquivo:

```text
repositorios/AuditoriaRepositorio.php
```

---

## MVC aplicado

### Model

Responsável pelas entidades principais.

```text
modelos/Usuario.php
modelos/Vinho.php
```

### View

Responsável pelas telas.

```text
index.html
login.php
registro.php
painel.php
visoes/parciais/cabecalho.php
visoes/parciais/rodape.php
```

### Controller

Responsável pelo fluxo das ações.

```text
controladores/cadastrar_usuario.php
controladores/entrar.php
controladores/salvar_vinho.php
controladores/excluir_vinho.php
controladores/sair.php
```

### Repository / DAO

Responsável pela persistência no MySQL.

```text
repositorios/UsuarioRepositorio.php
repositorios/VinhoRepositorio.php
repositorios/AuditoriaRepositorio.php
```

---

## Design Patterns usados

### 1. Singleton

Aplicado em:

```text
config/Conexao.php
```

Uso:

A conexão PDO é centralizada em uma única instância.

---

### 2. Repository / DAO

Aplicado em:

```text
repositorios/UsuarioRepositorio.php
repositorios/VinhoRepositorio.php
repositorios/AuditoriaRepositorio.php
```

Uso:

Separa a regra de acesso ao banco do restante do sistema.

---

### 3. MVC

Aplicado em:

```text
modelos/
visoes/
controladores/
```

Uso:

Organiza o sistema em responsabilidades separadas.

---

### 4. Front Controller simplificado por ação

Aplicado em:

```text
controladores/*.php
```

Uso:

Cada arquivo controlador recebe uma ação específica do formulário, valida a requisição e redireciona.

---

### 5. Service / Validator

Aplicado em:

```text
servicos/Validador.php
```

Uso:

Centraliza validações de nome, e-mail, senha, números e textos.

---

## Como testar

### Teste 1 — Instalação do banco

Abra:

```text
http://localhost/VinhoSend/instalar_banco.php
```

Resultado esperado:

```text
Banco instalado com sucesso
```

### Teste 2 — Cadastro

Abra:

```text
http://localhost/VinhoSend/registro.php
```

Cadastre:

```text
Nome: Felipe Teste
E-mail: teste@vinhosend.com
Senha: Teste123
```

Resultado esperado:

```text
Conta criada com sucesso. Faça login para continuar.
```

### Teste 3 — Login

Abra:

```text
http://localhost/VinhoSend/login.php
```

Entre com o usuário cadastrado.

Resultado esperado:

```text
Redirecionamento para painel.php
```

### Teste 4 — CRUD

No painel:

1. Cadastre um vinho.
2. Edite o vinho.
3. Exclua o vinho.

Resultado esperado:

```text
As ações devem aparecer na tabela e persistir no MySQL.
```

### Teste 5 — Banco

No phpMyAdmin, confira:

```sql
SELECT * FROM usuarios;
SELECT * FROM vinhos;
SELECT * FROM auditoria;
```

---

## Observação importante

Este projeto não usa Python, SQLite, Node ou servidor externo.

Ele foi deixado para rodar no XAMPP com:

```text
Apache + PHP + MySQL
```

