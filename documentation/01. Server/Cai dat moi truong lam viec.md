### Install Ubuntu 14.04 & add repository


```
cd /tmp
sudo su 
```

Oracle JDK repository:
```
add-apt-repository ppa:webupd8team/java
```

Hipchat repository:
```
echo "deb http://downloads.hipchat.com/linux/apt stable main" > \
/etc/apt/sources.list.d/atlassian-hipchat.list
wget -O - https://www.hipchat.com/keys/hipchat-linux.key | apt-key add -
```

IBUS Unikey repository:
```
add-apt-repository ppa:ubuntu-vn/ppa
```

NGINX repository:
Please download <this key>[http://nginx.org/keys/nginx_signing.key] from our web site, and add it to the apt program keyring with the following command:
```
cd /tmp
wget http://nginx.org/keys/nginx_signing.key
apt-key add nginx_signing.key
```
Append the following to the end of the ```/etc/apt/sources.list``` file
```
 ##Nginx
deb http://nginx.org/packages/ubuntu/ trusty nginx
deb-src http://nginx.org/packages/ubuntu/ trusty nginx
```

HHVM repository:
```
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0x5a16e7281be7a449
```

MariaDB repository:
```
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
add-apt-repository 'deb http://kartolo.sby.datautama.net.id/mariadb/repo/5.5/ubuntu trusty main'
```

Varnish repository:
```
curl https://repo.varnish-cache.org/GPG-key.txt | apt-key add -
echo "deb https://repo.varnish-cache.org/ubuntu/ trusty varnish-4.0" >> /etc/apt/sources.list.d/varnish-cache.list
```

Update & upgrade:
```
sudo apt-get -y update
sudo apt-get -y upgrade
```

### Install tools

```
apt-get install -y git gitk
```

### Install Java

```
apt-get install -y python-software-properties
apt-get install -y default-jre
apt-get install -y default-jdk
apt-get install -y oracle-java7-installer
```

See: [https://www.digitalocean.com/community/tutorials/how-to-install-java-on-ubuntu-with-apt-get]

### Install Eclipse PDT

- Download at [https://eclipse.org/pdt/]


### Install Chrome

- Download at [http://www.google.com/chrome]


### Install HipChat

```
apt-get install hipchat 
```

See: [https://www.hipchat.com/downloads]


### Install Ibus-Unikey

```
apt-get install ibus-unikey
```

See: [http://topthuthuat.com/thu-thuat-ubuntu/cach-cai-dat-bo-go-tieng-viet-ibus-tren-ubuntu]

### Install Nginx

```
apt-get install nginx
```

See: [http://nginx.org/en/linux_packages.html#stable]

### Install HHVM

```
apt-get install software-properties-common
apt-get install apt-transport-https
apt-get install hhvm
/usr/share/hhvm/install_fastcgi.sh
/etc/init.d/hhvm restart
update-rc.d hhvm defaults
/usr/bin/update-alternatives --install /usr/bin/php php /usr/bin/hhvm 60
```

See: [https://github.com/facebook/hhvm/wiki/Prebuilt-packages-on-Ubuntu-14.04]

### Install MariaDB

```
apt-get install -y mariadb-server
```

See: [https://downloads.mariadb.org/mariadb/repositories/#mirror=datautama]

```
/usr/bin/mysql_secure_installation
```

### Install Varnish

```
apt-get install -y varnish
```

See: [https://www.varnish-cache.org/installation/ubuntu]

### Install Redis

```
apt-get install -y build-essential
apt-get install -y tcl8.5
wget http://download.redis.io/releases/redis-stable.tar.gz
tar xzf redis-stable.tar.gz
cd redis-stable
make
make test
make install
cd utils
./install_server.sh
service redis_6379 start
update-rc.d redis_6379 defaults
```

### Install Solr

```
apt-get -y install openjdk-7-jdk
mkdir /usr/java
ln -s /usr/lib/jvm/java-7-openjdk-amd64 /usr/java/default
apt-get -y install solr-tomcat
```

Configuring a schema.xml for Solr:
```
cd /usr/share/solr
rm -R data
nano conf/schema.xml
```
Paste your own schema.xml in here.

```
service tomcat6 restart
```

See: [https://www.digitalocean.com/community/tutorials/how-to-install-solr-on-ubuntu-14-04]

### Install Beanstalkd

```
apt-get install -y beanstalkd
nano /etc/default/beanstalkd
```

After opening the file, scroll down to the bottom and find the line ```#START=yes```. Change it to:

```
START=yes
```

Managing The Service:
```
service beanstalkd start
service beanstalkd stop
service beanstalkd restart
service beanstalkd status
```

See: [https://www.digitalocean.com/community/tutorials/how-to-install-and-use-beanstalkd-work-queue-on-a-vps]

### Install phpMyAdmin

```
exit
exit
cd ~/www
```

Download phpMyAdmin-xxx.tar.gz here: [http://www.phpmyadmin.net/home_page/downloads.php]

```
tar -zxvf phpMyAdmin-xxx.tar.gz
mv phpMyAdmin-xxx phpmyadmin
cd phpmyadmin
cp config.sample.inc.php config.inc.php
nano config.inc.php
```

Change this file to:
```
/* Authentication type */
$cfg['Servers'][$i]['auth_type'] = 'config';
/* Server parameters */
$cfg['Servers'][$i]['host'] = 'localhost';
$cfg['Servers'][$i]['username'] = 'root';
$cfg['Servers'][$i]['password'] = 'root';
```

###Install Composer

```
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

###Config varnish server

```
nano /etc/default/varnish
```

Search ```## Alternative 2, Configuration with VCL```. Change it to:
```
DAEMON_OPTS="-a :80 \
             -T localhost:6082 \
             -f /etc/varnish/default.vcl \
             -S /etc/varnish/secret \
             -s file,/var/lib/varnish/$INSTANCE/varnish_storage.bin,1G"
```

Change default.vcl file:
```
nano /etc/varnish/default.vcl
```

Paste content of this file [https://github.com/kalephan/varnish-4.0-configuration-templates/blob/master/default.vcl]

```
service varnish restart
```

