This is a way to run Known using Docker and Tailscale for local development.

Copy docker/sample.env to .env and adjust as appropriate

Run ./bin/init.sh

Run ./bin/start.sh

Visit https://known.YOUR-TAILSCALE-TAILNET/

You will need to enter the following for the database:

Database name: known (or whatever you set in .env)
User name: known (or whatever you set in .env)
Password: whatever you set in .env
Server: db

The 'configuration' and 'Uploads' directories are wide open so the webserver
can write to them because Docker user/permission management is a headache.

Jim Winstead <jimw@trainedmonkey.com>
June 3, 2024
