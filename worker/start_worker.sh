#!/bin/sh
php /var/crawler/vendor/bin/gearman start --daemon=false --bootstrap="/var/crawler/bootstrap.php" --class="Bootstrap" --server="$ENV_JOBSERVER_PORT_4730_TCP_ADDR:4730"