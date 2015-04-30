FROM phusion/baseimage:0.9.16
MAINTAINER Tegan Snyder <tsnyder@tegdesign.com>

# Setup docker defaults
WORKDIR /opt

# Enable PHP 5.6 repo and update apt-get
RUN echo "deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main" >> /etc/apt/sources.list && \
    apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-key E5267A6C && \
    apt-get update

RUN apt-get install -y telnet redis-server git supervisor \
	php5 php5-cli php5-common \
	php-pear php5-dev \
	php5-curl php5-redis php5-intl \
	php5-gearman unicode curl && \
	apt-get purge -y aptitude && \
	apt-get autoremove -y && \
	apt-get clean && \
	rm -rf /usr/share/man/* && \
	rm -rf /var/lib/apt/lists/*

# Redis configuration
RUN sed -i 's/bind 127.0.0.1/bind 0.0.0.0/g' /etc/redis/redis.conf

# PHP configuration
RUN echo "date.timezone = \"America/Chicago\"" >> /etc/php5/cli/php.ini
RUN echo "memory_limit = 512M" >> /etc/php5/cli/php.ini

# Setup working directory
RUN mkdir -p /var/crawler

# Setup supervisord
RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Add main supervisor daemon
COPY bootstrap.php /var/crawler/

# Setup composer dependencies
COPY composer.json /var/crawler/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /var/crawler && composer install

# Expose Redis Port
EXPOSE 6379

# Start supervisor
CMD /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf