#!/bin/sh
set -ex

if [ -f docker/init.sh ]; then
    cd docker
fi

echo Building image
docker build -f Dockerfile .. -t jimwins/known

echo Creating temporary container to extract 'vendor'
TMP_CONTAINER=known.$$
docker create --name ${TMP_CONTAINER} jimwins/known

echo Extracting 'vendor'
docker cp ${TMP_CONTAINER}:/app/vendor ..

echo Cleaning up
docker rm ${TMP_CONTAINER}

echo Making configuration and Uploads writeable
chmod 0777 ../configuration ../Uploads

echo done.
