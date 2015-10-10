<?php 
	//TODO: mangler fortsatt test på tidsformat, og en liste for å koble slagverksbærere til medlemmer
	
	$feilmeldinger = Array();
	
	//sjekker om man er admin
	if(!tilgang_endre()){
		header('Location: ?side=aktiviteter/liste');
		die();
	}
	$aktiviteter = Array();

	//hvis en aktivitet er lagt inn og noen har trykket på lagre hentes verdiene ut
	if(has_post()) {
		$id = post('id');
		$tittel = post('tittel');
		$public = post('public');
		$type = post('type');
		$ingress = post('ingress');
		$sted = post('sted');
		$dato = has_post('dato') ? array_unique(post('dato')) : Array();
		$oppmote = post('oppmoetetid');
		$starttid = post('starttid');
		$sluttid = post('sluttid');
		$hjelpere = post('hjelpere');
		$kakebaker = post('kakebaker');

		if (!isset($tittel) || $tittel=="") { 
		   $feilmeldinger[] =  "Du må fylle inn tittel"; 
		} 
		elseif (!isset($sted) || $sted=="") { 
		   $feilmeldinger[] =  "Du må fylle inn sted"; 
		}
		elseif (empty($dato) || !isset($oppmote) || $oppmote=="" || !isset($starttid) || $starttid=="" || !isset($sluttid) || $sluttid=="") { 
		   $feilmeldinger[] =  "Du må fylle inn dato, oppmøtetid, start og sluttid"; 
		} 
		elseif (strtotime($oppmote) > strtotime($starttid)) { 
		   $feilmeldinger[] =  "Oppmøte må være før starttiden"; 
		} 
		elseif (strtotime($starttid) > strtotime($sluttid)) { 
		   $feilmeldinger[] =  "Starttid må være før sluttiden"; 
		}
		if (empty($feilmeldinger)) {

			//sjekker om man vil legge til eller endre en aktivitet
			if ($id){
				$dato = $dato[0];

				$sql="UPDATE arrangement SET tittel='".$tittel."',sted='".$sted."',dato='".$dato."',oppmoetetid='".$oppmote."'
				,start='".$dato." ".$starttid."',slutt='".$dato." ".$sluttid."',ingress='".$ingress."',public='".$public."',type='".$type."',hjelpere='".$hjelpere."'
				,kakebaker='".$kakebaker."' WHERE arrid='".$id."';";
				mysql_query($sql);
				header('Location: ?side=aktiviteter/liste');
			} else {
				print_r($dato);

				foreach($dato as $d) {
					$sql="INSERT INTO arrangement (tittel,type,sted,dato,oppmoetetid,start,slutt,ingress,beskrivelsesdok,public,hjelpere,kakebaker)
	values ('$tittel','$type','$sted','$d','$oppmote','$d $starttid','$d $sluttid','$ingress','','$public','$hjelpere','$kakebaker')";
					mysql_query($sql);
				}
				
				header('Location: ?side=aktiviteter/liste');
			}
		}

		$aktiviteter = Array(
			"tittel" => $tittel,
			"public" => $public,
			"ingress" => $ingress,
			"sted" => $sted,
			"dato" => $dato,
			"oppmoetetid" => $oppmote,
			"start" => $starttid,
			"slutt" => $sluttid,
			"hjelpere" => $hjelpere,
			"kakebaker" => $kakebaker
		);
	}
	$handling = "Ny";

	$arrid = post('id');
	
	//henter valgte aktivitet fra databasen
	if(has_get('id')){	
		#Hente ut valgte nyhet hvis "endre"
		$arrid=get('id');
		$sql="SELECT * FROM `arrangement` WHERE `arrid`=".$arrid;
		$mysql_result=mysql_query($sql);
		$aktiviteter=mysql_fetch_array($mysql_result);
		$handling = "Endre";

		$public = kanskje($aktiviteter, 'public');
	}
	
	//henter ut alle medlemmer som kakebaker
	$sql="SELECT fnavn, enavn, medlemsid FROM medlemmer WHERE status='Aktiv' ORDER BY fnavn";
	$mysql_result=mysql_query($sql);
	while($row=mysql_fetch_array($mysql_result)){
		$medlemmer[$row['medlemsid']] = $row;
	}
		
	?>
	<section class="informasjonslinje">
	  	<h2><?php echo $handling; ?> aktivitet</h2>

	  	<?php if(!has_get('id') && session('rettigheter')>1){ ?>
	  	<span class='verktoy standard-oving'><i class='fa fa-check'></i>Fyll ut standard øving</span>
		<span class='verktoy flere-dato'><i class='fa fa-calendar-o'></i>Ekstra dato</span>
		<?php } ?>
	</section>

    <script>
    $(function() {
        $('.datepicker').pickadate();
        $('.timepicker').pickatime({interval: 15});

        var flereDato = function() {
        	$(".dato").after("<tr>" + $(".dato").html() + "</tr>");
        	$('.datepicker').pickadate();
        };

        var fjernDato = function() {
        	$(this).parents("tr").remove();
        };

        var standardOving = function() {
        	$(".tittel").val("Øvelse");
        	$(".sted").val("Bispehaugen");
        	$(".oppmoetetid").val("19:15");
        	$(".starttid").val("19:30");
        	$(".sluttid").val("22:00");
        };

        <?php if (!has_get('id')) { ?>
    		$('.flere-dato').click(flereDato);

    		$('.fjern-dato').click(fjernDato);

    		$('.standard-oving').click(standardOving);

    	<?php } ?>
    });
    </script>

