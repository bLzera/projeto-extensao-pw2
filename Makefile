SAIL = ./vendor/bin/sail

.PHONY: up down restart logs bash tinker \
        migrate seed fresh storage setup \
        npm-install npm-dev npm-build

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
