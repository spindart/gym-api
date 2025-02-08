#!/bin/bash
set -e

echo "Aguardando o MySQL iniciar..."
TIMEOUT=120  # Aumentado para 2 minutos
START_TIME=$(date +%s)

until mysqladmin ping -h db -u root -proot --silent; do
    sleep 5  # Aumentado o intervalo entre tentativas
    ELAPSED_TIME=$(( $(date +%s) - START_TIME ))
    if [ "$ELAPSED_TIME" -ge "$TIMEOUT" ]; then
        echo "Erro: Tempo limite atingido para o MySQL iniciar."
        exit 1
    fi
    echo "Ainda aguardando MySQL... ($ELAPSED_TIME segundos se passaram)"
done

echo "MySQL está pronto!"

echo "Executando migrações..."
php vendor/bin/phinx migrate || { echo "Falha ao executar migrações"; exit 1; }

echo "Populando o banco de dados..."
php vendor/bin/phinx seed:run || { echo "Falha ao popular o banco de dados"; exit 1; }

echo "Inicialização concluída com sucesso!"