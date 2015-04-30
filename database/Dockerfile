FROM phusion/baseimage:0.9.16
MAINTAINER Tegan Snyder <tsnyder@tegdesign.com>

# Setup docker defaults
WORKDIR /opt

# Install Percona Server, client, toolkit
RUN \
  apt-key adv --keyserver hkp://keys.gnupg.net:80 --recv-keys 1C4CBDCDCD2EFD2A && \
  echo "deb http://repo.percona.com/apt `lsb_release -cs` main" > /etc/apt/sources.list.d/percona.list && \
  apt-get update && \
  apt-get install -y percona-server-server-5.6 percona-server-client-5.6 percona-toolkit percona-xtrabackup

ADD crawl_operations.sql /opt/crawl_operations.sql
ADD my.cnf /etc/mysql/my.cnf
ADD create-users.sh /opt/create-users.sh
RUN chmod -R 777 /opt/create-users.sh
RUN /opt/create-users.sh

CMD ["mysqld_safe"]

EXPOSE 3306