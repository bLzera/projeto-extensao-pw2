# Feira Digital

Marketplace de produtores locais — Projeto de Extensão da disciplina Programação Web II (Unidavi).

**Stack:** Laravel 13 · Blade SSR · SCSS · Vite · MySQL 8 · Laravel Sail · Laravel Breeze

---

## Pré-requisitos

- Docker e Docker Compose
- Git

## Instalação rápida

```bash
git clone <repo-url>
cd projeto-extensao-pw2
cp .env.example .env
./vendor/bin/sail up -d
make setup        # migrate + seed + storage:link
make npm-install
make npm-build
```

## Comandos úteis

| Comando          | O que faz                          |
|------------------|------------------------------------|
| `make up`        | Sobe os containers                 |
| `make down`      | Para os containers                 |
| `make migrate`   | Roda as migrations                 |
| `make seed`      | Roda os seeders                    |
| `make fresh`     | Recria o banco e roda seed         |
| `make storage`   | Cria o link `public/storage`       |
| `make setup`     | migrate + seed + storage (pós-clone)|
| `make npm-dev`   | Inicia o Vite em modo dev          |
| `make npm-build` | Compila os assets para produção    |
| `make bash`      | Abre shell no container da app     |
| `make tinker`    | Abre o REPL do Laravel             |

## URLs

| Serviço   | URL                       |
|-----------|---------------------------|
| Aplicação | http://localhost           |
| Mailpit   | http://localhost:8025      |

## Documentação

Consulte `docs/` para requisitos, arquitetura e detalhes das sprints.
