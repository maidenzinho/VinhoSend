# VinhoSend

VinhoSend é um marketplace simples de vinhos feito em PHP. O sistema permite cadastrar usuários, registrar vinhos, publicar anúncios, receber encomendas, acompanhar compras e gerar uma nota fiscal simples para controle da venda.

O projeto foi organizado para apresentação acadêmica: código separado em camadas, banco em SQL único, testes automatizados, workflow no GitHub Actions e requisitos de segurança documentados em PDF.

## Funcionalidades

- Cadastro de usuários.
- Login com autenticação.
- Bloqueio temporário após tentativas inválidas.
- Login obrigatório nas áreas internas.
- Cadastro, edição, listagem e exclusão de vinhos.
- Upload de foto do vinho.
- Publicação de anúncios no marketplace.
- Compra/encomenda de vinhos anunciados por outros usuários.
- Registro da forma de pagamento.
- Comentário de entrega com endereço informado pelo comprador.
- Nota fiscal simples da encomenda.
- Área do vendedor com solicitações recebidas.
- Marcação de pedido como enviado.
- Confirmação de recebimento pelo comprador.
- Páginas de Links Úteis e Contato.
- Auditoria de ações importantes.
- Testes automatizados.
- GitHub Actions para rodar lint e testes.

## Observação sobre a nota fiscal

A nota fiscal do sistema é uma nota simples de controle interno da encomenda. Ela registra número, data, comprador, vendedor, vinho, valor, forma de pagamento e endereço. Não é integração com SEFAZ e não substitui NF-e oficial.

## Entidade principal

A entidade principal é o **Vinho**.

A partir dela, o usuário pode criar um **Anúncio de Vinho** no marketplace. Outro usuário pode fazer uma **Compra/Reserva**, que gera registro no banco e uma nota fiscal simples.

## Estrutura do projeto

```text
config/             Configurações, conexão, sessão e segurança
controladores/      Ações dos formulários
modelos/            Classes principais do sistema
repositorios/       Acesso ao banco de dados
servicos/           Validação, criptografia e upload de imagens
visoes/             Partes reutilizáveis da interface
database/           SQL único do banco de dados
tests/              Testes automatizados
.github/workflows/  Workflow do GitHub Actions
uploads/            Imagens enviadas pelos usuários
docs/               PDF final dos requisitos
```

## Arquitetura

O projeto usa uma estrutura equivalente a MVC:

- **Modelos:** `modelos/`
- **Telas:** arquivos PHP principais e `visoes/`
- **Controladores:** `controladores/`
- **Persistência:** `repositorios/`
- **Serviços auxiliares:** `servicos/`

A pasta `repositorios/` funciona como Repository/DAO. Ela centraliza as consultas SQL e evita que o acesso ao banco fique espalhado pelas telas.

## Segurança implementada

O relatório completo dos requisitos está em:

```text
docs/relatorio-requisitos-seguranca.pdf
```

Controles implementados no código:

- Senha protegida com `password_hash`.
- Verificação de senha com `password_verify`.
- Login obrigatório nas páginas internas.
- Sessão com cookie HttpOnly e SameSite.
- Proteção CSRF em ações de alteração.
- Prepared statements com PDO.
- Escape de saída contra XSS.
- Validação centralizada de entrada.
- Autorização por dono do recurso.
- Upload de imagem com validação de tipo, tamanho e nome seguro.
- Auditoria de ações importantes.
- Criptografia AES-256-GCM para textos sensíveis.

## Criptografia

O módulo de criptografia fica em:

```text
servicos/CriptografiaServico.php
```

Ele usa AES-256-GCM com `openssl_encrypt` e `openssl_decrypt`. O objetivo é proteger textos sensíveis antes de gravar no banco e decifrar apenas quando o usuário autorizado precisa visualizar a informação.

A criptografia aparece principalmente em:

```text
servicos/CriptografiaServico.php
repositorios/VinhoRepositorio.php
repositorios/AnuncioRepositorio.php
repositorios/CompraRepositorio.php
tests/CriptografiaServicoTest.php
```

## Banco de dados

O projeto usa apenas um SQL:

```text
database/vinhosend_unico.sql
```

Esse arquivo cria o banco `vinhosend_ra2` e as tabelas principais:

```text
usuarios
vinhos
auditoria
anuncios_vinhos
compras
```

## Como instalar no XAMPP

1. Copie a pasta do projeto para `htdocs`.
2. Inicie Apache e MySQL no XAMPP.
3. Abra o phpMyAdmin.
4. Importe o arquivo `database/vinhosend_unico.sql`.
5. Acesse o sistema pelo navegador.

Exemplo:

```text
http://localhost/VinhoSend/
```

## Fluxo para testar manualmente

1. Criar um usuário vendedor.
2. Fazer login.
3. Cadastrar um vinho com foto.
4. Publicar esse vinho em **Vender / Meus Anúncios**.
5. Sair da conta.
6. Criar ou entrar com outro usuário comprador.
7. Abrir o **Marketplace**.
8. Encomendar o vinho.
9. Informar forma de pagamento e endereço no comentário.
10. Abrir **Minhas Compras** e visualizar a nota fiscal.
11. Voltar com o usuário vendedor.
12. Abrir **Vender / Meus Anúncios** e marcar o pedido como enviado.
13. Voltar com o comprador e confirmar recebimento.

## Testes locais

Para rodar os testes:

```bash
php tests/run.php
```

Resultado esperado:

```text
[OK] AutenticacaoTest.php
[OK] CriptografiaServicoTest.php
[OK] LoginObrigatorioTest.php
[OK] MarketplaceTest.php
[OK] RepositorySecurityTest.php
[OK] UploadImagemServicoTest.php
[OK] ValidadorTest.php

Testes executados com sucesso: 7
```

Para verificar sintaxe dos arquivos PHP no Linux:

```bash
find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

No Windows PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

## GitHub Actions

O workflow fica em:

```text
.github/workflows/tests.yml
```

Ele executa:

- conferência do SQL único;
- verificação de sintaxe PHP;
- testes automatizados com `php tests/run.php`.

## Checklist rápido de entrega

```text
[ ] docs/relatorio-requisitos-seguranca.pdf está no projeto
[ ] README.md está na raiz
[ ] database/vinhosend_unico.sql importa no phpMyAdmin
[ ] Não existe instalar_banco.php
[ ] php tests/run.php passa localmente
[ ] GitHub Actions está verde
[ ] Cadastro e login funcionam
[ ] Usuário sem login não acessa páginas internas
[ ] Cadastro de vinho com foto funciona
[ ] Publicação de anúncio funciona
[ ] Compra/encomenda funciona
[ ] Nota fiscal simples abre
[ ] Vendedor consegue marcar pedido como enviado
[ ] Comprador consegue confirmar recebimento
```

## Arquivos importantes para a defesa

```text
config/seguranca.php
config/sessao.php
controladores/entrar.php
controladores/comprar_anuncio.php
repositorios/UsuarioRepositorio.php
repositorios/VinhoRepositorio.php
repositorios/AnuncioRepositorio.php
repositorios/CompraRepositorio.php
repositorios/AuditoriaRepositorio.php
servicos/CriptografiaServico.php
servicos/UploadImagemServico.php
servicos/Validador.php
tests/run.php
.github/workflows/tests.yml
database/vinhosend_unico.sql
docs/relatorio-requisitos-seguranca.pdf
```
