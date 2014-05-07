<?php

function forum_innlegg_liste($sql, $class="forum-innlegg-liste", $temaid = 0) {
	$innleggliste=hent_og_putt_inn_i_array($sql, "innleggid");
	$medlemsid = $_SESSION["medlemsid"];

	$har_temaid =  ($temaid > 0);

	$ulesteinnlegg = Array();
	if ($temaid != 0) {
		//henter listeid til alle innlegg i valgte forum og tema som det er en liste knyttet til
		$sql="SELECT forum_liste.listeid, forum_liste.tittel FROM forum_liste, forum_innlegg_ny
			WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_innlegg_ny.temaid=".$temaid.";";
		$listeinnlegg=hent_og_putt_inn_i_array($sql, "listeid");	
		
		//henter ut alle aktuelle liste-oppfåringer
		$sql="SELECT medlemsid, fnavn, enavn, forum_listeinnlegg_ny.listeid, forum_listeinnlegg_ny.tid, forum_listeinnlegg_ny.innleggid, 
		forum_listeinnlegg_ny.kommentar, forum_listeinnlegg_ny.flagg, forum_liste.expires 
		FROM forum_liste, forum_innlegg_ny, forum_listeinnlegg_ny, forum_tema, medlemmer 
		WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_liste.listeid=forum_listeinnlegg_ny.listeid AND
		 forum_tema.temaid=".$temaid." AND forum_innlegg_ny.temaid=".$temaid." AND brukerid=medlemmer.medlemsid ORDER BY tid ;";
		$listeoppforinger=hent_og_putt_inn_i_array($sql, "innleggid");	


		//Henter ut siste uleste innlegg i tråd
		$sql="SELECT uleste_innlegg FROM forum_leste WHERE temaid=".$temaid." AND medlemsid=".$medlemsid.";";
		$ulesteinnlegg=hent_og_putt_inn_i_array($sql, "uleste_innlegg");		
	}
	
	$hentMedlemsid = function($innleggliste) {
		return $innleggliste['skrevetavid'];
	};
	$brukerIder = array_map($hentMedlemsid, $innleggliste);

	$brukerdata = hent_brukerdata($brukerIder);

	echo "
		<section class='".$class."'>
			<ul>
	";

	foreach($innleggliste as $id => $innlegg) {
		$b = $brukerdata[$innlegg['skrevetavid']];
		$tid = strtotime($innlegg['skrevet']);

		$erLestClass = array_key_exists($id, $ulesteinnlegg) ? "ulest" : "lest";

		echo "<li class='innlegg ".$erLestClass."'>";
		echo "<header>";
		
		if (!empty($b['foto'])) {
			echo "<img class='foto' src='".$b['foto']."' />";
		}
		echo "<section class='info'>";
		echo "<h5 class='navn'>".$b['fnavn']." ".$b['enavn']."</h5>";
		echo "<abbr class='tid timeago' title='".date("c", $tid)."''>kl. ".date("H:i", $tid)." den ".date("d. F Y", $tid)."</abbr>";

		if (!$har_temaid) {
			echo " i ";
			echo "<span class='plassering'>";
			echo "<span class='forum-tittel'><a href='?side=forum/tema&id=".$innlegg['forumid']."'>".$innlegg['tematittel']."</a></span>";
			echo " <i class='icon-caret-right'></i> ";
			echo "<span class='tema-tittel'><a href='?side=forum/innlegg&id=".$innlegg['temaid']."'>".$innlegg['innleggtittel']."</a></span>";
			echo "</span>";
		}

/*
		//legger til liker-ikon med antall likes (vises ikke for lister)
		if(!$innlegg['liste']){
			echo"<i class='icon-thumbs-up' title='Antall som liker dette'>XX";
			//du kan bare like andres innlegg
			if($forum_innlegg['skrevetavid']!=$_SESSION['medlemsid']){
				echo"<br><a href='?side=forum/innlegg&id=".$temaid."&likerinnlegg=".$forum_innlegg['innleggid']."'>Lik dette</i></a>";
			}
		}
*/
		echo "</section>";


		//viser endre/slett-knapper på egne innlegg og for admin (så de har mulighet til å overstyre)
		if(($innlegg['skrevetavid']==$medlemsid || $_SESSION['rettigheter']>1) && $har_temaid){
			echo "<section class='tools'>";
				//echo "<i class='icon-edit tool' title='Klikk for å endre'></i> ";
				echo "<a class='tool' href='javascript:void(0)' 
						onclick='confirm_url(\"?side=forum/innlegg&id=".$temaid."&sletteinnlegg=".$id."\", 
											 \"Er du sikker på at du vil slette kommentaren?\")'>";
					echo "<i class='icon-remove' title='Klikk for å slette'></i>";
				echo "</a>";
			echo "</section>";
		}


		echo "</header>";
		echo "<article>";
			echo "<p class='tekst'>".nl2br($innlegg['tekst'])."</p>";
		
      	//if som skriver ut liste hvis det hører en liste til innlegget
		if(array_key_exists($id, $listeinnlegg)){
			$oppfort_paa_liste = False;
			echo "<table class='paameldingsliste'>";
			foreach($listeoppforinger as $listeoppforing){
				if($listeoppforing['listeid']==$id){
					$strek_igjennom_klasse = ($listeoppforing['flagg']==1) ? "skrek-igjennom" : "";
					echo "<tr><td class='".$strek_igjennom_klasse."'>";
					echo $listeoppforing['fnavn']." ".$listeoppforing['enavn'];
					echo "</td><td>".$listeoppforing['kommentar']."</td></tr>";
					
					#For å vite om bruker står på lista og dermed ikke kan skrive seg på på nytt
					if($listeoppforing['medlemsid']==$medlemsid){$oppfort_paa_liste=True;}	
				};	
			};
			//Legger til tekstfelt for å melde seg på hvis ikke lista har expired
			
			if($oppfort_paa_liste){
				echo "<tr><td colspan='2'><b>Du er allerede skrevet på lista</b></td></tr> ";						
			}elseif(strtotime(date('Y-m-d'))/(60*60*24) <= strtotime(substr($listeoppforing['expires'],0,10))/(60*60*24) || $listeoppforing['expires']==NULL){
			echo "<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
				<tr><td>Kommentar (frivillig):<br><input type='text' name='kommentar' autofocus><br><input type='checkbox' name='flagg' value='1'> Stryk navnet</td>
				<td><input type='hidden' name='medlemsid' value='".$_SESSION['medlemsid']."'>
				<input type='hidden' name='listeinnlegg' value='".$listeinnlegg[$innlegg['innleggid']]['listeid']."'>
				<input type='submit' name='nyttListeInnlegg' value='Skriv meg på lista'></td></tr>";
			}else{
				echo "<tr><td colspan='2'><b>Det er ikke lenger mulig å melde seg på denne lista</b></td></tr> ";	
			};
			echo "</form></table>";
  		};
			

		echo "</article>";
		echo "</li>";
	}

	echo "
			</ul>
		</section>
	";
}