#!/bin/bash

# Exit if any command fails
set -e

# Launch the WordPress docker
docker-compose up -d

# Wait until the docker containers are setup properely
echo -n "Waiting for server..."
until [ "$(curl -m 1 -sLI 'http://localhost' | grep 'HTTP')" != "" ]; do echo -n '.'; sleep 1; done
echo " Ready."
