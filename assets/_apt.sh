#which php

sudo apt install php-dev
sudo apt-get install php-pear
sudo pecl install trader

php -i|grep 'php.ini'
sudo sh -c 'echo "extension=trader.so" > /etc/php/8.2/cli/php.ini"

install php-bc