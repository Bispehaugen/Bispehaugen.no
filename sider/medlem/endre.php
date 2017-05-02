<?php 
//TODO Bilde, sjekk på at alle obligatoriske felter er fyllt ut, hvis man endrer seg selv autogenerer mail til webkom/sekretær

    global $dbh;
	global $opprett_ny_medlem;
	$endre_seg_selv = false;
	
	//funksjonalitet

	if (has_get('id')) {
		$id = get('id');
	} elseif(has_post('id')){
		$id = post('id');
	} else {
		if(isset($_SESSION['medlemsid'])) {
			$id = $_SESSION['medlemsid'];
		} else {
			die("Du må logge inn");
		}
	}
	
	$bruker = hent_brukerdata($id);
	
	//sjekker om man er admin eller prøver å endre seg selv
	if($_SESSION['medlemsid']==$id){
		$endre_seg_selv=true;//brukes for å sende mail til sekretær ved endring
	}
	elseif($_SESSION['rettigheter']<3){
		header('Location: ?side=medlem/liste');
		die();
	};
	
	//henter ut alle instrumenter
	$sql="SELECT posisjon, instrument, instrumentid FROM instrument ORDER BY posisjon";
	$instrumenter=hent_og_putt_inn_i_array($sql);
	
	$feilmeldinger = Array();
	//hvis et medlem er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post('id') || has_post('fnavn')){
		$medlemsid=post('id');
		$fnavn=post('fnavn');
		$enavn=post('enavn');
		$brukernavn=post('brukernavn');
		$instnr=post('instnr');		
		$grleder=post('grleder') ? post('grleder') : 0;
		$status=post('status');
		$fdato=date("Y-m-d", strtotime(post('fdato')));
		$adresse=post('adresse');
		$postnr=post('postnr');
		$poststed=post('poststed');
		$hengerfeste=post('hengerfeste') ? post('hengerfeste') : 0;
		$bil=post('bil') ? post('bil') : 0;
		$tlfmobil=post('tlfmobil');
		$email=post('email');
		$bakgrunn=post('bakgrunn');
		$startetibuk=post('startetibuk_date');
        $sluttetibuk=post('sluttetibuk_date') != "0000-00-00" && post('sluttetibuk_date') != ""
            ? post('sluttetibuk_date') : NULL;
		$studieyrke=post('studieyrke');
		$kommerfra=post('kommerfra');
		$ommegselv=post('ommegselv');
		$foto = post('foto');
		$begrenset=post('begrenset') ? post('begrenset') : 0;
		if(has_post('rettigheter')){
			$rettigheter=post('rettigheter');
		}else if(isset($bruker['rettigheter'])){
			$rettigheter=$bruker['rettigheter'];
		}else{
			$rettigheter='0';
		};

		if(empty($fnavn) || empty($enavn)) {
			$feilmeldinger[] = "Navn må være fylt ut";
		} else if (empty($fdato) || strtotime($fdato) == 0) {
			$feilmeldinger[] = "Fødselsdato må være fylt ut";
		} else if (empty($adresse) || empty($poststed)) {
			$feilmeldinger[] = "Adresse og poststed må være fylt ut";
		} else if (empty($tlfmobil) || empty($email)) {
			$feilmeldinger[] = "Mobil og epost må være fylt ut";
		} else if (empty($fdato) && strtotime($fdato) > 0) {
			$feilmeldinger[] = "Fødselsdato må være fylt ut";
		}
		
		if (empty($feilmeldinger)) {
			
			if (empty($foto)) {
				$foto = $bruker['foto'];
			}
		
			if($grleder==1){
				$sql="SELECT medlemsid, instrument FROM medlemmer WHERE grleder=1 AND instnr=?";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array($instnr));
				
				while ($medl = $stmt->fetch()) {
					$sql2 = "UPDATE medlemmer SET grleder=0 WHERE medlemsid=? AND instrument=?";
                    $stmt2 = $dbh->prepare($sql2);
                    $stmt2->execute(array($medl['medlemsid'], $medl['instrument']));
				}
			}
	
			//harEndretAdresse
			if ($endre_seg_selv && !empty($bruker) && ($bruker['adresse'] != $adresse || $bruker['fnavn'] != $fnavn || $bruker['enavn'] != $enavn )) {
				$message = "
Adresseendring:
$fnavn $enavn har endret adressen sin til:
$adresse
$postnr $poststed

Den gamle adressen var:
" . $bruker['adresse'] . "
" . $bruker['postnr'] . " " . $bruker['poststed'];
				
				$to = "webkom@bispehaugen.no";
				$replyto = $fnavn." ".$enavn." <".$email.">";
				$subject = "Bispehaugen.no - Adressendring";
				
				epost($to,$replyto,$subject,$message);
	 		}
			
			//sjekker om man vil legge til eller endre et medlem
			if ($medlemsid){
				$sql="
				UPDATE 
					medlemmer 
				SET 
					fnavn = ?,
					enavn = ?,
					fdato = ?,
					status = ?,
					instnr = ?,
					grleder = ?,
					adresse = ?,
					postnr = ?,
					poststed = ?,
					hengerfeste = ?,
					bil = ?,
					tlfmobil = ?,
					email = ?,
					bakgrunn = ?,
					startetibuk_date = ?,
					sluttetibuk_date = ?,
					studieyrke = ?,
					kommerfra = ?,
					ommegselv = ?,
					begrenset = ?, 
					rettigheter = ?
				WHERE 
					medlemsid = ?;
				";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array($fnavn, $enavn, $fdato, $status, $instnr, $grleder, $adresse, $postnr,
                    $poststed, $hengerfeste, $bil, $tlfmobil, $email, $bakgrunn, $startetibuk, $sluttetibuk, 
                    $studieyrke, $kommerfra, $ommegselv, $begrenset, $rettigheter, $medlemsid));
				innlogget_bruker_oppdatert();
			} else {
				$sql="
				INSERT INTO 
				medlemmer (fnavn, enavn, fdato, status, instnr, grleder, adresse, postnr, poststed, hengerfeste, bil, tlfmobil, 
					email, bakgrunn, startetibuk_date, sluttetibuk_date, studieyrke, kommerfra, ommegselv, begrenset, rettigheter)
				values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $stmt = $dbh->prepare($sql);
                $stmt->execute(array($fnavn, $enavn, $fdato, $status, $instnr, $grleder, $adresse, $postnr,
                    $poststed, $hengerfeste, $bil, $tlfmobil, $email, $bakgrunn, $startetibuk, $sluttetibuk, 
                    $studieyrke, $kommerfra, $ommegselv, $begrenset, $rettigheter));
				$medlemsid = $dbh->lastInsertId();
			}
			header('Location: ?side=medlem/vis&id='.$medlemsid);
			die();
		}
	}
	//henter valgte medlem fra databasen hvis "endre"
	if(has_get('id')||has_post('id')){	
		$medlemmer = hent_brukerdata($id);
	} else if ($opprett_ny_medlem) {
		$medlemmer = Array();
	} else {
		$medlemmer = hent_brukerdata();
	}
	$gyldige_statuser = Array("Aktiv", "Permisjon", "Sluttet", "Ubekreftet");
	$gyldige_rettigheter = Array("0", "1", "2", "3");
	$navn_gyldige_rettigheter = Array("Ikke tilgang", "Internsider", "Admin light", "Full admin");
    if (session("rettigheter") == 10) {
        $gyldige_rettigheter[] = "10";
        $navn_gyldige_rettigheter[10] = "Sysadmin";
    }
	
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
?>


