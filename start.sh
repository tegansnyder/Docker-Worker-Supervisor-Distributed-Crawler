DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
docker run -w $DIR/database -d -p 3306 --name database database || docker start database
docker run -w $DIR/queuestore -d -p 6379 --name queuestore queuestore || docker start queuestore
docker run -w $DIR/jobserver -d -P --name jobserver --link queuestore:queuestore jobserver || docker start jobserver
docker run -w $DIR/supervisor -d -P --name supervisor --link jobserver:jobserver --link database:database --link queuestore:queuestore supervisor || docker start supervisor
docker run -w $DIR/worker -d -P --name worker --link jobserver:jobserver --link database:database worker || docker start worker
docker run -w $DIR/worker -d -P --name worker2 --link jobserver:jobserver --link database:database worker || docker start worker2
docker run -w $DIR/worker -d -P --name worker3 --link jobserver:jobserver --link database:database worker || docker start worker3