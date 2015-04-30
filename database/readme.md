This container is running MySQL (Percona) and stores the results of parsed data collected by the workers.

To build this container:
```sh
docker build -t database .
```

Run the container linked to the supervisior.
```sh
docker run -d -p 3306 --name database database
```


### Testing
To test connectivity between containers you can open a bash terminal from the container by calling it using the `docker exec` command:
```sh
docker exec -t -i database bash -l
```