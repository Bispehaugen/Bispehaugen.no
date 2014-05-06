<?php 
	include_once("sider/forum/funksjoner.php");

	setlocale(LC_TIME, "Norwegian");

	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: index.php');
	};

	//Her legges det inn en oversikt over alle forumene
    list_forum();
	
	$sql = "SELECT fi . * , ft.tittel as innleggtittel, f.tittel as tematittel
			FROM forum_innlegg_ny AS fi
			LEFT JOIN forum_tema AS ft ON fi.temaid = ft.temaid
			LEFT JOIN forum AS f ON fi.forumid = f.forumid
			ORDER BY skrevet DESC 
			LIMIT 10";

	forum_innlegg_liste($sql, "forum-innlegg-liste siste-poster");
?>	