<?php		
	echo "
    	<h2>".($opprett_ny_medlem ? "Nytt medlem" : "Rediger medlem")."</h2>";
    	
    	echo feilmeldinger($feilmeldinger);
    	
    	echo "
		<form method='post' action='?side=medlem/endre'>
			<table>
				";
				$gruppelederCheck = (kanskje($medlemmer, "grleder") == 1) ? "checked" : "";
				echo "<tr><td>Gruppeleder:</td><td><input type='checkbox' name='grleder' value='1' ".$gruppelederCheck." /></td></tr>";
				echo "
				<tr><td>Fornavn:</td><td><input type='text' name='fnavn' value='".kanskje($medlemmer, 'fnavn')."'></td></tr>
				<tr><td>Etternavn:</td><td><input type='text' name='enavn' value='".kanskje($medlemmer, 'enavn')."'></td></tr>
				<tr><td>status:</td><td>
					<select name='status'>
					";
					
					foreach($gyldige_statuser as $status) {
						$selected = (kanskje($medlemmer, 'status')=="$status") ? " selected=selected" : "";
						
						echo "<option value='".$status."'".$selected.">".$status."</option>";
					}
					echo "
					</select></td></tr>
				<tr><td>Instrument:</td><td>
					<select name='instnr'>";
					foreach($instrumenter as $instrument){
						$selected = ($instrument['instrument']==kanskje($medlemmer, 'instrument')) ? " selected=selected" : "";
						
						echo"<option value='".$instrument['instrumentid']."'".$selected.">".$instrument['instrument']."</option>";
					}
					echo "</select>";
		
					echo "
				<tr><td>Fødselsdato:</td><td><input type='date' name='fdato' value='".kanskje($medlemmer, 'fdato')."'></td></tr>
				<tr><td>Adresse:</td><td><input type='text' name='adresse' value='".kanskje($medlemmer, 'adresse')."'></td></tr>
				<tr><td>Postnr:</td><td><input type='text' name='postnr' value='".kanskje($medlemmer, 'postnr')."'></td></tr>
				<tr><td>Poststed:</td><td><input type='text' name='poststed' value='".kanskje($medlemmer, 'poststed')."'></td></tr>
				<tr><td>Bil:</td><td><label><input type='checkbox' name='bil' value='1' ".((kanskje($medlemmer, 'bil')==1) ? "checked":"")."> Tilgang på bil</label></td></tr>
				<tr><td>Hengerfeste:</td><td><label><input type='checkbox' name='hengerfeste' value='1' ".((kanskje($medlemmer, 'hengerfeste')==1) ? "checked":"")."> Tilgang på bil med hengerfeste</label></td></tr>
				<tr><td>Mobilnummer:</td><td><input type='text' name='tlfmobil' value='".kanskje($medlemmer, 'tlfmobil')."'></td></tr>
				<tr><td>E-post:</td><td><input type='text' name='email' value='".kanskje($medlemmer, 'email')."'></td></tr>
				<tr><td>Musikalsk bakgrunn:</td><td><textarea name='bakgrunn'>".kanskje($medlemmer, 'bakgrunn')."</textarea></td></tr>
				<tr><td>Startet i BUK:</td><td><input type='text' class='datepicker' name='startetibuk_date' value='".kanskje($medlemmer, 'startetibuk_date')."'></td></tr>
				<tr><td>Sluttet i BUK:</td><td><input type='text' class='datepicker' name='sluttetibuk_date' value='".kanskje($medlemmer, 'sluttetibuk_date')."'></td></tr>
				<tr><td>Studie/yrke:</td><td><input type='text' name='studieyrke' value='".kanskje($medlemmer, 'studieyrke')."'></td></tr>
				<tr><td>Kommer fra:</td><td><input type='text' name='kommerfra' value='".kanskje($medlemmer, 'kommerfra')."'></td></tr>
				<tr><td>Litt om meg selv:</td><td><textarea name='ommegselv'>".kanskje($medlemmer, 'ommegselv')."</textarea></td></tr>
				";
				$begrensetChecked = (kanskje($medlemmer, 'begrenset') == 1) ? "checked" : "";
				
				if (!$opprett_ny_medlem) {
				echo "
				<tr class='dropzone'>
					<td>Bilde:</td>
					<td>
						<div id='bytt-bilde'>
							<img src='".thumb(kanskje($medlemmer, 'foto'), 200)."' />
							<i class='ikon fa fa-edit'></i>
							<div class='spinner'><i class='fa fa-spinner fa-spin'></i></div>
						</div>
						<p><label><input type='checkbox' name='begrenset' value='1' ".$begrensetChecked." /> Vis bilde kun for innloggede</label></p>
					</td>
				</tr>
				";
				
				}
				if(session('rettigheter')>2){
					echo"<tr><td>Rettigheter*:</td><td>
					<select name='rettigheter'>";
					foreach($gyldige_rettigheter as $rettighet){
						$selected = (kanskje($medlemmer, 'rettigheter')=="$rettighet") ? " selected=selected" : "";						
						echo"<option value='".$rettighet."'".$selected.">".$navn_gyldige_rettigheter[$rettighet]."</option>";
					}
					echo "</select><br>*admin light kan endre nyheter, endre aktiviteter og legge til dokumenter. Det kreves full admin for å se styre-forum/styre-dokumenter, endre medlemmer og tilganger.";
				};
				
				echo "</td></tr>
				<tr>
				<td></td>
				<td class='right'>
					<a href='?side=medlem/liste'>Avbryt</a>
					<input type='submit' name='endreMedlem' value='Lagre'>
				</td>
				</tr>
				</table>
			<input type='hidden' name='id' value='".kanskje($medlemmer, 'medlemsid')."'>
			
		</form> 
	";
	
