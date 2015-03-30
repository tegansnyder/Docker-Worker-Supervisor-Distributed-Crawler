This is the job server container. It runs the Gearman server to farms out work to all the workers containers who have registered with the Gearman server.

To build this container:
```sh
docker build -t jobserver .
```

Run the container:
```sh
docker run -d -p 4730 --name jobserver jobserver
```

### To get all of the command line options:
```sh
docker run --rm jobserver -h
```

### To test the connection on OSX using boot2docker:
Since Boot2Docker is a VM in between your host and Docker you need to use the ip address of boot2docker and the random port shown when you type `docker ps` for the container port forwarding.

Example:
```
➜  jobserver git:(master) ✗ docker ps
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                     NAMES
aa61830e3877        jobserver:latest    "/sbin/my_init -- /u   17 minutes ago      Up 17 minutes       0.0.0.0:49153->4730/tcp   jobserver
```

We see that it is forwarding the port 49153 of boot2docker to port 4730 on the docker container. What we need next is the boot2docker ip. This can be found by typing:
```sh
boot2docker ip
# outputs something like 192.168.59.103
```
Then you can test via telnet:
```sh
telent 192.168.59.103 49153
# try a command
version
```