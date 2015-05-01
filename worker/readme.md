This is the worker container. It contains PHP CLI and composer.


To build this container:
```sh
docker build -t worker .
```

Run the container:
```sh
# docker run -it --rm --name worker worker php worker.php

# this container is linked to the jobserver and database
docker run -d -P --name worker --link jobserver:jobserver --link database:database worker

``

### Check the status of the container:
docker logs $(docker ps | grep worker | awk '{ print $1 }')


### Testing
To test connectivity between containers you can open a bash terminal from the container by calling it using the `docker exec` command:
```sh
docker exec -t -i worker bash -l
```