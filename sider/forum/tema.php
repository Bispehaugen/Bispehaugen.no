<?php 

	include_once('sider/forum/funksjoner.php');
	//funksjonalitet
		
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
		die();
	}
	
	$forumid=get('id');
	$skip=get('skip');

	har_tilgang_til_forum($forumid);

    #Det som printes på sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum($forumid);

    ?>
    <section class='forum'>
    	<section class='tools topplinje'>
			<a class="tool" href="?side=forum/nytt-tema&forumid=<?php echo $forumid; ?>"><i class='fa fa-plus' title='Klikk for å endre'></i>Nytt tema</a>
		</section>
	<?php
    
    forum_list_tema($forumid, $skip);

    forum_paginering($forumid, $skip, "tema");

    echo "</section>";