# Roteiro rápido para defesa - VinhoSend Marketplace

## 1. Apresentação do sistema

O VinhoSend é um marketplace de vinhos. Cada usuário pode cadastrar seus próprios vinhos, publicar anúncios de venda e comprar/reservar anúncios publicados por outros usuários.

## 2. Fluxo funcional para demonstrar

1. Abrir a tela inicial.
2. Criar o usuário vendedor.
3. Fazer login como vendedor.
4. Cadastrar um vinho em **Meus Vinhos**.
5. Ir em **Vender** e publicar um anúncio com preço e quantidade.
6. Sair.
7. Criar ou entrar com outro usuário comprador.
8. Abrir **Marketplace**.
9. Reservar a compra do anúncio.
10. Abrir **Minhas Compras**.
11. Voltar ao vendedor e mostrar a reserva recebida.

## 3. Pontos técnicos para mostrar no código

- MVC/equivalente: `modelos`, `controladores`, `repositorios`, `servicos` e telas.
- Entidade central: `modelos/Vinho.php`.
- Marketplace: `modelos/AnuncioVinho.php`, `modelos/Compra.php`, `marketplace.php`, `meus_anuncios.php`.
- Persistência: `database/vinhosend_unico.sql`.
- Autorização: `VinhoRepositorio.php`, `AnuncioRepositorio.php`, `CompraRepositorio.php`.
- Criptografia: `servicos/CriptografiaServico.php`.
- Testes: `tests/`.
- GitHub Actions: `.github/workflows/tests.yml`.

## 4. Explicação curta da criptografia

O sistema usa AES-256-GCM para proteger informações textuais sensíveis antes de salvar no banco. A descrição do vinho, as observações do anúncio e o endereço de entrega são cifrados. Na leitura, o sistema decifra apenas para o usuário autorizado.

## 5. Explicação curta dos testes

Os testes validam regras de senha, validação de dados, criptografia, autenticação e regras do marketplace. O GitHub Actions executa esses testes automaticamente quando há push.
