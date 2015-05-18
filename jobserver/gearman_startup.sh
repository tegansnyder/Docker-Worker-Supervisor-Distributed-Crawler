#!/bin/sh
cd /usr/sbin
gearmand -d --log-file=stderr --user=gearman -q redis --redis-server=$QUEUESTORE_PORT_6379_TCP_ADDR --redis-port=$QUEUESTORE_PORT_6379_TCP_PORT