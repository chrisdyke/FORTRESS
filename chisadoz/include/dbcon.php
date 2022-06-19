<?php
$DB_host = "localhost";
$DB_user = "fortressystemnig_chisado"; 
$DB_pass = "chisado@2021";
$DB_name = "fortressystemnig_chisado";
$item_per_page = 8 ;

	try
	{
		$DBcon = new PDO("mysql:host={$DB_host};dbname={$DB_name}",$DB_user,$DB_pass);	
		$DBcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e)
	{
		echo "ERROR : ".$e->getMessage();
	}
