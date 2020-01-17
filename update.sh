# cd /var/www/whatsapp.our24.ru/data/www/whatsapp.our24.ru
git pull
composer install
composer dump-autoload
# chown -R whatsapp.our24.ru:whatsapp.our24.ru /var/www/whatsapp.our24.ru/data/www/whatsapp.our24.ru