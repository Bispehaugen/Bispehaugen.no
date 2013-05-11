<?php 
//TODO Bilde, sjekk på at alle obligatoriske felter er fyllt ut, hvis man endrer seg selv autogenerer mail til webkom/sekretær
	
	//funksjonalitet
	
	//sjekker om man er admin eller prøver å endre seg selv
	if($_SESSION['medlemsid']==$_GET['id']){
		$endre_seg_selv=1;//brukes for å sende mail til sekretær ved endring
	}
	elseif($_SESSION['rettigheter']<2){
		header('Location: ?side=medlem/liste');
	};
	
	//henter ut alle instrumenter
		$sql="SELECT instrument, posisjon, instrumentid FROM instrument ORDER BY posisjon";
		$instrumenter=hent_og_putt_inn_i_array($sql, $id_verdi='posisjon');

	//hvis et medlem er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(isset($_POST['id']) || $_POST['fnavn']){
		$medlemsid=mysql_real_escape_string($_POST['id']);
		$fnavn=mysql_real_escape_string($_POST['fnavn']);
		$enavn=mysql_real_escape_string($_POST['enavn']);
		$instrument=mysql_real_escape_string($_POST['instrument']);		
		$instnr=mysql_real_escape_string($_POST['instnr']);		
		$grleder=mysql_real_escape_string($_POST['grleder']);
			if($grleder==1){
				$sql="SELECT medlemsid FROM medlemmer WHERE grleder=1 AND instnr=".$instrument.";";
				$mysql_result=mysql_query($sql);
				$medl=mysql_fetch_array($mysql_result);
				$sql2="UPDATE medlemmer SET grleder=0 WHERE medlemsid=".$medl['medlemsid']." AND instrument=".$instrument.";";
			}

		$status=mysql_real_escape_string($_POST['status']);
		$fdato=mysql_real_escape_string($_POST['fdato']);
		$adresse=mysql_real_escape_string($_POST['adresse']);
		$postnr=mysql_real_escape_string($_POST['postnr']);
		$poststed=mysql_real_escape_string($_POST['poststed']);
		$tlfmobil=mysql_real_escape_string($_POST['tlfmobil']);
		$email=mysql_real_escape_string($_POST['email']);
		$bakgrunn=mysql_real_escape_string($_POST['bakgrunn']);
		$startetibuk=mysql_real_escape_string($_POST['startetibuk_date']);
		$sluttetibuk=mysql_real_escape_string($_POST['sluttetibuk_date']);
		$studieyrke=mysql_real_escape_string($_POST['studieyrke']);
		$kommerfra=mysql_real_escape_string($_POST['kommerfra']);
		$ommegselv=mysql_real_escape_string($_POST['ommegselv']);
		$foto=mysql_real_escape_string($_POST['foto']);
		$begrenset=mysql_real_escape_string($_POST['begrenset']);
		
		//sjekker om man vil legge til eller endre et medlem
		if ($medlemsid){
			$sql="
			UPDATE 
				medlemmer 
			SET 
				fnavn = '$fnavn',
				enavn = '$enavn',
				fdato = '$fdato',
				status = '$status',
				instnr = '$instrument',
				grleder = '$grleder',
				adresse = '$adresse',
				postnr = '$postnr',
				poststed = '$poststed',
				tlfmobil = '$tlfmobil',
				email = '$email',
				bakgrunn = '$bakgrunn',
				startetibuk_date = '$startetibuk',
				sluttetibuk_date = '$sluttetibuk',
				studieyrke = '$studieyrke',
				kommerfra = '$kommerfra',
				ommegselv = '$ommegselv',
				foto = '$foto',
				begrenset = '$begrenset' 
			WHERE 
				medlemsid = '$medlemsid';
			";
			
			echo "loop1".$sql;
			mysql_query($sql);
			//header('Location: ?side=medlem/liste');
		}else{
			die("WRONG");			
			$sql="
			INSERT INTO 
			medlemmer (fnavn, enavn, fdato, status, instrument, instnr, grleder, adresse, postnr, poststed, tlfmobil, 
				email, bakgrunn, startetibuk_date, sluttetibuk_date, studieyrke, kommerfra, ommegselv, foto, begrenset)
			values ('$fnavn','$enavn','$fdato','$status','$instrument','$instnr','$grleder','$adresse','$postnr','$poststed','$tlfmobil',
				'$email','$bakgrunn','$startetibuk','$sluttetibuk','$studieyrke','$kommerfra','$ommegselv','$foto','$begrenset')";
			mysql_query($sql);
			//header('Location: ?side=medlem/liste');
		}
		};
	//henter valgte medlem fra databasen hvis "endre"
	if(isset($_GET['id'])){	
		$id=mysql_real_escape_string($_GET['id']);
		$medlemmer = hent_brukerdata($id);
	} else {
		$medlemmer = hent_brukerdata();
	}
	$gyldige_statuser = Array("Aktiv", "Permisjon", "Sluttet");
	
	//printer ut skjema med forh�ndsutfylte verdier hvis disse eksisterer
		
	echo "
    <script>
    $(function() {
        $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' }).val();;
    });
    </script>
		<form method='post' action='?side=medlem/endre'>
			<table>
				<th>Rediger medlem</th><th></th>
				<tr><td>Gruppeleder:</td><td><input type='checkbox' name='grleder' value=".$medlemmer['grleder']."></td></tr>
				<tr><td>Fornavn:</td><td><input type='text' name='fnavn' value=".$medlemmer['fnavn']."></td></tr>
				<tr><td>Etternavn:</td><td><input type='text' name='enavn' value=".$medlemmer['enavn']."></td></tr>
				<tr><td>status:</td><td>
					<select name='status'>
					";
					
					foreach($gyldige_statuser as $status) {
						echo "<option value='$status' ";
						if($medlemmer['status']=="$status") {
							echo "selected=selected";
						} 
						echo ">".$status."</option>";
					}
					echo "
					</select></td></tr>
				<tr><td>Instrument:</td><td>
					<select name='instnr'>";
					foreach($instrumenter as $instrument){
							echo"
  							<option value=".$instrument['instrumentid']." ";
  							if($instrument['instrument']==$medlemmer['instrument']){
  								echo "selected";
  							};
  							echo ">".$instrument['instrument']."</option>";
					};
					echo "<input type='hidden' name='instrument' value=".$instrument['instrument']."></select></td></tr>
				<tr><td>Fødselsdato:</td><td><input type='text' class='datepicker' name='fdato' value=".$medlemmer['fdato']."></td></tr>
				<tr><td>Adresse:</td><td><input type='text' name='adresse' value=".$medlemmer['adresse']."></td></tr>
				<tr><td>Postnr:</td><td><input type='text' name='postnr' value=".$medlemmer['postnr']."></td></tr>
				<tr><td>Poststed:</td><td><input type='text' name='poststed' value=".$medlemmer['poststed']."></td></tr>
				<tr><td>Mobilnummer:</td><td><input type='text' name='tlfmobil' value=".$medlemmer['tlfmobil']."></td></tr>
				<tr><td>E-post:</td><td><input type='text' name='email' value=".$medlemmer['email']."></td></tr>
				<tr><td>Musikalsk bakgrunn:</td><td><input type='text' name='bakgrunn' value=".$medlemmer['bakgrunn']."></td></tr>
				<tr><td>Startet i BUK:</td><td><input type='text' class='datepicker' name='startetibuk_date' value=".$medlemmer['startetibuk_date']."></td></tr>
				<tr><td>Sluttet i BUK:</td><td><input type='text' class='datepicker' name='sluttetibuk_date' value=".$medlemmer['sluttetibuk_date']."></td></tr>
				<tr><td>Studie/yrke:</td><td><input type='text' name='studieyrke' value=".$medlemmer['studieyrke']."></td></tr>
				<tr><td>Kommer fra:</td><td><input type='text' name='kommerfra' value=".$medlemmer['kommerfra']."></td></tr>
				<tr><td>Litt om meg selv:</td><td><textarea name='ommegselv'>".$medlemmer['ommegselv']."</textarea></td></tr>
				<tr><td>Bilde:</td><td><input type='text' name='foto' value=".$medlemmer['foto']."></td></tr>
				<tr><td>Vises kun for innloggede:</td><td><input type='checkbox' name='begrenset' value=".$medlemmer['begrenset']."></td></tr>
				</table>
			<input type='hidden' name='id' value=".$medlemmer['medlemsid'].">
			<a href='?side=medlem/liste'>Avbryt</a>
			<input type='submit' name='endreMedlem' value='Lagre'>
		</form> 
	";
?>
	