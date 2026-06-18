# Feira Digital

Marketplace de produtores locais desenvolvido como projeto de extensão da disciplina Programação Web II (Unidavi).

A plataforma conecta produtores rurais e artesanais a consumidores locais, permitindo o cadastro de produtos, gestão de catálogo e navegação pública sem necessidade de conta.

**Stack:** Laravel 11 · Blade SSR · SCSS · Vite · MySQL 8 · Laravel Sail (Docker) · Laravel Breeze

---

## Pré-requisitos

- Docker Engine 24 ou superior
- Docker Compose v2
- Git
- Make (opcional, mas recomendado)

---

## Instalação e configuração

Clone o repositório e entre no diretório:

```bash
git clone <url-do-repositorio>
cd projeto-extensao-pw2
```

Execute o setup completo com um único comando:

```bash
make install
```

Esse comando faz tudo na ordem correta: copia o `.env`, instala as dependências PHP via Docker (sem precisar de PHP local), sobe os containers, gera a chave da aplicação, roda migrations + seeders, cria o link de storage e compila os assets.

> **Nota:** o `.env.example` já vem pré-configurado para o ambiente Docker local. Se precisar ajustar alguma variável (ex: credenciais externas), edite o `.env` gerado antes de rodar `make install`.

---

## Executando o projeto

Suba os containers em segundo plano:

```bash
./vendor/bin/sail up -d
```

Para desenvolvimento com recompilação automática de assets:

```bash
./vendor/bin/sail npm run dev
```

---

## Acessando a aplicação

| Servico    | URL                    |
|------------|------------------------|
| Aplicacao  | http://localhost       |
| Mailpit    | http://localhost:8025  |

O Mailpit captura todos os e-mails enviados pela aplicacao (confirmacao de cadastro, recuperacao de senha, etc.) sem encaminhar para destinatarios reais.

---

## Comandos Make disponíveis

Execute `make` ou `make help` para listar todos os comandos. Os principais sao:

**Containers**

| Comando          | O que faz                                            |
|------------------|------------------------------------------------------|
| `make up`        | Sobe os containers em background                     |
| `make down`      | Derruba os containers                                |
| `make restart`   | Derruba e sobe novamente (util apos trocar o .env)   |
| `make logs`      | Acompanha os logs em tempo real (Ctrl+C para sair)   |
| `make bash`      | Abre shell dentro do container da aplicacao          |

**Banco de dados**

| Comando          | O que faz                                            |
|------------------|------------------------------------------------------|
| `make migrate`   | Roda as migrations pendentes                         |
| `make seed`      | Popula o banco com os seeders (categorias, etc.)     |
| `make fresh`     | Recria o banco do zero e roda seed — destroi dados locais |

**Setup inicial**

| Comando          | O que faz                                                              |
|------------------|------------------------------------------------------------------------|
| `make install`   | Setup completo após clonar (composer, up, key, migrate, seed, assets)  |
| `make setup`     | migrate + seed + storage:link em sequencia                             |
| `make storage`   | Cria o symlink public/storage -> storage/app/public                    |

**Assets**

| Comando            | O que faz                                          |
|--------------------|----------------------------------------------------|
| `make npm-install` | Instala as dependencias JS                         |
| `make npm-dev`     | Inicia o Vite em modo watch para desenvolvimento   |
| `make npm-build`   | Gera os assets para producao                       |

**Artisan e testes**

| Comando                        | O que faz                                     |
|--------------------------------|-----------------------------------------------|
| `make routes`                  | Lista todas as rotas registradas              |
| `make test`                    | Roda a suite de testes PHPUnit                |
| `make tinker`                  | Abre o REPL do Laravel                        |
| `make clear`                   | Limpa todos os caches (config, view, route, app) |
| `make artisan CMD="<comando>"` | Executa qualquer comando artisan avulso       |
| `make composer CMD="<comando>"`| Executa qualquer comando composer avulso      |

---

## Estrutura de pastas

```
app/                  Models, Controllers, Requests, Policies
resources/views/      Templates Blade (layouts, paginas, componentes)
resources/scss/       Estilos SCSS organizados por modulo
database/             Migrations, seeders e factories
docs/                 Requisitos, arquitetura e documentacao das sprints
```

---

## Documentacao adicional

Veja a pasta `docs/` para requisitos, arquitetura e sprints.
