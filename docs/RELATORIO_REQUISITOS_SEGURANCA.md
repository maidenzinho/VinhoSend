# Relatório de requisitos de segurança - VinhoSend Marketplace

## 1. Visão geral

O VinhoSend é um marketplace de vinhos com cadastro de usuários, login, gestão de vinhos, criação de anúncios, reserva de compras e persistência em banco de dados. O sistema possui múltiplos usuários e separa os dados por proprietário, vendedor e comprador.

Entidade central: **Vinho**. Entidades de apoio: **AnuncioVinho** e **Compra**.

## 2. Requisitos derivados de DFD/STRIDE

### RQ-01 - Proteção contra falsificação de requisições - STRIDE: Spoofing/Tampering

**Risco:** um atacante poderia tentar enviar formulários sem passar pela interface legítima do sistema.

**Implementação:** todos os formulários críticos usam token CSRF gerado com `random_bytes(32)` e validado com `hash_equals`.

**Arquivos:**

- `config/seguranca.php`
- `controladores/salvar_vinho.php`
- `controladores/excluir_vinho.php`
- `controladores/salvar_anuncio.php`
- `controladores/pausar_anuncio.php`
- `controladores/comprar_anuncio.php`

### RQ-02 - Controle de autorização por dono do recurso - STRIDE: Elevation of Privilege

**Risco:** um usuário poderia tentar editar ou excluir vinho/anúncio de outro usuário alterando IDs na URL ou no formulário.

**Implementação:** operações de vinho usam `id` junto com `usuario_id`. Operações de anúncio usam `id` junto com `vendedor_id`. Compra bloqueia compra do próprio anúncio.

**Arquivos:**

- `repositorios/VinhoRepositorio.php`
- `repositorios/AnuncioRepositorio.php`
- `repositorios/CompraRepositorio.php`
- `controladores/salvar_anuncio.php`
- `controladores/comprar_anuncio.php`

### RQ-03 - Auditoria de ações relevantes - STRIDE: Repudiation

**Risco:** usuários poderiam negar ações relevantes feitas no sistema, como criar vinho, publicar anúncio ou reservar compra.

**Implementação:** ações importantes são registradas na tabela `auditoria`, com usuário, ação, detalhes, IP e data.

**Arquivos:**

- `repositorios/AuditoriaRepositorio.php`
- `database/vinhosend_unico.sql`
- `controladores/salvar_vinho.php`
- `controladores/excluir_vinho.php`
- `controladores/salvar_anuncio.php`
- `controladores/pausar_anuncio.php`
- `controladores/comprar_anuncio.php`

## 3. Requisitos de segurança geral / codificação segura

### RQ-04 - Senhas armazenadas com hash seguro - OWASP ASVS / OWASP Cheat Sheet

**Risco:** vazamento do banco poderia expor senhas em texto claro.

**Implementação:** a senha é armazenada com `password_hash` e verificada com `password_verify`.

**Arquivos:**

- `controladores/cadastrar_usuario.php`
- `controladores/entrar.php`
- `repositorios/UsuarioRepositorio.php`

### RQ-05 - Consultas parametrizadas contra SQL Injection - OWASP / CWE-89

**Risco:** entrada do usuário poderia alterar comandos SQL.

**Implementação:** todos os acessos ao banco usam PDO com `prepare` e `execute` parametrizado. `PDO::ATTR_EMULATE_PREPARES` está desativado.

**Arquivos:**

- `config/Conexao.php`
- `repositorios/UsuarioRepositorio.php`
- `repositorios/VinhoRepositorio.php`
- `repositorios/AnuncioRepositorio.php`
- `repositorios/CompraRepositorio.php`
- `repositorios/AuditoriaRepositorio.php`

### RQ-06 - Validação e escape de dados - OWASP / CWE-20 / CWE-79

**Risco:** dados inválidos ou HTML/JavaScript poderiam ser persistidos ou exibidos de forma perigosa.

**Implementação:** entradas passam por `Validador`, com limites de tamanho, tipo, preço, quantidade e status. Saídas são exibidas com `escapar`, usando `htmlspecialchars`.

**Arquivos:**

- `servicos/Validador.php`
- `config/seguranca.php`
- `painel.php`
- `marketplace.php`
- `meus_anuncios.php`
- `minhas_compras.php`

## 4. Requisitos livres de segurança

### RQ-07 - Criptografia de dados sensíveis textuais

**Risco:** descrições privadas, observações de venda ou dados de entrega poderiam ser lidos diretamente no banco.

**Implementação:** o sistema usa AES-256-GCM em `CriptografiaServico`. A descrição do vinho, observações do anúncio e endereço/ponto de entrega são cifrados antes de salvar e decifrados apenas na leitura autorizada.

**Arquivos:**

- `servicos/CriptografiaServico.php`
- `repositorios/VinhoRepositorio.php`
- `repositorios/AnuncioRepositorio.php`
- `repositorios/CompraRepositorio.php`
- `tests/CriptografiaServicoTest.php`

### RQ-08 - Bloqueio após tentativas inválidas de login

**Risco:** tentativa automatizada de adivinhar senha.

**Implementação:** o sistema registra tentativas de login e bloqueia temporariamente o usuário após o limite configurado.

**Arquivos:**

- `config/config.php`
- `controladores/entrar.php`
- `repositorios/UsuarioRepositorio.php`

### RQ-09 - Sessão segura

**Risco:** roubo ou abuso de sessão.

**Implementação:** sessão inicializada com configuração segura de cookies, `HttpOnly`, `SameSite=Lax`, regeneração de ID e controle por login.

**Arquivos:**

- `config/sessao.php`
- `config/seguranca.php`

### RQ-10 - Regras de negócio de marketplace protegidas

**Risco:** compra do próprio anúncio, estoque negativo ou reserva acima da quantidade disponível.

**Implementação:** a compra usa transação, `SELECT ... FOR UPDATE`, valida comprador diferente do vendedor e reduz estoque de forma controlada. Quando o estoque chega a zero, o anúncio muda para vendido.

**Arquivos:**

- `repositorios/CompraRepositorio.php`
- `repositorios/AnuncioRepositorio.php`
- `controladores/comprar_anuncio.php`
- `tests/MarketplaceTest.php`

## 5. Criptografia

O módulo de criptografia fica em `servicos/CriptografiaServico.php` e usa AES-256-GCM. O fluxo técnico é:

1. Normaliza a chave de aplicação.
2. Gera IV aleatório com `random_bytes`.
3. Cifra o conteúdo com `openssl_encrypt`.
4. Armazena IV, tag e texto cifrado codificados em Base64.
5. Na leitura, separa os componentes e chama `openssl_decrypt`.

Uso implementado:

- descrição do vinho;
- observações do anúncio;
- endereço ou ponto de retirada informado na reserva de compra.

## 6. Testes e GitHub Actions

Os testes ficam em `tests/` e podem ser executados com:

```bash
php tests/run.php
```

O workflow fica em `.github/workflows/tests.yml` e executa:

- verificação de sintaxe PHP;
- testes automatizados.

## 7. Demonstração recomendada

1. Mostrar `database/vinhosend_unico.sql` com as tabelas `usuarios`, `vinhos`, `anuncios_vinhos`, `compras` e `auditoria`.
2. Cadastrar dois usuários.
3. Usuário A cadastra vinho e cria anúncio.
4. Usuário B entra no marketplace e reserva compra.
5. Mostrar que o usuário B não consegue comprar anúncio próprio.
6. Mostrar `CompraRepositorio.php` com transação e controle de estoque.
7. Mostrar `CriptografiaServico.php` e testes.
8. Mostrar GitHub Actions verde.
