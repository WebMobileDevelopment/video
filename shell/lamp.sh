#!/bin/bash
#Install LAMP ,PHPMyadmin and TMUX
sudo apt-get update
sudo apt-get install apache2
sudo apt-get install mysql-server php5-mysql
sudo mysql_install_db
sudo mysql_secure_installation
sudo apt-get install php5 libapache2-mod-php5 php5-mcrypt
sudo apt-get install php5-cli
sudo apt-get install php5-curl
sudo nano /etc/apache2/mods-enabled/dir.conf
sudo service apache2 restart
sudo apt-get update
sudo apt-get install phpmyadmin apache2-utils
sudo nano /etc/apache2/apache2.conf
sudo php5enmod mcrypt
sudo service apache2 restart
sudo apt-get install curl php5-cli git
sudo apt-get install libtm-perl
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
sudo a2enmod rewrite
sudo nano /etc/php5/apache2/php.ini
sudo service apache2 restart
wget http://aravinth.net/tmux.conf
mv tmux.conf ~/.tmux.conf
echo 'tm() { tmux new -s "$1" ;}' >> ~/.bashrc
echo 'ta() { tmux attach -t "$1"; }' >> ~/.bashrc
echo 'tl() { tmux list-sessions; }' >> ~/.bashrc
source ~/.bashrc