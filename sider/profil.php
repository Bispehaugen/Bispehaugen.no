<?php

if(has_get('id')){
	
	$id = get('id');
	$sql = "SELECT nyhetsid, overskrift, ingress, hoveddel, bilde, tid, type, skrevetav FROM `medlemmer` WHERE nyhetsid=".$id;
	
}


?>