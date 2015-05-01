DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
docker run -w $DIR/database -d -p 3306 --name database database || docker start database
docker run -w $DIR/jobserver -d -p 4730 -p 9001 --name jobserver jobserver || docker start jobserver
docker run -w $DIR/supervisor -d -P --name supervisor --link jobserver:jobserver --link database:database supervisor || docker start supervisor
docker run -w $DIR/worker -d -P --name worker --link jobserver:jobserver --link database:database worker || docker start worker