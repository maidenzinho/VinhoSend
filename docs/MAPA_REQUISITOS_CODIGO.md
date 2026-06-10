# Mapa simples dos requisitos no código

Use este arquivo na apresentação para mostrar rapidamente onde cada parte está feita.

## Sistema funcionando

| Requisito | Onde está |
|---|---|
| Cadastro de usuário | `registro.php` e `controladores/cadastrar_usuario.php` |
| Login | `login.php` e `controladores/entrar.php` |
| Login obrigatório | `config/sessao.php` e chamada `exigir_login()` nas páginas internas |
| Vários usuários | tabela `usuarios` no `database/vinhosend_unico.sql` |
| Entidade principal | `modelos/Vinho.php` e tabela `vinhos` |
| Cadastro/edição/exclusão de vinho | `painel.php`, `controladores/salvar_vinho.php`, `controladores/excluir_vinho.php` |
| Foto do vinho | `servicos/UploadImagemServico.php`, `uploads/vinhos/` e coluna `vinhos.imagem` |
| Marketplace | `marketplace.php`, `modelos/AnuncioVinho.php`, `repositorios/AnuncioRepositorio.php` |
| Área do vendedor | `meus_anuncios.php` |
| Compras do usuário | `minhas_compras.php` |
| Nota fiscal | `nota_fiscal.php` e colunas `numero_nota`, `forma_pagamento`, `nota_emitida_em` na tabela `compras` |
| Links úteis | `links_uteis.php` |
| Contato | `contato.php` e `controladores/enviar_contato.php` |

## Segurança

| Requisito | Onde está |
|---|---|
| Senha com hash | `controladores/cadastrar_usuario.php` |
| Verificação de senha | `controladores/entrar.php` |
| Bloqueio por tentativas inválidas | `controladores/entrar.php` e `config/config.php` |
| CSRF nos formulários | `config/seguranca.php` |
| Prepared statements | arquivos da pasta `repositorios/` |
| Usuário só vê os próprios dados | `VinhoRepositorio.php`, `AnuncioRepositorio.php`, `CompraRepositorio.php` |
| Upload seguro | `servicos/UploadImagemServico.php` e `.htaccess` em `uploads/` |
| Auditoria | `repositorios/AuditoriaRepositorio.php` |
| Logout por POST | `controladores/sair.php` e formulário no menu |
| Escape de saída | função `escapar()` em `config/seguranca.php` |

## Criptografia

| Uso | Onde está |
|---|---|
| AES-256-GCM | `servicos/CriptografiaServico.php` |
| Descrição do vinho protegida | `repositorios/VinhoRepositorio.php` |
| Observação do anúncio protegida | `repositorios/AnuncioRepositorio.php` |
| Comentário/endereço da entrega protegido | `repositorios/CompraRepositorio.php` |

## Testes e GitHub Actions

| Requisito | Onde está |
|---|---|
| Testes unitários | pasta `tests/` |
| Runner dos testes | `tests/run.php` |
| Workflow do GitHub Actions | `.github/workflows/tests.yml` |
| SQL único | `database/vinhosend_unico.sql` |

## Organização do código

| Parte | Pasta/arquivo |
|---|---|
| Interface | páginas `.php` da raiz e `visoes/parciais/` |
| Controle | pasta `controladores/` |
| Modelo | pasta `modelos/` |
| Banco/persistência | pasta `repositorios/` |
| Serviços | pasta `servicos/` |
| Banco completo | `database/vinhosend_unico.sql` |
