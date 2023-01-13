<?php
include 'php_start.php';
// LOAD WEB PUSH LIBRARY

if (strpos(getcwd(), 'DEV') !== false) { require "../vendor/autoload.php";} // dev environment
else{require "vendor/autoload.php";} // PROD

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

//**************  GET SUBSCRIPTION and store it in database **************
if (isset ($_POST['sub'])){
	Logs::addM("Get Web Subscription:".$_POST['sub']); // store the subscription to broadcast after
	// check if a subscription already exist
	$query = "select * from `WebNotificationSubscription` where details = '".$_POST['sub']."'";
	Logs::addM($query);
	$result = sql_query($query);
	if (sizeof($result)>0){
		Logs::addM("web notif subscription already exists. Nothing to create.");
	}
	else{
		Logs::addM("web notif subscription do not exist -> creation...");
		$query = "INSERT INTO `WebNotificationSubscription`( `details`, `status`) VALUES ('".$_POST['sub']."','subscribed')";
		$result = sql_query($query);
		Logs::addM($query);
	}
}


?>
