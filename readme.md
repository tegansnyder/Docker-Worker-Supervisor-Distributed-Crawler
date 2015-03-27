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
```

Once boot2docker has finished loading you can now run any of the "docker" commands.

