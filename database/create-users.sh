#!/bin/bash
set -ex

mysqld_safe &
sleep 10
mysql -e 'GRANT ALL PRIVILEGES ON *.* TO "root"@"%" WITH GRANT OPTION;' -u root
mysql -e 'create database crawl_operations;' -u root
mysql -u root crawl_operations < crawl_operations.sql
mysqladmin -u root -h 127.0.0.1 shutdown