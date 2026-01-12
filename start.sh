#!/bin/bash
# echo "--- Ejecutando Migraciones ---"
# php artisan migrate --force  <-- LE PONEMOS UN # AL PRINCIPIO PARA QUE NO SE EJECUTE

echo "--- Iniciando Servidor ---"
php artisan serve --host=0.0.0.0 --port=$PORT
