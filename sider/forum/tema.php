<?php 

	include_once('sider/forum/funksjoner.php');
	//funksjonalitet
		
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	$forumid=get('id');
	$skip=get('skip');

    #Det som printes på sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();

    echo "<section class='forum'>";
    
    forum_list_tema($forumid, $skip);

    forum_paginering($forumid, $skip, "tema");

    echo "</section>";