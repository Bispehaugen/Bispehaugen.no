<?php 

	setlocale(LC_TIME, "Norwegian");

	//funksjonalitet
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: index.php');
	};

	//Her legges det inn en oversikt over alle forumene
    list_forum();
	
	$sql = "SELECT fi . * , ft.tittel as innleggtittel, f.tittel as tematittel
			FROM forum_innlegg AS fi
			LEFT JOIN forum_tema AS ft ON fi.temaid = ft.temaid
			LEFT JOIN forum AS f ON fi.forumid = f.forumid
			ORDER BY skrevet DESC 
			LIMIT 10";
	$result = mysql_query($sql);
	$sisteInnlegg=hent_og_putt_inn_i_array($sql, "innleggid");

	$hentMedlemsid = function($innlegg) {
		return $innlegg['skrevetavid'];
	};
	$brukerIder = array_map($hentMedlemsid, $sisteInnlegg);
	$brukerinfo = brukerinfo_forum($brukerIder);

	echo "
		<section>
			<ul>
				<li>
	";

	foreach($sisteInnlegg as $id => $innlegg) {
		$tid = strtotime($innlegg['skrevet']);

		echo "<li>";
		echo "<div class='info'>";
			echo "<span class='forum-tittel'><a href='?side=forum/tema&id=".$innlegg['forumid']."'>".$innlegg['tematittel']."</a></span>";
			echo " <i class='icon-caret-right'></i> ";
			echo "<span class='tema-tittel'><a href='?side=forum/innlegg&id=".$innlegg['temaid']."'>".$innlegg['innleggtittel']."</a></span>";
			echo "<span class='tid'>kl. ".date("H:i", $tid)." den ".date("d. F Y", $tid)."</span>";
		echo "</div>";

		// Lag en hjelpefil for å hente ut mer profilinfo generelt i forum
		echo "<div class='skrevetav'>";
		echo "Bruker: ".$innlegg['skrevetav'];
		echo $brukerinfo[$innlegg['skrevetavid']]['innlegg_html'];
		echo "</div>";

		echo "<p class='tekst'>".nl2br($innlegg['tekst'])."</p>";

		echo "</li>";
	}

	echo "
			</ul>
		</section>
	";
?>