<?php 
	include_once("sider/forum/funksjoner.php");

	setlocale(LC_TIME, "Norwegian");

	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: index.php');
		die();
	};

	//Her legges det inn en oversikt over alle forumene
    list_forum();
	
	$sql = siste_forumposter_sql(5);

	echo "<section class='forum'>";
	echo "<h2>Siste poster</h2>";

	forum_innlegg_liste($sql, "forum-innlegg-liste siste-poster");

	echo "</section>";
?>	