<?php

define("antall_tema_per_side", 25);

function forum_innlegg_liste($sql, $class="forum-innlegg-liste", $temaid = 0) {
	$innleggliste=hent_og_putt_inn_i_array($sql, "innleggid");
	$medlemsid = $_SESSION["medlemsid"];

	$har_temaid =  ($temaid > 0);

	$ulesteinnlegg = Array();
	$listeinnlegg = Array();
	if ($temaid != 0) {
		//henter listeid til alle innlegg i valgte forum og tema som det er en liste knyttet til
		$sql="SELECT forum_liste.listeid, forum_liste.tittel FROM forum_liste, forum_innlegg_ny
			WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_innlegg_ny.temaid=".$temaid.";";
		$listeinnlegg=hent_og_putt_inn_i_array($sql, "listeid");	
		
		//henter ut alle aktuelle liste-oppføringer
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
			echo " <i class='fa fa-caret-right'></i> ";
			echo "<span class='tema-tittel'><a href='?side=forum/innlegg&id=".$innlegg['temaid']."'>".$innlegg['innleggtittel']."</a></span>";
			echo "</span>";
		}

/*
		//legger til liker-ikon med antall likes (vises ikke for lister)
		if(!$innlegg['liste']){
			echo"<i class='fa fa-thumbs-up' title='Antall som liker dette'>XX";
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
				//echo "<i class='fa fa-edit tool' title='Klikk for å endre'></i> ";
				echo "<a class='tool' href='javascript:void(0)' 
						onclick='confirm_url(\"?side=forum/innlegg&id=".$temaid."&sletteinnlegg=".$id."\", 
											 \"Er du sikker på at du vil slette kommentaren?\")'>";
					echo "<i class='fa fa-times' title='Klikk for å slette'></i>";
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
					
					#For å vite om bruker står på lista og dermed ikke kan skrive seg på nytt
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

function forum_list_tema($forumid, $skip) {
	if (empty($skip)) $skip = 0;

	//henter ut alle temaene i valgte forum og henter ut siste innlegg
	$sql="SELECT forum_tema.temaid, forum_tema.forumid, tittel, sisteinnleggid, skrevetavid, tekst, innleggid, skrevet
	FROM forum_tema LEFT JOIN forum_innlegg_ny ON innleggid=sisteinnleggid WHERE forum_tema.forumid=".$forumid." ORDER BY sisteinnleggid DESC LIMIT ".$skip." , ".antall_tema_per_side.";";

	$forumtemaer = hent_og_putt_inn_i_array($sql, $id_verdi="temaid");

	$hentMedlemsid = function($innlegg) {
		return $innlegg['skrevetavid'];
	};
	$brukerIder = array_map($hentMedlemsid, $forumtemaer);

	$brukerdata = hent_brukerdata($brukerIder);

	//Henter ut alle temaer med uleste innlegg
	$medlemsid= $_SESSION["medlemsid"];
	$sql="SELECT forum_leste.temaid FROM forum_leste WHERE medlemsid=".$medlemsid.";";
	$uleste_innlegg = hent_og_putt_inn_i_array($sql, $id_verdi="temaid");

	echo "<section class='forum temaliste'>";

   	//skriver ut alle temaene i forumet sortet på sist oppdaterte med siste innlegg og av hvem
   	foreach($forumtemaer as $temaid => $forumtema){
   		$b = hent_bruker($brukerdata, $forumtema['skrevetavid']);
		$tid = strtotime($forumtema['skrevet']);

   		echo "<article class='tema";
   		if(array_key_exists($temaid, $uleste_innlegg) && $uleste_innlegg[$temaid]){
   			echo " uleste-poster";
   		}
   		echo "'>";

   		echo"<h1 class='overskrift'><a href='?side=forum/innlegg&id=".$forumtema['temaid']."'>".$forumtema['tittel']."</a></h1>
   			<div class='siste-post'>";
			if (!empty($b['foto'])) {
				$foto = $b['foto'];
			} else {
				$foto = "bilder/icon_logo.png";
			}
			echo "<img class='foto' src='".$foto."' />";
			echo "<section class='info'>";
			echo "<h5 class='navn'>".$b['fnavn']." ".$b['enavn']."</h5>";
			echo "<abbr class='tid timeago' title='".date("c", $tid)."''>kl. ".date("H:i", $tid)." den ".date("d. F Y", $tid)."</abbr>";
			echo "</div>";
		echo "</article>";
	}
	echo "</section>";

}

function forum_paginering($id, $skip, $type) {

	switch($type) {
		case "tema":
			$sql = "SELECT COUNT( temaid ) AS antall FROM forum_tema WHERE forumid=".$id;
		break;
		case "innlegg":
			$sql = "SELECT COUNT( innlegg_id ) AS antall FROM forum_innlegg_ny WHERE temaid=".$id;
			die("IKKE IMPLEMENTERT");
		break;
		default:
			die("Pagineringstype ".$type." finnes ikke");
	}

	$query = mysql_query($sql);
	$antall = mysql_result($query, 0);

	$max_antall_sider = floor($antall / antall_tema_per_side);
	$midtside = floor($max_antall_sider/2);
	//die("FIX ME");

	$sideNr = 1;
	echo "<ul class='forum pagenation'>";

	if ($skip > 0) {
		echo "<li><a href='?side=forum/tema&id=".$id."&skip=".($skip-antall_tema_per_side)."'><i class='icon-chevron-left'></i> Forrige</a></li>";
	}

	if ($antall > 12 * antall_tema_per_side) {
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=0'>1</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".(1*antall_tema_per_side)."'>2</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".(2*antall_tema_per_side)."'>3</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".(3*antall_tema_per_side)."'>4</a></li>";

			echo "<li class='dotdotdot'>...</li>";

			$midtSideMinusEn = ($midtside-2);
			$midtSide = ($midtside-1);
			$midtSidePlussEn = ($midtside);
			$midtSidePlussTo = ($midtside+1);

			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSideMinusEn*antall_tema_per_side."'>".$midtSideMinusEn."</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSide*antall_tema_per_side."'>".$midtside."</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSidePlussEn*antall_tema_per_side."'>".$midtSidePlussEn."</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$midtSidePlussTo*antall_tema_per_side."'>".$midtSidePlussTo."</a></li>";

			echo "<li class='dotdotdot'>...</li>";

			$fjerdeSisteSide = ($max_antall_sider-4);
			$tredjeSisteSide = ($max_antall_sider-3);
			$nestSisteSide = ($max_antall_sider-2);
			$sisteSide = $max_antall_sider-1;

			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$fjerdeSisteSide*antall_tema_per_side."'>".$fjerdeSisteSide."</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$tredjeSisteSide*antall_tema_per_side."'>".$tredjeSisteSide."</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$nestSisteSide*antall_tema_per_side."'>".$nestSisteSide."</a></li>";
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$sisteSide*antall_tema_per_side."'>".$sisteSide."</a></li>";
		
	} else {
		echo $antall;
		for($i = 0; $i <= $antall; $i+=25) {
			echo "<li><a href='?side=forum/tema&id=".$id."&skip=".$i."'>".$sideNr."</a></li>";
			$sideNr++;
		}
	}

	if ($skip < ($max_antall_sider-1)*antall_tema_per_side) {
		echo "<li><a href='?side=forum/tema&id=".$id."&skip=".($skip+antall_tema_per_side)."'>Neste <i class='icon-chevron-right'></i></a></li>";
	}
	echo "</ul>";
}