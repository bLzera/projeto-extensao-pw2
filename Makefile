SAIL = ./vendor/bin/sail
SCRIPTS = ./scripts/

.PHONY: help up down restart logs bash tinker \
        migrate seed fresh storage setup \
        npm-install npm-dev npm-build \
        artisan composer test routes clear select

# Exibe esta ajuda. Executar sem alvo também chama este target.
help:
	@echo ""
	@echo "Feira Digital — comandos disponíveis"
	@echo "====================================="
	@echo ""
	@echo "Containers"
	@echo "  make up            Sobe os containers em background (docker compose up -d)"
	@echo "  make down          Derruba os containers"
	@echo "  make restart       Derruba e sobe novamente (útil após trocar .env)"
	@echo "  make logs          Acompanha os logs em tempo real (Ctrl+C para sair)"
	@echo "  make bash          Abre shell dentro do container da aplicação"
	@echo ""
	@echo "Banco de dados"
	@echo "  make migrate       Roda as migrations pendentes"
	@echo "  make seed          Popula o banco com os seeders (categorias, etc.)"
	@echo "  make fresh         Recria o banco do zero e roda seed — destrói dados locais"
	@echo "  make select Q=\"<query>\"  Roda uma consulta de leitura (SELECT/SHOW/DESCRIBE)"
	@echo "                     Ex: make select Q=\"SELECT id, name FROM products LIMIT 5\""
	@echo ""
	@echo "Setup inicial (rode uma vez após clonar)"
	@echo "  make install       setup completo após clonar (composer, up, migrate, seed, assets)"
	@echo "  make setup         migrate + seed + storage:link em sequência"
	@echo "  make storage       Cria o symlink public/storage → storage/app/public"
	@echo ""
	@echo "Assets"
	@echo "  make npm-install   Instala as dependências JS (primeira vez ou após package.json mudar)"
	@echo "  make npm-dev       Inicia o Vite em modo watch para desenvolvimento"
	@echo "  make npm-build     Gera os assets para produção (rodar antes de entregar)"
	@echo ""
	@echo "Artisan & testes"
	@echo "  make routes        Lista todas as rotas registradas"
	@echo "  make test          Roda a suite de testes PHPUnit"
	@echo "  make tinker        Abre o REPL do Laravel para testar queries e models"
	@echo "  make clear         Limpa todos os caches (config, view, route, app)"
	@echo "  make artisan CMD=\"<cmd>\"  Qualquer comando artisan avulso"
	@echo "                     Ex: make artisan CMD=\"make:controller FooController\""
	@echo ""
	@echo "Composer"
	@echo "  make composer CMD=\"<cmd>\"  Qualquer comando composer avulso"
	@echo "                     Ex: make composer CMD=\"require vendor/pacote\""
	@echo ""

# ─── Setup completo (primeira vez) ───────────────────────────────
install:
	@[ -f .env ] || cp .env.example .env
	@echo "→ Instalando dependências PHP via Docker..."
	docker run --rm -u "$$(id -u):$$(id -g)" \
		-v "$$(pwd):/var/www/html" -w /var/www/html \
		laravelsail/php84-composer:latest \
		composer install --ignore-platform-reqs
	@echo "→ Subindo containers..."
	$(SAIL) up -d
	@echo "→ Gerando chave da aplicação..."
	$(SAIL) artisan key:generate
	@echo "→ Banco de dados e storage..."
	$(MAKE) setup
	@echo "→ Instalando e compilando assets..."
	$(SAIL) npm install
	$(SAIL) npm run build
	@echo ""
	@echo "Pronto! Acesse http://localhost"

# ─── Containers ───────────────────────────────────────────────────
up:
	$(SAIL) up -d

down:
	$(SAIL) down

restart: down up

logs:
	$(SAIL) logs -f

bash:
	$(SAIL) shell

tinker:
	$(SAIL) artisan tinker

# ─── Banco de dados ───────────────────────────────────────────────
migrate:
	$(SAIL) artisan migrate

seed:
	$(SAIL) artisan db:seed

fresh:
	$(SAIL) artisan migrate:fresh --seed

select:
	@$(SCRIPTS)db-select.sh "$(Q)"

# ─── Setup inicial ────────────────────────────────────────────────
storage:
	$(SAIL) artisan storage:link

setup: migrate seed storage
	@echo "Ambiente pronto."

# ─── Assets ───────────────────────────────────────────────────────
npm-install:
	$(SAIL) npm install

npm-dev:
	$(SAIL) npm run dev

npm-build:
	$(SAIL) npm run build

# ─── Artisan & PHP ────────────────────────────────────────────────
artisan:
	$(SAIL) artisan $(CMD)

routes:
	$(SAIL) artisan route:list

test:
	$(SAIL) artisan test

clear:
	$(SAIL) artisan config:clear
	$(SAIL) artisan view:clear
	$(SAIL) artisan route:clear
	$(SAIL) artisan cache:clear

# ─── Composer ─────────────────────────────────────────────────────
composer:
	$(SAIL) composer $(CMD)
