# tinyadr.es
Small URL shortener made a long time ago. [tinyadr.es](tinyadr.es)

### SQL table structure

You will need a MySQL/MariaDB server. Here's the table structure:
```sql
CREATE TABLE `URLs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shortURL` varchar(50) NOT NULL,
  `destURL` varchar(1000) NOT NULL,
  `createdDate` int(6) NOT NULL,
  `expirationDate` int(6) NOT NULL,
  `protocol` int(6) NOT NULL DEFAULT '80',
  `IP` varchar(20) NOT NULL,
  `Enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortURL` (`shortURL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Usage notes

If you'd like to spin up your own copy of this URL shortener, I'd recommend using lighttpd as the webserver. Configuration is as follows:
```
url.rewrite-final = (
        "^/css" => "/css/style.css",
        "^/tos" => "/tos.php",
        "^/about" => "/about.php",
        "^/check" => "/check.php",
        "^/check/(.*)$" => "/check.php?url=$1",
        "^/(.*)$" => "/index.php?url=$1",
)
```


### Known bugs

~~When first visited, a 404 will be thrown and the URL will be https://tinyadr.es/index.php?url=~~ Fixed!

### Contributors
 *  [@ResonantWave](https://github.com/ResonantWave)

### Contributing
 *  The code is licensed under the [AGPL v3.0](LICENSE)
 *  Feel free to contribute to the code