if (!$opprett_ny_medlem) {
?>
<script src="vendor/Flow/flow.js"></script>

<script>

var flow = new Flow({
  target:'sider/medlem/upload.php',
  singleFile: true,
  query: {
  	medlemsid: '<?php echo $id; ?>'
  }
});
flow.assignBrowse(document.getElementById('bytt-bilde'));
flow.assignDrop($('.dropzone'));

var preview = $("#bytt-bilde img, .liten.profilbilde");
var spinner = $("#bytt-bilde .spinner");

flow.on('fileAdded', function(fileinfo, event){

	// Legg til upload greie
	spinner.show();

	var reader = new FileReader();
	reader.onload = function (e) {
		if(e.srcElement.result.indexOf('data:image/') == 0) {
			preview.attr('src', e.target.result);
		} else {
			alert("Filen du lastet opp så ikke ut som ett bilde");
		}
	}
	reader.readAsDataURL(fileinfo.file);
});

flow.on('filesSubmitted', function(array, event){
	flow.upload();
});

flow.on('fileSuccess', function(file,message){
	
	flow.removeFile(file);

	spinner.hide();
});

flow.on('fileError', function(file){
	console.log("Filopplastingen feilet med fil: ", file);
	
	var oldPic = '<?php echo kanskje($medlemmer, 'foto'); ?>';
	preview.attr('src', oldPic);
	
	// Vis feilmelding
	alert("Oppdateringen av profilbilde feilet dessverre. Webkom er varslet og vil se på saken.");
});
</script>
<?php }
