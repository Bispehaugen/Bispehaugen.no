<?php 
	
	//funksjonalitet
	
	//sjekker om man er admin
	if($_SESSION['rettigheter']>1){
		header('Location: ?side=aktiviteter/liste');
	};
	
	$id=get('id');
	
	if(has('id')){
		$sql = "UPDATE arrangement SET slettet=true WHERE arrid = '".$id."';";
		mysql_query($sql);
		header('Location: ?side=aktiviteter/liste');
		exit('tets');
	}
	
?>