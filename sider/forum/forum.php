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
			FROM forum_innlegg_ny AS fi
			LEFT JOIN forum_tema AS ft ON fi.temaid = ft.temaid
			LEFT JOIN forum AS f ON fi.forumid = f.forumid
			ORDER BY skrevet DESC 
			LIMIT 10";
	$result = mysql_query($sql);
	$sisteInnlegg=hent_og_putt_inn_i_array($sql, "innleggid");

	$forum_innlegg_topp_template = forum_innlegg_topp($sisteInnlegg);

	echo "
		<section class='siste-poster'>
			<ul>
	";

	foreach($sisteInnlegg as $id => $innlegg) {
		echo "<li class='innlegg'>";

		echo "<header>";

		echo $forum_innlegg_topp_template[$id];

		echo "</header>";

		echo "<article>";
			echo "<p class='tekst'>".nl2br($innlegg['tekst'])."</p>";
		echo "</article>";

		echo "</li>";
	}

	echo "
			</ul>
		</section>
	";
?>	