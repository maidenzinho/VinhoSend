# Correções de login obrigatório e auditoria

## Login obrigatório

As páginas e ações internas do sistema exigem usuário autenticado antes de executar qualquer operação de marketplace, compra, venda, cadastro, edição, exclusão ou logout.

Arquivos principais:

- `config/sessao.php`: função `exigir_login()` com redirecionamento correto tanto para páginas da raiz quanto para controladores.
- `painel.php`, `marketplace.php`, `meus_anuncios.php`, `minhas_compras.php`: acesso permitido somente com login.
- `controladores/salvar_vinho.php`, `excluir_vinho.php`, `salvar_anuncio.php`, `pausar_anuncio.php`, `comprar_anuncio.php`, `sair.php`: ações protegidas por sessão.

As únicas ações públicas continuam sendo criação de conta e login, porque são necessárias para iniciar o uso do sistema.

## Logout seguro

O logout foi alterado para usar `POST` com token CSRF, evitando encerramento de sessão por link direto ou requisição GET.

Arquivos alterados:

- `controladores/sair.php`
- `visoes/parciais/cabecalho.php`
- `painel.php`
- `styles.css`

## Correção do erro de chave estrangeira na auditoria

O erro abaixo ocorria quando a sessão tinha um `usuario_id` que não existia mais na tabela `usuarios`:

```text
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row
```

Foi corrigido em `repositorios/AuditoriaRepositorio.php`. Antes de inserir o registro na tabela `auditoria`, o repositório agora verifica se o usuário existe. Se não existir, registra a auditoria com `usuario_id = NULL`, respeitando a chave estrangeira.

## Teste adicionado

Foi criado o teste `tests/LoginObrigatorioTest.php`, validando que os controladores sensíveis exigem login e que o logout usa POST + CSRF.

Comando:

```bash
php tests/run.php
```

Resultado esperado:

```text
Testes executados com sucesso: 6
```
