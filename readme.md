> NOTE: this is a work in progress repository that I'm using as a learning curve for setting up a micro service based workflow for distributed crawling. All my testing is being done on OSX with boot2docker.

This is a example of creating a worker supervisor architecture to crawl the web using docker containers.

* The "Supervisor" docker container runs Redis and a PHP shell script that figures out which URL to crawl next, which it then sends to the Gearman server.
* The "Gearman" docker container runs as a job server that farms out work to all the workers containers who have registered with the Gearman server.
* The "Worker" container is the base container that all worker instances use when performing crawling tasks using the PHP CLI. The "Worker" script runs on a single process, in an evented manner. They update the "Supervisor" server with the status of the crawl and write the processed HTML to our "Database" servers which run Percona (or MongoDB ?). Another design feature is that the "Workers" are stateless – they can be spun up or terminated at will without affecting jobs state.


### Testing on OSX
Make sure you have installed Boot2Docker and Docker:
https://docs.docker.com/installation/mac/

Then turn on boot2docker:
```sh
boot2docker start
# if you haven't already setup the env variables run
$(boot2docker shellinit)
```

Once boot2docker has finished loading you can now run any of the "docker" commands.


Some useful boot2docker info on OSX:
http://viget.com/extend/how-to-use-docker-on-os-x-the-missing-guide


### Cisco VPN AnyConnect issues
```sh
sudo sh vpn_fix.sh
# https://gist.github.com/christian-blades-cb/16e8ae55697ae65b5318
# https://github.com/boot2docker/boot2docker/issues/392#issuecomment-66694197
```


### Running this
As of right now this is an attempt at creating a microservices based crawling operation using docker and there is mistakes committed and best practices ommited as I'm learning as I go. Please don't run this until I take this notice away.

1. Start Database
2. Start Job Server
 - docker run -d -p 4730 -p 9001 --name jobserver jobserver
3. Start Supervisor Server
 - docker run -d -P --name supervisor --link jobserver:jobserver --link database:database supervisor
4. Start Workers
 - docker run -d -P --name worker --link jobserver:jobserver --link database:database worker


There is an automated way of doing this in the `start.sh` script:
```sh
sh start.sh
```


> NOTE: look into Amabassador:
http://www.centurylinklabs.com/deploying-multi-server-docker-apps-with-ambassadors/