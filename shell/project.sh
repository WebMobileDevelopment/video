#!/bin/bash
eval `ssh-agent -s`
ssh-add ~/.ssh/streamtube
cd ~
cd /home/streamtube/
git clone git@bitbucket.org:streamhash/streamtube-v1.0.git
cd /home/streamtube/streamtube-v1.0/
sudo cp .env.example .env
sudo mkdir public/uploads
sudo mkdir public/uploads/videos/
sudo mkdir public/uploads/videos/original
sudo mkdir public/uploads/smil/
sudo mkdir public/uploads/images/
sudo > storage/logs/laravel.log
sudo chmod -R 777  public/ public/uploads/ bootstrap/ storage/ .env storage/logs/ storage/logs/laravel.log
sudo nano .env
sudo /bin/dd if=/dev/zero of=/var/swap.1 bs=1M count=1024
sudo /sbin/mkswap /var/swap.1
sudo /sbin/swapon /var/swap.1
sudo composer update
php artisan key:generate
php artisan config:cache
php artisan migrate
php artisan db:seed
cd ~
sudo rm -rf /home/streamtube/streamtube-v1.0/vendor/olaferlandsen
sudo cp -rf /home/streamtube/streamtube-v1.0/app/ffmpeg-custom/olaferlandsen/  /home/streamtube/streamtube-v1.0/vendor/
sudo ln -sf /home/streamtube/streamtube-v1.0/ /var/www/html/
sudo crontab -e