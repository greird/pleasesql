<?php
require_once('../class.pleasesql.php');

$please = new PleaseSql();

var_dump($please->autoRestore('test', 'test.sql', 10));
echo "<pre>";
print($please->getStatus);
echo "</pre>";

//var_dump($please->restore('lgd_demodata2', '../demo-login/sql/lgd_demodata2.sql'));

//var_dump($please->execute("UPDATE  `lgd`.`lgd_demodata2` SET  `content` =  'Modification de la base' WHERE  `lgd_demodata2`.`id` =3;", 0));

echo "<pre>";
var_dump($please->execute("SELECT content FROM test WHERE id='1'", 1));
echo "</pre>";

echo "<pre>";
var_dump($please->execute("SELECT * FROM test", 2));
echo "</pre>";

unset($please);
?>
