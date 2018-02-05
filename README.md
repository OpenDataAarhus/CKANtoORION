# Organicity Proxy
## CKANtoORION
Scripts to fetch, convert and post specific datasets from ODAA/CKAN to OASC/ORION

**Based on:**  
Symfony3  
resquebundle/resque  
eightpoints/guzzle-bundle

## Install
In `/vagrant/htdocs` run `composer install`

## Running
To create jobs run:  
`php bin/console app:jobs:create`

For jobs to be executed resque workers have to be started:  
```
php bin/console resque:worker-start default
php bin/console resque:worker-start orion_sync
php bin/console resque:scheduledworker-start --force
```

