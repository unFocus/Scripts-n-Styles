# Dev Server commands
The dev server is basically the generic docker images for WordPress, but with things that got in the way ripped out, so that we can tweak wp-config.php constants if we wish. :-/ It should've been easier.

## Start
`docker-compose up` or `docker-compose up -d`
Builds and starts your services.

## Stop
docker-compose stop
Stops the services without removing them. Leaves it in the current state to continue working later.

## Backup
`docker exec sns-db sh -c 'exec mysqldump --all-databases -uroot -p"$MYSQL_ROOT_PASSWORD"' > ./dbinit/data.sql`
Calls into the `sns-db` container and makes a sql dump into the dbinit folder (for a fresh pre-population on next up).

You can make a new backup and use it as `mkdir -p foo && docker exec sns-db sh -c 'exec mysqldump --all-databases -uroot -p"$MYSQL_ROOT_PASSWORD"' > ./foo/data.sql && USEDB=foo`
(Change the .env file variable to make it persistently use it.)

## Clean up
`docker-compose down`
Removes most things, but not the data volume (countinue from that data next time).
`docker-compose down -v --remove-orphans`
Removes all (well, most of at least) the things, including the database. (The generic source images remain.) Use this to reset the database to the 'dbinit' backup.

## Change PHP versions
`export PHPV=7.1 && docker-compose up --build`
Choices are `5.6`, `7.0`, `7.1`

### Bash in the container
`docker-compose run wordpress /bin/bash`

Use `alias wp='docker-compose exec --user www-data wordpress wp --url=localhost'` to set up a temporary shortcut for using wp-cli in the container.