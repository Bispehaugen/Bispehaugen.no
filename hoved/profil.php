<?php

if(isset($_GET['id'])){
	
	$id = mysql_real_escape_string($_GET['id']);
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `medlemmer` WHERE nyhetsid=".$id;
	
	
	
	
	
}


?>