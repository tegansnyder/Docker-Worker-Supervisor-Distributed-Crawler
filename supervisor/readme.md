This is the supervisor container. It runs Redis and a PHP shell script that figures out which URL to crawl next, which it then sends to the Gearman server.


To build this container:
```sh
docker build -t supervisor .
```

To run:
```sh

# this container is linked to the jobserver and database server
# make sure they are both started first then run

docker run -d -P --name supervisor --link jobserver:jobserver --link database:database supervisor



# you can verify the supervisor container is linked to the jobserver container by running:

docker inspect -f "{{ .HostConfig.Links }}" supervisor
# ---- above will print: [/jobserver:/supervisor/jobserver]

# standalone mode - not we would be using this linked to the jobserver in prod
docker run -d -p 6379 --name supervisor supervisor

```

This container is using supervisord program to auto start Redis and the PHP script `boss.php`.


### To test the connection on OSX using boot2docker:
Since Boot2Docker is a VM in between your host and Docker you need to use the ip address of boot2docker and the random port shown when you type `docker ps` for the container port forwarding.

Example:
```
➜  supervisor git:(master) ✗ docker ps
CONTAINER ID        IMAGE               COMMAND                CREATED             STATUS              PORTS                     NAMES
e533c5b61bc3        supervisor:latest   "/bin/sh -c '/usr/bi   2 minutes ago       Up 5 seconds        0.0.0.0:49155->6379/tcp   supervisor
```

We see that it is forwarding the port 49155 of boot2docker to port 6379 on the docker container. What we need next is the boot2docker ip. This can be found by typing:
```sh
boot2docker ip
# outputs something like 192.168.59.103
```
Then you can test via telnet:
```sh
telnet 192.168.59.103 49155
# try a command
DBSIZE
```



### Testing
To test connectivity between containers you can open a bash terminal from the container by calling it using the `docker exec` command:
```sh
docker exec -t -i supervisor bash -l
```

To verify the proper environmental variables have been set after linking the supervisor to the job server you can use `docker exec` then type:
```sh
env
```
You will see a list like this:
```
root@33b9ddeb25ea:/opt# env
HOSTNAME=33b9ddeb25ea
JOBSERVER_PORT_4730_TCP_ADDR=172.17.0.21
JOBSERVER_PORT_4730_TCP=tcp://172.17.0.21:4730
LS_COLORS=
JOBSERVER_PORT=tcp://172.17.0.21:4730
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
PWD=/opt
JOBSERVER_NAME=/supervisor/jobserver
JOBSERVER_PORT_4730_TCP_PORT=4730
SHLVL=1
HOME=/root
JOBSERVER_PORT_4730_TCP_PROTO=tcp
LESSOPEN=| /usr/bin/lesspipe %s
LESSCLOSE=/usr/bin/lesspipe %s %s
_=/usr/bin/env
```

You can debug supervisord with commands like:
```sh
supervisorctl
# stop all
# start boss
# ? for more commands
```
