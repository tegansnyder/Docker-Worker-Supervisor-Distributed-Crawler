FROM phusion/baseimage:0.9.16

MAINTAINER moltar <moltar@moltar.net>
# https://github.com/moltar/docker.gearmand

# Setup docker defaults
WORKDIR /opt

RUN apt-get update -qq && \
    apt-get install -y gcc make g++ libboost-all-dev gperf libevent-dev \
        uuid-dev libmysqld-dev libpq-dev libsqlite3-dev libhiredis-dev \
        unzip && \
    curl -L --silent -o /tmp/cyassl.zip http://www.yassl.com/cyassl-3.2.0.zip && \
    cd /tmp && \
    unzip -qq cyassl.zip && \
    cd cyassl* && \
    ./configure \
        --prefix=/usr && \
    make && \
    make install && \
    mkdir /tmp/gearman && \
    curl -L --insecure --silent https://github.com/tegansnyder/gearmand-1.1.12-patched-for-redis/archive/1.12.12_redis_patched.tar.gz \
        | tar -xz --strip 1 -C /tmp/gearman && \
    cd /tmp/gearman && \
    ./configure \
        --prefix=/usr \
        --sysconfdir=/etc \
        --localstatedir=/var \
        --enable-hiredis && \
    make && \
    make install && \
    adduser --system --no-create-home --disabled-login --disabled-password --group gearman && \
    apt-get purge -y gcc make gperf libevent-dev uuid-dev \
        libmysqld-dev libpq-dev libsqlite3-dev libhiredis-dev unzip && \
    apt-get autoremove -y && \
    apt-get install -y telnet libmysqlclient18 libhiredis0.10 libevent-2.0-5 libpq5 && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# add startup init script
RUN mkdir -p /etc/my_init.d
ADD gearman_startup.sh /etc/my_init.d/gearman_startup.sh
RUN chmod +x /etc/my_init.d/gearman_startup.sh

EXPOSE 4730