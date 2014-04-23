SuperSql
==========

###Introduction###

SuperSql is a simple PHP5 class which makes database handling easy. 

Features:
- Connect to the database (on construction)
- Execute a query
- Restore table from an sql file
- Automatically restore table from an sql file if given date expired
- Disconnect from database (on destruction)
 
***

###Configuration###

Edit the top block of the class to set your host name, database name, username and password.
Additionnaly, you can edit the path to the file containing the date of the last table restoration. The file won't be created as long as you do not call the autoRestore method.

```php
/**
* ///////////////////////////////////////////
* 
* SETTINGS - database information
*/
private $host                 = 'localhost';
private $database             = 'test';
private $username             = 'root';
private $password             = '';
private $timestampFile        = "timestamp.ini"; // file will be created if it doesn't exist
/**
* DO NOT EDIT BELOW THIS LINE
* ///////////////////////////////////////////
*/
```

***

###How to###


#####Connect to database#####

When initiated, the class will automatically try to connect to database.

```php
$superSql = new SuperSql();
```


#####Execute a query#####

```php
execute($sql, $fetchData);
```

$sql is your sql query.

$fetchData is set to 0 by default :
- 0 : Will return true on success and nothing else
- 1 : Will return a single variable containing the first result
- 2 : Will return an array containing every results

e.g.
```php
// Return an array containing every results
$superSql->execute("SELECT content 
            FROM my_table", 2));

// Return a single string variable containing the first result
$superSql->execute("SELECT content 
            FROM my_table
            WHERE id='3'", 1));

// Return true on success
$superSql->execute("TRUNCATE my_table", 0));
// same as
$superSql->execute("TRUNCATE my_table"));
```


#####Restore a table from a given sql file#####

```php
restore($tableName, $filePath);
```

$tableName is the name of your table.

$filePath is the relative path to an sql file containing an INSERT query.

e.g.
```php
$superSql->restore('my_table', 'sql/my_table_backup.sql');
```

The sql file must contain a simple INSERT query such as the one below

```sql
INSERT INTO `lgd_demodata2` (`id`, `content`) VALUES
(1, 'Default content #1'),
(2, 'Default content #2');
```

Note: restore() will empty the table first (TRUNCATE).


#####Automatically restore a table from a given sql file#####

```php
autoRestore($tableName, $filePath, $delay);
```

Same as above with an additionnal $delay parameter.
$delay (int) is the time in seconds between each restoration.

e.g.
```php
// my_table will be restored every 24h
$superSql->autoRestore('my_table', 'sql/my_table_backup.sql', 84600);
```

Note: If the last restoration date was 24h ago, the table will be restored as soon as the code will be executed --> when the page will be displayed.


#####Disconnect from database#####

Unset the class and you'll be automatically disconnected from the database.

```php
unset($superSql);
```

***

###Debugging###

```php
print $superSql->status;
```

This will display the following information:

```
Status:

WAINTING.. / TABLE RESTORED

Last db reset: 2014-04-21 10:44:44
Next db reset: 2014-04-22 23:58:53 
```