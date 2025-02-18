#!/bin/sh
set -e

echo "Aguardando o MySQL iniciar..."
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --silent; do
    sleep 1
done

echo "MySQL está pronto!"

echo "Executando migrações..."
cd /var/www
php vendor/bin/phinx migrate -e development

echo "Populando o banco de dados..."
php vendor/bin/phinx seed:run -e development

echo "Iniciando PHP-FPM..."
php-fpm


php vendor/bin/phinx seed:run || { echo "Falha ao popular o banco de dados"; exit 1; }

echo "Inicialização concluída com sucesso!"