<?php 
echo feilmeldinger($feilmeldinger);

$gyldige_typer = Array("Øvelse", "Seminar", "Dugnad", "Sosialt", "Spilleoppdrag", "Møte", "Tur", "Annet");

$aktivitetsdato = kanskje($aktiviteter, 'dato');
$datoer = is_array($aktivitetsdato) ? $aktivitetsdato : Array(0 => $aktivitetsdato);
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
		
	echo "
		<form method='post' action='?side=aktiviteter/endre'>
			<table>
				<tr><td>Tittel:</td><td><input type='text' class='tittel' name='tittel' value='".kanskje($aktiviteter, 'tittel')."'></td></tr>
				<tr><td>Hvem kan se den:</td><td>
					<select name='public'>
  						<option value='1' ".($public==1?"selected='selected'":"").">Alle (Åpen på internett)</option>
  						<option value='0' ".(($public==0 || !isset($public))?"selected='selected'":"").">Intern (Bare korpsmedlemmer)</option>";
  						if (tilgang_full()){
  							echo "<option value='2' ".($public==2?"selected='selected'":"").">Admin</option>";
  						}
  				echo "
					</select></td></tr>
				<tr><td>Type:</td><td>
					<select name='type'>
					";
					
					foreach($gyldige_typer as $type) {
						$selected = (kanskje($aktiviteter, 'type')=="$type") ? " selected=selected" : "";
						
						echo "<option value='".$type."'".$selected.">".$type."</option>";
					}
					echo "
					</select></td></tr>
				<tr><td>Ingress:</td><td><input type='text' name='ingress' value='".kanskje($aktiviteter, 'ingress')."'></td></tr>
				<tr><td>Sted:</td><td><input type='text' class='sted' name='sted' value='".kanskje($aktiviteter, 'sted')."'></td></tr>
				<tr><td></td><td>* dato oppgis på formen yyyy-mm-dd og tidpunkter oppgis på formen tt:mm.</td></tr>
				";
				$i = 0;
				foreach($datoer as $d) {
					echo "<tr class='dato'><td>Dato: ";
					if ($i > 0) {
						echo "<i class='fjern-dato fa fa-times'></i>";
					}
					echo "</td><td><input type='text' class='datepicker' name='dato[]' value='".$d."'></td></tr>";
					$i++;
				}
				
echo "
				<tr><td>Oppmøte kl:</td><td><input type='text' class='timepicker oppmoetetid' name='oppmoetetid' value='".bare_tidspunkt(kanskje($aktiviteter, 'oppmoetetid'))."'></td></tr>
				<tr><td>Start kl:</td><td><input type='text' class='timepicker starttid' name='starttid' value='".bare_tidspunkt(kanskje($aktiviteter, 'start'))."'></td></tr>
				<tr><td>Slutt kl:</td><td><input type='text' class='timepicker sluttid' name='sluttid' value='".bare_tidspunkt(kanskje($aktiviteter, 'slutt'))."'></td></tr>
				<tr><td>Slagverksbærere:</td><td><input type='text' name='hjelpere' value='".kanskje($aktiviteter, 'hjelpere')."'></td></tr>
				<tr><td>Kakebaker:</td><td>
					<select name='kakebaker'>
					<option value='NULL'>Ingen</option>";
					foreach($medlemmer as $medlem){
						echo"<option value='".$medlem['medlemsid']."'";

						if ($medlem['medlemsid'] == kanskje($aktiviteter, 'kakebaker')) {
							echo " selected=selected";
						}
						echo "'>".$medlem['fnavn']." ".$medlem['enavn']."</option>";
					}
					echo "</select></td></tr>

					<tr>
						<td colspan=2>
							<p class='right'>
							<a href='?side=aktiviteter/liste'>Avbryt</a>
							<input type='submit' name='endreNyhet' value='Lagre'>
							</p>
						</td>
					</tr>
			</table>
			<input type='hidden' name='id' value='".$arrid."'>
		</form> 
	";
?>