FROM phusion/baseimage:0.9.16
MAINTAINER Tegan Snyder <tsnyder@tegdesign.com>

# Setup docker defaults
WORKDIR /opt

RUN apt-get update
RUN apt-get upgrade -y

RUN apt-get install -y redis-server supervisor unicode

# Redis configuration
RUN sed -i 's/bind 127.0.0.1/bind 0.0.0.0/g' /etc/redis/redis.conf

# Setup supervisord
RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Redis port
EXPOSE 6379

# Start supervisor
CMD /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf