#!/bin/bash

#### REDIS INSTALL START ####
sudo add-apt-repository ppa:mc3man/trusty-media
sudo apt-get update
sudo apt-get dist-upgrade
sudo apt-get install ffmpeg
sudo add-apt-repository ppa:chris-lea/redis-server
sudo apt-get update
sudo apt-get install redis-server
sudo service redis-server start
#### REDIS INSTALL END ####

#### NGINX INSTALL START ####
cd ~
mkdir nginx
cd nginx
#For complier and git
sudo apt-get install git gcc make
#For the Http rewrite module which requires the PCRE library
sudo apt-get install libpcre3-dev 
#For SSL modules
sudo apt-get install libssl-dev
git clone https://github.com/arut/nginx-rtmp-module
wget http://nginx.org/download/nginx-1.4.3.tar.gz
tar zxpvf nginx-1.4.3.tar.gz
cd nginx-1.4.3
./configure --add-module=/home/streamtube/nginx/nginx-rtmp-module/ --with-http_ssl_module --prefix=/usr/local/nginx-streaming/
sudo make
sudo make install
cd /usr/local/nginx-streaming/conf
mv nginx.conf nginx.conf.bkp
wget http://aravinth.net/nginx.conf
sudo cp /home/streamtube/nginx/nginx-rtmp-module/stat.xsl /var/www/
sudo apt-get update
sudo apt-get install nginx
sudo /usr/local/nginx-streaming/sbin/nginx

#### NGINX INSTALL END ####

### Project Configure Start #####
cd ~/.ssh/
# ssh key for laravel code
ssh-keygen
cat streamtube.pub
### Project Configure END #####
