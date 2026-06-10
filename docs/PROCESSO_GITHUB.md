# Processo recomendado de Git e GitHub

## Branches recomendadas

```bash
git checkout -b develop
git add .
git commit -m "estrutura mvc e persistencia inicial"

git checkout -b feature/seguranca-criptografia
git add .
git commit -m "adiciona criptografia aes gcm para descricao dos vinhos"

git checkout develop
git merge feature/seguranca-criptografia

git checkout -b feature/testes-actions
git add .
git commit -m "adiciona testes unitarios e github actions"

git checkout develop
git merge feature/testes-actions

git checkout main
git merge develop
git push origin main
```

## Observação

O ideal é que os commits sejam feitos ao longo do desenvolvimento. Caso o projeto já esteja pronto, ainda assim mantenha mensagens honestas e organizadas, sem criar histórico falso demais.
