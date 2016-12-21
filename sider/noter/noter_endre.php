<?php 	
    global $dbh;

	//funksjonalitet
	//sjekker om man er admin
	if($_SESSION['rettigheter']<2){
		header('Location: ?side=noter/liste');
	};
		
	// //hvis en aktivitet er lagt inn og noen har trykket på� lagre hentes verdiene ut
	 if(has_post('noteid')){
		  $noteid=post('noteid');
		  $tittel=post('tittel');
		  $komponist=post('komponist');
		  $arrangor=post('arrangor');
		 $arkivnr=post('arkivnr');
		 if(empty($filpath)){$filpath=clean($tittel);}
		 $besetningsid=post('besetningsid');
	     //sjekker om man vil legge til eller endre et notesett
		 if ($noteid){
			$sql="UPDATE noter_notesett SET tittel=?,komponist=?,arrangor=?,
			arkivnr=?,besetningsid=? WHERE noteid=?";
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array($tittel, $komponist, $arrangor, $arkivnr, $besetningsid, $noteid));

			header('Location: ?side=noter/noter_oversikt');
		 }else{
		 	//utkommenteres for å ikke lage bøtter og spamm av mapper lokalt
		 	//echo "<pre>".shell_exec("mkdir ../noter/".$filpath)."</pre>";
			$sql="INSERT INTO noter_notesett (tittel,komponist,arrangor,arkivnr,besetningsid,filpath) values (?,?,?,?,?,?)";
            $stmt = $dbh->prepare($sql);
            $stmt->execute(array($tittel, $komponist, $arrangor, $arkivnr, $besetningsid, "/noter/$filpath"));
			header('Location: ?side=noter/noter_oversikt');
		 }
			};
	
	//henter valgte notesett fra databasen
	if(has_get('noteid')){
		$noteid=get('noteid');
		$sql="SELECT * FROM `noter_notesett` WHERE `noteid`=?";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($noteid));
		print_r($stmt->fetch(););
	};
	
	//henter ut alle besetningstyper
    $sql="SELECT * FROM noter_besetning";
    foreach ($dbh->query($sql) as $row) {
        $besetningstyper[$row['besetningsid']] = $row;
    };
		
		
	
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
		
	echo " 
		<form method='post' action='?side=noter/noter_endre'>
			<table>";
				if(has_get('noteid')){
					echo"<th>Endre notesett</th><th></th>";
				}
				else{
					echo"<th>Nytt notesett</th><th></th>";
				};
				echo"<tr><td>Tittel:</td><td><input type='text' name='tittel' value='".$noter['tittel']."'></td></tr>
				<tr><td>Komponist:</td><td><input type='text' name='komponist' value='".$noter['komponist']."'></td></tr>
				<tr><td>Arrangør:</td><td><input type='text' name='arrangor' value='".$noter['arrangor']."'></td></tr>
				<tr><td>Arkivnr:</td><td><input type='text' name='arkivnr' value='".$noter['arkivnr']."'></td></tr>
				<tr><td>Besetningstype:</td><td>
					<select name='besetningsid'>";
					foreach($besetningstyper as $besetning){
						echo"
  							<option value='".$besetning['besetningsid']."'";
							if($besetning['besetningsid']==$noter['besetningsid']){
  								echo " selected";
  							};
  							echo">".$besetning['besetningstype']."</option>";
						};
						echo "</select></td></tr>
			</table>
			<input type='hidden' name='noteid' value='".get('noteid')."'>
			<a href='?side=noter/noter_oversikt'>Avbryt</a>
			<input type='submit' name='endreNote' value='Lagre'>
		</form>"; 
				
		echo"<p>
		Info til deg som skal legge inn noter:
		<ul>
		<li>- Endringer i lista over besetningstyper?? - kontakt webkom</li>
		<li>- Koblingen mellom noter og konserter gjøres på aktivitetssida. Merk! Arrangementet bør være en konsert</li>
		<li>- Webkom oppfordrer til å ha intrumentnavn i filnavnet på opplastede noter</li>
		<li>- Filplassering er automatisk genererert og kan ikke endres. </li>
		<li>- Enn så lenge er det ikke mulig å slette notesett</li>
		</ul>
		</p>
		
		Her kommer boks for å velge filer
	";
?>
