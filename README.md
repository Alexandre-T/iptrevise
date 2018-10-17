# IP Manager

Ip Manager for a tech team on network where DHCP protocols are forbidden. 

## Requirements
Software needed :
* git
* composer
* Database server postgresql
* An access to create a new database or an owner-access to a dedicated database 

## Installation
```
git clone https://github.com/Alexandre-T/iptrevise.git ip-manager
cd ip-manger
composer install
composer update
```

Create database if it does not exists:
```
CREATE DATABASE bd_ipmanager
    WITH 
    OWNER = symfony
    ENCODING = 'UTF8'
    LC_COLLATE = 'French_France.1252'
    LC_CTYPE = 'French_France.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;
```

Edit `.env` to specify access:

Load database model:
```
php bin/console doctrine:migrations:migrate
```

Load database data:
```
php bin/console doctrine:fixtures:load --env=dev --fixtures=src/DataFixtures
```

Launch application (run can be replaced by start on linux):
```
php bin/console server:run
```

Launch unit test:
```
php bin/codecept run unit
```

Launch functional test:
```
php bin/codecept run functional
```

Launch acceptance test: (not implemented)
```
php bin/codecept run unit
```

