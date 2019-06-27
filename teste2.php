<?php

$hostname = "localhost";
$username = "satir710_chalita";
$password = "satir710_chalita";
$dbname = 'chalita123';
$dbh =null;
try {
    $dbh = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
}
catch(PDOException $e)
{
   echo "erro ".$e->getMessage();
}

var_dump($dbh);

$result = $dbh->query("show tables");
//Table starting tag and header cells
while($row = $result->fetch ()) {
	var_dump($row);
}?>
