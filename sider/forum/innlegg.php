<?php 

	//funksjonalitet
	
	//TODO bredde pÃ¥ tabellen, bare hente opp de 10 siste og ha en knapp med 'vis alle' som printer smtlige innlegg
	//og blar til bunnen, format og ev bilde i 'skrevet av' kolonnen. I tillegg er det rot med brukerid-en i databasen.
	
	//sjekker om man er logget inn
	if(!er_logget_inn()){
		header('Location: ../index.php');
	};
	
	//hvis det er lagt til et nytt innlegg legges dette inn i databasen
	if(has_post('temaid')){
		$tekst=post('tekst');
		$sql="INSERT INTO forum_innlegg_ny (temaid, tekst, skrevet, skrevetavid, sistredigert) 
			VALUES ('".post('temaid')."','".$tekst."','".date('Y-m-d H:i:s')."','".post('medlemsid')."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);
		//henter ut id til det nye innlegget
		$id = mysql_insert_id();
		//henter ut liste over alle aktive medlemmer
		$sql="SELECT medlemsid FROM medlemmer WHERE status!='Sluttet';";
		$aktivemedlemmer=hent_og_putt_inn_i_array($sql, "medlemsid");
		$sql="INSERT INTO `forum_leste`(`medlemsid`, `uleste_innlegg`, `temaid`) 
			VALUES ";
		foreach ($aktivemedlemmer as $medlemsid => $medlem) {
			$sql.="('".$medlem['medlemsid']."','".$id."','".post('temaid')."'),";
		}
		$sql=substr($sql,0,-1);
		mysql_query($sql);
	};
	
	//hvis noen har skrevet seg pï¿½ en liste
	if(has_post('listeinnlegg')){
		//fjerner alt skummelt fra kommentarfeltet og setter inn feltet
		$kommentar=post('kommentar');
		if(post('flagg')==1){$flagg=1;}else{$flagg=0;};
		//sql - databasen er sï¿½nn at pdd. kan du ikke melde deg pï¿½ nï¿½r du allerede er pï¿½meldt
		$sql="INSERT INTO forum_listeinnlegg_ny (listeid, flagg, brukerid, kommentar, tid) 
			VALUES ('".post('listeinnlegg')."','".$flagg."','".$_SESSION["medlemsid"]."','".$kommentar."','".date('Y-m-d h:i:s')."')";
		mysql_query($sql);	
	};
	
	$temaid=get('id');
	//henter ut alle innleggene i valgte forum/tema 
	$sql="SELECT forum_tema.temaid, forum_innlegg_ny.innleggid, forum_innlegg_ny.tekst, forum_innlegg_ny.skrevetavid,  
	forum_innlegg_ny.skrevet, medlemmer.medlemsid, medlemmer.fnavn, medlemmer.enavn FROM forum_tema, forum_innlegg_ny, medlemmer 
	WHERE forum_tema.temaid=".$temaid." AND forum_innlegg_ny.temaid=".$temaid." AND forum_innlegg_ny.skrevetavid=medlemsid ORDER BY skrevet;";
	$foruminnlegg=hent_og_putt_inn_i_array($sql, "innleggid");
	
	//henter listeid til alle innlegg i valgte forum og tema som det er en liste knyttet til
	$sql="SELECT forum_liste.listeid, forum_liste.tittel FROM forum_liste, forum_innlegg_ny
		WHERE forum_liste.listeid=forum_innlegg_ny.innleggid;";
	$listeinnlegg=hent_og_putt_inn_i_array($sql, "listeid");	
	
	//henter ut alle aktuelle liste-oppfï¿½ringer
	$sql="SELECT medlemsid, fnavn, enavn, forum_listeinnlegg_ny.listeid, forum_listeinnlegg_ny.tid, forum_listeinnlegg_ny.innleggid, 
	forum_listeinnlegg_ny.kommentar, forum_listeinnlegg_ny.flagg, forum_liste.expires 
	FROM forum_liste, forum_innlegg_ny, forum_listeinnlegg_ny, forum_tema, medlemmer 
	WHERE forum_liste.listeid=forum_innlegg_ny.innleggid AND forum_liste.listeid=forum_listeinnlegg_ny.listeid AND
	 forum_tema.temaid=".$temaid." AND forum_innlegg_ny.temaid=".$temaid." AND brukerid=medlemmer.medlemsid ORDER BY tid ;";
	$listeoppforinger=hent_og_putt_inn_i_array($sql, "innleggid");	
		
	//Henter ut siste uleste innlegg i trï¿½d
	$medlemsid= $_SESSION["medlemsid"];
	$sql="SELECT uleste_innlegg FROM forum_leste WHERE temaid=".$temaid." AND medlemsid=".$medlemsid.";";
	$result = mysql_query($sql);
	$ulesteinnlegg=hent_og_putt_inn_i_array($sql, "uleste_innlegg");		
	
	//Henter ut tema-tittel
	$sql="SELECT tittel, temaid FROM forum_tema WHERE temaid=".$temaid.";";
	$mysql_result=mysql_query($sql);
	$tema = mysql_fetch_array($mysql_result);
	
	//Setter alle innlegg i aktuelle tråd som lest i databasen (så neste gang blir de merket som lest)
	$sql = "DELETE FROM `forum_leste` WHERE temaid=".$temaid." AND medlemsid=".$medlemsid.";";
	mysql_query($sql);
	
    #Det som printes pï¿½ sida
    
    //Her legges det inn en oversikt over alle forumene
    list_forum();
	//skriver ut temaet for denne trÃ¥den
    echo "<table class='forum'><tr><th colspan = '3'>".$tema['tittel']."</th></tr>";

   	//skriver ut alle innleggene valgte forum og tema i forumet sortet pÃ¥ sist oppdaterte med siste innlegg og av hvem
   	foreach($foruminnlegg as $innleggId => $forum_innlegg){
      	if($ulesteinnlegg[$forum_innlegg['innleggid']]){
	      	echo "<tr class='ulest'>";
		}
		else{
			echo"<tr>";
		}
		echo "<td class='liten_tekst'>".strftime("%a %d. %b", strtotime($forum_innlegg['skrevet']))." skrev ".$forum_innlegg['fnavn']." ".$forum_innlegg['enavn']." </td>
   			<td>".$forum_innlegg['tekst'];
		
      	//if som skriver ut liste hvis det hï¿½rer en liste til innlegget
		if($listeinnlegg[$forum_innlegg['innleggid']]){
			echo "<table>
			<tr><th colspan='2'>".$listeinnlegg[$forum_innlegg['innleggid']]['tittel']."</th></tr>";
			foreach($listeoppforinger as $listeoppforing){
				if($listeoppforing['listeid']==$forum_innlegg['innleggid']){
					if($listeoppforing['flagg']==1){
						echo "<tr><td><strike>".$listeoppforing['fnavn']." ".$listeoppforing['enavn']."</strike>";
					}else{
						echo "<tr><td>".$listeoppforing['fnavn']." ".$listeoppforing['enavn'];
					};
					echo "</td><td>".$listeoppforing['kommentar']."</td></tr>";
					
					#For å vite om bruker står på lista og dermed ikke kan skrive seg på på nytt
					if($listeoppforing['medlemsid']==$_SESSION["medlemsid"]){$oppfort_paa_liste=1;}	
				};	
			};
			//Legger til tekstfelt for ï¿½ melde seg pï¿½ hvis ikke lista har expired
			
			if($oppfort_paa_liste==1){
				echo "<tr><td colspan='2'><b>Du er allerede skrevet på lista</b></td></tr> ";						
			}elseif(strtotime(date('Y-m-d'))/(60*60*24) <= strtotime(substr($listeoppforing['expires'],0,10))/(60*60*24) || $listeoppforing['expires']==NULL){
			echo "<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
				<tr><td>Kommentar (frivillig):<br><input type='text' name='kommentar' autofocus><br><input type='checkbox' name='flagg' value='1'> Stryk navnet</td>
				<td><input type='hidden' name='medlemsid' value='".$_SESSION['medlemsid']."'>
				<input type='hidden' name='listeinnlegg' value='".$listeinnlegg[$forum_innlegg['innleggid']]['listeid']."'>
				<input type='submit' name='nyttListeInnlegg' value='Skriv meg pï¿½ lista'></td></tr>";
			}else{
				echo "<tr><td colspan='2'><b>Det er ikke lenger mulig ï¿½ melde seg pï¿½ denne lista</b></td></tr> ";	
			};
			echo "</form></table>";
  		};
  		echo "</td><td><a href''>liker</a></td></tr>";
	};	
	echo "
	<form class='forum' method='post' action='?side=forum/innlegg&id=".$temaid."'>
			<tr><td>Svar pÃ¥ innlegg:</td><td><textarea name='tekst' autofocus></textarea></td>
			<td><input type='hidden' name='medlemsid' value='".$_SESSION['medlemsid']."'>
			<input type='hidden' name='temaid' value='".$temaid."'>
			<input type='submit' name='nyttInnlegg' value='Lagre'></td></tr>
		</form> 
	</table>";
?>