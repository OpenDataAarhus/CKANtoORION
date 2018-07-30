# Orion ContextBroker

## Server Setup

See install guide at [ReadTheDocs](https://fiware-orion.readthedocs.io/en/develop/admin/install/index.html)

We're working on CentOS using yum to install.

**First install MongoDB:**

Create a `/etc/yum.repos.d/mongodb-org-3.2.repo` file so that you can install MongoDB directly, using yum.

Edit the file to contain this:

```
[mongodb-org-3.2]
name=MongoDB Repository
baseurl=https://repo.mongodb.org/yum/redhat/$releasever/mongodb-org/3.2/x86_64/
gpgcheck=1
enabled=1
gpgkey=https://www.mongodb.org/static/pgp/server-3.2.asc
```

Then `sudo yum install mongodb-org`

For complete MongoDB install option see [the Docs](https://docs.mongodb.com/manual/tutorial/install-mongodb-on-red-hat/)

**Second install Orion ContextBroker**

Create a `/etc/yum.repos.d/fiware.repo` file so that you can install MongoDB directly, using yum.

Edit the file to contain this:

```
[fiware]
name=Fiware Repository
baseurl=http://repositories.lab.fiware.org/repo/rpm/$releasever
gpgcheck=0
enabled=1
```

Then `sudo yum install contextBroker`


**Start the ContextBroker**

```
sudo /etc/init.d/mongod start
sudo /etc/init.d/contextBroker start
````


**Install MariaDB**
Create a `/etc/yum.repos.d/mariaqdb.repo` file so that you can install MongoDB directly, using yum.

Edit the file to contain this:

```
# MariaDB 10.2 RedHat repository list - created 2018-07-30 07:46 UTC
# http://downloads.mariadb.org/mariadb/repositories/
[mariadb]
name = MariaDB
baseurl = http://yum.mariadb.org/10.2/rhel7-amd64
gpgkey=https://yum.mariadb.org/RPM-GPG-KEY-MariaDB
gpgcheck=1
```

After the file is in place, install MariaDB with:

```
sudo yum install MariaDB-server MariaDB-client
sudo mysql_secure_installation
```

## Subscription to Central Orion (Prod only)

Follow https://github.com/OrganicityEu/organicityeu.github.io/blob/mkdocs/docs/HowToOcSite.md  
Installed in `/usr/local/etc/Asset-Subscription-Proxy/` 

Run with `forever start -a -l /dev/null -o /dev/null -r /dev/null asset-subscription-proxy.js`   
(Requires `sudo npm install -g forever`)

Validate that the subscription exists with   
`curl "http://orion.odaa.dk:1026/v2/subscriptions" \
      -H "Fiware-Service: organicity"`
      
There should be only ons subscription. If more than one subscription has been created the extra subscription must be deleted. 
There is an issue with doing this through the API, so you must do it in Mongo directly. 

To clear ALL subscription:
 ```
 # mongo
 > use orion-organicity
 > db.csubs.remove({})
 ```
 (Start mongo CLI, select db, truncate collection)


## Test Installation

**Version**

`GET http://organicity.vm:1026/version

with Request Headers:

``Content-Type: application/json`` 

Reply should look like:
```
{
  "orion": {
    "uptime": "0 d, 0 h, 0 m, 40 s",
    "git_hash": "af44fd1fbdbbfd28d79ef4f929e871e515b5452e",
    "compile_time": "Thu Jun 16 15:46:51 CEST 2016",
    "compiled_by": "fermin",
    "compiled_in": "centollo",
    "version": "1.2.1"
  }
}
```

**Post Entity/Entities (Batch)**

`POST http://organicity.vm:1026/v2/op/update`

with Request Headers:

```
Content-Type: application/json
Accept: application/json
Fiware-Service: organicity
Fiware-ServicePath: /
```

And body:
```
{
  "actionType": "APPEND",
  "entities": [
    {
      "id": "urn:oc:entity:aarhus:friluftsliv:forest:...
  
  ...
  
}
```


**Get All Entities**

`GET http://organicity.vm:1026/v2/entities`

with Request Headers:

```
Content-Type: application/json
Fiware-Service: organicity
Fiware-ServicePath: /
```

**Get All Entities for Aarhus**

http://organicity.vm:1026/v1/queryContext

with Request Headers:

```
Content-Type: application/json
Accept: application/json
Fiware-Service: organicity
Fiware-ServicePath: /
```


and with Body:
```
{
    "entities": [
        {          
            "isPattern": "true",
            "id": "urn:oc:entity:aarhus:.*"
        }
    ]
}
```

## Trouble shooting

Log files are located in /var/log/contextBroker, so:

```
tail -f /var/log/contextBroker/contextBroker.log
```
