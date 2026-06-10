# VinhoSend

VinhoSend é um marketplace acadêmico para cadastro, compra e venda de vinhos entre usuários autenticados. O projeto foi organizado para cumprir os critérios de avaliação de segurança, criptografia, versionamento, testes, arquitetura MVC e sistema funcional.

## Funcionalidades principais

- Cadastro de usuário com senha protegida por hash.
- Login com controle de tentativas e bloqueio temporário.
- Sessão segura com cookie HttpOnly, SameSite e configuração de segurança.
- Cadastro, edição, listagem e exclusão de vinhos do próprio usuário.
- Marketplace com anúncios de venda de vinhos.
- Compra/reserva de anúncios publicados por outros usuários.
- Controle de estoque do anúncio após reserva.
- Histórico de compras do comprador.
- Área do vendedor com anúncios publicados, solicitações recebidas e botão para marcar pedido como enviado.
- Auditoria de ações relevantes.
- Criptografia AES-256-GCM para dados sensíveis textuais.
- Testes automatizados e workflow do GitHub Actions.

## Entidade central do sistema

A entidade principal é o **Vinho**. A partir dela, o usuário pode criar **Anúncios de Vinho** para venda no marketplace. Outro usuário pode realizar uma **Compra/Reserva**, gerando persistência da transação.

## Estrutura MVC/equivalente

```text
config/            conexão, sessão e segurança
controladores/     ações de cadastro, login, vinho, anúncio e compra
modelos/           Usuario, Vinho, AnuncioVinho e Compra
repositorios/      acesso ao banco e regras de persistência
servicos/          validação e criptografia
visoes/            partes reutilizáveis da interface
*.php              telas principais da aplicação
database/          schema SQL
tests/             testes automatizados
.github/workflows/ GitHub Actions
```

## Segurança implementada

O projeto possui 10 requisitos de segurança documentados em `docs/relatorio-requisitos-seguranca.pdf` e `docs/RELATORIO_REQUISITOS_SEGURANCA.md`.

Controles principais:

- Hash de senha com `password_hash`.
- Verificação de senha com `password_verify`.
- Token CSRF com `random_bytes` e `hash_equals`.
- Sessão segura.
- Prepared statements com PDO.
- Validação centralizada de entrada.
- Escape de saída contra XSS.
- Controle de autorização por dono do recurso.
- Auditoria de ações importantes.
- Criptografia AES-256-GCM para descrição, observações e dados de entrega.

## Criptografia

O módulo principal fica em:

```text
servicos/CriptografiaServico.php
```

Ele usa `openssl_encrypt` e `openssl_decrypt` com AES-256-GCM. O uso aparece na persistência dos vinhos, anúncios e compras:

```text
repositorios/VinhoRepositorio.php
repositorios/AnuncioRepositorio.php
repositorios/CompraRepositorio.php
```

## Banco de dados

O projeto usa um único SQL para criar o banco inteiro:

```text
database/vinhosend_unico.sql
```

No XAMPP, abra o phpMyAdmin, clique em **Importar**, selecione esse arquivo e execute.

O SQL cria o banco `vinhosend_ra2` e as tabelas principais do sistema: usuários, vinhos, anúncios, compras e auditoria.

Não existe mais instalador PHP de banco; a instalação é feita somente pelo SQL único.

## Fluxo para demonstrar na defesa

1. Criar dois usuários.
2. Entrar com o primeiro usuário.
3. Cadastrar um vinho em **Meus Vinhos**.
4. Criar um anúncio em **Vender**.
5. Sair e entrar com o segundo usuário.
6. Abrir **Marketplace**.
7. Reservar a compra do anúncio.
8. Abrir **Minhas Compras**.
9. Voltar ao vendedor e conferir **Reservas recebidas**.
10. Voltar ao vendedor, abrir **Vender** e marcar a solicitação como enviada.
11. No comprador, abrir **Minhas Compras** e confirmar o recebimento.
12. Mostrar no código os controles de segurança, criptografia, testes e Actions.

## Testes locais

```bash
php tests/run.php
```

Resultado esperado:

```text
Testes executados com sucesso: 7
```

Verificação de sintaxe:

```bash
find . -name "*.php" -not -path "./vendor/*" -print0 | xargs -0 -n1 php -l
```

No Windows PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

## GitHub Actions

O workflow está em:

```text
.github/workflows/tests.yml
```

Ele executa verificação de sintaxe e os testes automatizados a cada push nas branches configuradas.

## Páginas principais

- `index.html` - página inicial.
- `registro.php` - cadastro de usuário.
- `login.php` - autenticação.
- `painel.php` - cadastro e gestão dos vinhos do usuário.
- `marketplace.php` - anúncios disponíveis para compra.
- `meus_anuncios.php` - criação, edição, controle de anúncios e solicitações de compra recebidas.
- `minhas_compras.php` - histórico de compras/reservas e confirmação de recebimento.

## Observação acadêmica

O projeto não processa pagamento real. A compra funciona como reserva registrada no banco, suficiente para demonstrar entidade de domínio, múltiplos usuários, persistência, regra de negócio, autorização e segurança.

## Ajustes finais da versão marketplace

Esta versão exige login nas páginas internas e nos controladores que alteram dados. A verificação fica em `config/sessao.php` e usa `exigir_login()` nas páginas protegidas.

Também foi adicionado upload de foto dos vinhos. O usuário pode anexar uma imagem ao cadastrar ou editar um vinho. O upload fica em `servicos/UploadImagemServico.php`, aceita JPG, PNG e WEBP até 2 MB e salva em `uploads/vinhos` com nome aleatório.

Para atualizar o banco antigo, abra no navegador:

```text
http://localhost/phpMyAdmin importando o arquivo database/vinhosend_unico.sql
```

Depois rode os testes:

```bash
php tests/run.php
```

Resultado esperado:

```text
Testes executados com sucesso: 7
```

Para a defesa, use o arquivo `docs/MAPA_REQUISITOS_CODIGO.md` para mostrar onde cada requisito foi implementado.
