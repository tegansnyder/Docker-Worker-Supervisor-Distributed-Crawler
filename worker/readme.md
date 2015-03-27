This is the worker container. It contains PHP CLI and composer.


To build this container:
```sh
docker build -t worker .
```

Run the container:
```sh
docker run -it --rm --name worker worker php worker.php
``