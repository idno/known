#!/bin/sh
set -ex

if [ -f docker/init.sh ]; then
    cd docker
fi

docker-compose up -d
