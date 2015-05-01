## Preface
Please note this repositiory contains my experiment in building micro-services based web crawling operation using docker containers. It currently is not suitable for any production use and should only be viewed as skeleton template.

## What this is?
This is a example of creating a worker supervisor architecture to crawl the web using docker containers. I'm taking some inspiration from an article found [here](http://blog.semantics3.com/how-we-built-our-almost-distributed-web-crawler/). This repository consists of a few different micro services running as docker containers. These docker containers are listed below:

* The "supervisor" container runs as a daemon. It continually checks for new crawling jobs by connecting to a database container. When it finds a new job it dispatches the job to the "jobserver" running Gearman.
* The "jobserver" container runs the Gearman job server that handles all the job queue between the workers and the the jobs dispatched by the "supervisor" container.
* The "worker" container is the base container that all worker instances use when performing crawling tasks using the PHP CLI. The "Worker" script runs on a single process, in an evented manner. They update the "supervisor" container with the status of the crawl and write the processed HTML the "database" container in the "crawl_results" table. 
* The "database" container runs MySQL (Percona 5.6) and contains two tables "crawl_jobs" and "crawl_results". The "crawl_jobs" table contains information that the worker will use when parsing HTML found on the url it is crawling in the form of a "patterns" column. This "patterns" column is seralized and can be used to define the parsing technique used by the worker. It will be refined in future commits to this repository to work in a standardized manner across ecommerce websites.

## Running this
As of right now this is an attempt at creating a microservices based crawling operation using docker and there is mistakes committed and best practices ommited as I'm learning as I go. Please don't run this until I take this notice away. If you are really wanting to run it you would need to start by building the containers.

#### Building the containers
```sh
cd jobserver/ && docker build -t jobserver . && cd ..
cd supervisor/ && docker build -t supervisor . && cd ..
cd database/ && docker build -t database . && cd ..
cd worker/ && docker build -t worker . && cd ..
```

#### Starting the containers automatically

There is an automated way of doing this in the `start.sh` script. Basically it first trys to run them using the "docker run" command. If it can't run them because you have already created a container via that name then it will complain and throw a message to the screen you can safely ignore. It then trys to use the "docker start" command to start the already existing containers.
```sh
sh start.sh
```

#### Starting the containers manually
1. Start Database

	```sh
	docker run -d -p 3306 --name database database
	```
2. Start Job Server

	```sh
	docker run -d -p 4730 -p 9001 --name jobserver jobserver
 	```
3. Start Supervisor Server

	```sh
	docker run -d -P --name supervisor --link jobserver:jobserver --link database:database supervisor
	```
4. Start Workers

	```sh
	docker run -d -P --name worker --link jobserver:jobserver --link database:database worker
	```


-----------------

## OSX Users
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

-----------------

### Cisco VPN AnyConnect issues
If your behind a corporate firewall and use the Cisco VPN client you will have issues with Docker connections. I have included a nice little shell script to help in this situation.

```sh
sudo sh vpn_fix.sh
# https://gist.github.com/christian-blades-cb/16e8ae55697ae65b5318
# https://github.com/boot2docker/boot2docker/issues/392#issuecomment-66694197
```

-----------------


> NOTE: look into Amabassador:
http://www.centurylinklabs.com/deploying-multi-server-docker-apps-with-ambassadors/