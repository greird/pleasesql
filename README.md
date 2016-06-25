PleaseSQL
==========

###Gentle introduction###

Hi there, 

PleaseSQL is a dead-simple php 5+ class that will painlessly execute any SQL query as long as you ask it kindly. 

Features:
- Connect to the database (on construction)
- Execute a query
- Restore table from an sql file
- Automatically restore table from an sql file if given date expires
- Disconnect from database (on destruction)
 
##Thoughtful warning##

Actually, I would not recommend anyone to use this on a serious production environnement. I am no security expert nor am I a php nerd.
I mainly use this for rapid prototyping on a local environnement. 
You've been warned. ;)

***

###Pleasant configuration###

Edit the top block of the class to set your host name, database name, username and password.
Additionnaly, you can edit the path to the file containing the date of the last table restoration. The file won't be created as long as you do not call the autoRestore method.

```php
/**
* ///////////////////////////////////////////
* SETTINGS - Please insert your database information below
*/
private $host 			= 'localhost';
private $database 		= 'test';
private $username 		= 'root';
private $password 		= 'root';
private $timestampFile 	= "timestamp.ini"; // file will be created if it doesn't exist
/**
* ///////////////////////////////////////////
*/
```

***

###Courteous usage###


#####Connect to database#####

When initiated, the class will automatically try to connect to database.

```php
$please = new PleaseSQL();
```


#####Execute a query#####

```php
$please->execute($sql, $fetchData);
```

$sql is your sql query.

$fetchData is set to 0 by default :
- 0 : Will return true on success and nothing else
- 1 : Will return a single variable containing the first result
- 2 : Will return an array containing every results

e.g.
```php
// Return an array containing every results
$please->execute("SELECT content 
            FROM my_table", 2));

// Return a single string variable containing the first result
$please->execute("SELECT content 
            FROM my_table
            WHERE id='3'", 1));

// Return true on success
$please->execute("TRUNCATE my_table", 0));
// same as
$please->execute("TRUNCATE my_table"));
```


#####Restore a table from a given sql file#####

```php
$please->restore($tableName, $filePath);
```

$tableName is the name of your table.

$filePath is the relative path to an sql file containing an INSERT query.

e.g.
```php
$please->restore('my_table', 'sql/my_table_backup.sql');
```

The sql file must contain a simple INSERT query such as the one below

```sql
INSERT INTO `lgd_demodata2` (`id`, `content`) VALUES
(1, 'Default content #1'),
(2, 'Default content #2');
```

Note: It will empty the table first (TRUNCATE) !


#####Automatically restore a table from a given sql file#####

```php
$please->autoRestore($tableName, $filePath, $delay);
```

Same as above with an additionnal $delay parameter.
$delay (int) is the time in seconds between each restoration.

e.g.
```php
// my_table will be restored every 24h
$please->autoRestore('my_table', 'sql/my_table_backup.sql', 84600);
```

Note: If the last restoration date was 24h ago, the table will be restored as soon as the code will be executed --> when the page will be displayed.

If you want to know when the last table restoration has occured and when will the next happens, print $please->getStatus.

```php
print $please->getStatus;
```

This will display the following information:

```
Status:

WAINTING.. / TABLE RESTORED

Last db reset: 2014-04-21 10:44:44
Next db reset: 2014-04-22 23:58:53 
```


#####Disconnect from database#####

Unset the class and you'll be automatically disconnected from the database.

```php
unset($please);
```

***