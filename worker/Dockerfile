FROM phusion/baseimage:0.9.16
MAINTAINER Tegan Snyder <tsnyder@tegdesign.com>

# Setup docker defaults
WORKDIR /opt

COPY start_worker.sh /opt/

# Enable PHP 5.6 repo and update apt-get
RUN echo "deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main" >> /etc/apt/sources.list && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-key E5267A6C && \
   	apt-get update -qq && \
	apt-get install -y git \
	php5 php5-cli php5-common supervisor \
	php-pear php5-dev \
	php5-curl php5-redis php5-intl \
	php5-gearman unicode curl && \
	apt-get purge -y aptitude && \
	apt-get autoremove -y && \
	apt-get clean && \
	rm -rf /usr/share/man/* && \
	rm -rf /var/lib/apt/lists/*

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
RUN mkdir -p /var/crawler/code
COPY code/CrawlJob.php /var/crawler/code/

# Setup composer dependencies
COPY composer.json /var/crawler/
COPY worker.php /var/crawler/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /var/crawler && composer install

# Start supervisor
CMD /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf