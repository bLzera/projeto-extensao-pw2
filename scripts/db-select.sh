#!/usr/bin/env bash
#
# Roda uma consulta de leitura no banco do container (docker compose).
# Uso:
#   ./scripts/db-select.sh "SELECT id, name FROM users LIMIT 5"
#
set -euo pipefail

# Sempre roda a partir da raiz do projeto, independente de onde foi chamado.
cd "$(dirname "$0")/.."

SERVICE="mysql"

# Sail usa essas vars; fora do comando `sail` elas vêm vazias e geram avisos.
export WWWUSER="${WWWUSER:-$(id -u)}"
export WWWGROUP="${WWWGROUP:-$(id -g)}"

# --- valida o argumento ---
if [[ $# -lt 1 || -z "${1// }" ]]; then
  echo "Uso: $0 \"SELECT ... FROM ...\"" >&2
  exit 1
fi
QUERY="$1"

# Guarda de segurança: só permite leitura. Comente este bloco se precisar de DDL/DML.
if [[ ! "${QUERY,,}" =~ ^[[:space:]]*(select|show|describe|desc|explain)[[:space:]] ]]; then
  echo "Recusado: este script só roda consultas de leitura (SELECT/SHOW/DESCRIBE/EXPLAIN)." >&2
  exit 1
fi

# --- lê credenciais do .env ---
get_env() { grep -E "^$1=" .env | head -1 | cut -d= -f2-; }
DB_DATABASE="$(get_env DB_DATABASE)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"

# MYSQL_PWD evita o aviso "password on the command line is insecure".
# -T desativa o pseudo-TTY (necessário para rodar não-interativo/em pipe).
docker compose exec -T \
  -e MYSQL_PWD="$DB_PASSWORD" \
  "$SERVICE" \
  mysql -u"$DB_USERNAME" --table "$DB_DATABASE" -e "$QUERY"
