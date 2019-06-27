<?php
$link = mysql_connect('localhost', 'satir710_chalita', 'chalita123');
if (!$link) {
    die('Não foi possível conectar: ' . mysql_error());
}
echo 'Conexão bem sucedida';
mysql_close($link);


$query = "select * from wp_post";
	
	$result = mysql_query($query);
	
	if($result){
		while($row = mysql_fetch_array($result)){
			$name = $row["$yourfield"];
			echo "Nome: ".$name."br/>";
		}
	}
?>
