ls
sudo su -
cd /www/wwwroot/albagroup
git config --global --add safe.directory /www/wwwroot/albagroup
git pull origin staging
rm -f /usr/bin/php
ln -s /www/server/php/82/bin/php /usr/bin/php
composer install
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan event:clear
php artisan storage:link
php artisan migrate --force
chmod 777 storage/api-docs
chmod 777 storage/api-docs/*
npm install
npm run build
exit
