<?php 
	
	//funksjonalitet
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=aktiviteter/liste');
	};
	
	$id=$_GET['id'];
	
	if(isset($_GET['id'])){
		$sql="DELETE FROM medlemmer WHERE medlemsid = '".$id."';";
		mysql_query($sql);
		header('Location: ?side=medlem/liste');
	};
	
?>