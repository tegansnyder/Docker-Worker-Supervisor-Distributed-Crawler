FROM ubuntu:trusty
MAINTAINER Tegan Snyder <tsnyder@tegdesign.com>

# Enable PHP 5.6 repo and update apt-get
RUN echo "deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main" >> /etc/apt/sources.list && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-key E5267A6C && \
    apt-get update

RUN apt-get install -y git \
	php5 php5-cli php5-common \
	php5-curl php5-redis php5-intl \
	php5-gearman unicode curl && \
	apt-get purge -y aptitude && \
	apt-get autoremove -y && \
	apt-get clean && \
	rm -rf /usr/share/man/* && \
	rm -rf /var/lib/apt/lists/*

RUN echo "date.timezone = \"America/Chicago\"" >> /etc/php5/cli/php.ini
RUN echo "memory_limit = 512M" >> /etc/php5/cli/php.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /var/crawler
COPY composer.json /var/crawler/
COPY worker.php /var/crawler/

RUN cd /var/crawler && composer install

WORKDIR /var/crawler
#ENTRYPOINT ["php"]
CMD ["php", "-a"]