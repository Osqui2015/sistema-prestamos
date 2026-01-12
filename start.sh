#!/bin/bash
echo "--- Ejecutando Migraciones ---"
php artisan migrate:fresh --force

echo "--- Iniciando Servidor ---"
php artisan serve --host=0.0.0.0 --port=$PORT
