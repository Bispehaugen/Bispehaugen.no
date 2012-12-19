<?php 
	
	//funksjonalitet
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=aktiviteter/liste');
	};
	
	$id=$_GET['id'];
	
	if(isset($_GET['id'])){
		//$sql = "UPDATE arrangement SET slettet=true WHERE arrid = '".$id."';";
		mysql_query($sql);
		header('Location: ?side=aktiviteter/liste');
		exit('tets');
	}
	
?>