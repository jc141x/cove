# Cove

public torrent tracker built on symfony

[symfony docs](https://symfony.com/doc/5.4/index.html)

## Get Started

Create and start the docker container:

```sh
docker-compose up
```

Build the assets:

```sh
docker-compose exec yarn build
# or
docker-compose exec yarn watch
```

site will be available at http://localhost:8000

---

prepend `docker-compose exec cove` to commands to run in the container (e.g. `docker-compose exec cove php bin/console make:controller`)
