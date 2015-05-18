This container runs Redis and is used to store the job queues for the Gearman job server.

To build this container:
```sh
docker build -t queuestore .
```

To run:
```sh

docker run -d -p 6379 --name queuestore queuestore

```