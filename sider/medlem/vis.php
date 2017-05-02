<?php 
//TODO Bilde og fikse kolonnebredde
	
	//funksjonalitet
	$id=get('id');
	
	if(!has_get('id') || empty($id)) {
		throw new Exception();
	}

	//henter valgte medlem fra databasen
	$medlem = hent_brukerdata($id);
	
	echo '
		<section class="informasjonslinje">
			<h2 class="back-link"><a href="?side=medlem/liste" title="Vis medlemsliste">
				<i class="fa fa-chevron-left"></i>Medlemmer</a>
			</h2>
			
			';
			
			if(session('medlemsid')==$id || session('rettigheter') >2){
				echo"<div class='verktoy'><a href='?side=medlem/endre&id=".$id."'><i class='fa fa-edit'></i>Endre</a></div>";
			}
echo '
		</section>';
	
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
	$bilde = isset($medlem['foto']) ? $medlem['foto'] : "";
	?>

<article class="medlem">
	
<?php if (!empty($bilde) && ($medlem['begrenset'] == 0 || er_logget_inn())) { ?>
<div class="profilbilde"><img src='<?php echo thumb($bilde, 300); ?>' /></div>
<?php } ?>

<h1><?php echo $medlem['fnavn']." ".$medlem['enavn']; ?></h1>

<p>
	<strong>Instrument:</strong> <?php echo $medlem['instrument']; ?>
	<?php if($medlem['grleder']==1){ ?>
		<span class="tag gruppeleder">Gruppeleder</span></b>
	<?php } ?>
</p>

<p>
	<strong>Status:</strong> <?php echo $medlem['status']; ?>
</p>

<?php if(er_logget_inn()){ ?>
	<p>
		<strong>Født:</strong> <?php echo isset($medlem['fdato']) ? date("d. m. Y", strtotime($medlem['fdato'])) : "Ukjent"; ?>
	</p>
	
	<?php if(isset($medlem['startetibuk_date']) && strtotime($medlem['startetibuk_date']) > 0) { ?>
	<p>
		<strong>Startet i BUK:</strong> <?php echo date("d. M Y", strtotime($medlem['startetibuk_date'])); ?>
	</p>
	<?php } ?>
	<?php if(isset($medlem['sluttetibuk_date']) && strtotime($medlem['sluttetibuk_date']) > 0) { ?>
	<p>
		<strong>Sluttet i BUK:</strong> <?php echo date("d. M Y", strtotime($medlem['sluttetibuk_date'])); ?>
	</p>
	<?php } ?>
	
	<?php if(isset($medlem['studieyrke']) && !empty($medlem['studieyrke'])) { ?>
	<p>
		<strong>Studie/yrke:</strong> <?php echo $medlem['studieyrke']; ?>
	</p>
	<?php } ?>
	
<?php } ?>

<?php if(isset($medlem['kommerfra']) && !empty($medlem['kommerfra'])){ ?>
	<p>
		<strong>Kommer fra:</strong> <?php echo $medlem['kommerfra']; ?>
	</p>
<?php } ?>

<?php if(isset($medlem['bil']) && $medlem['bil'] && tilgang_endre()){ ?>
	<p>
		<strong>Bil:</strong> Har tilgang på bil
	</p>
<?php } ?>


<?php if(isset($medlem['hengerfeste']) && $medlem['hengerfeste'] && tilgang_endre()){ ?>
	<p>
		<strong>Hengefeste:</strong> Har tilgang på bil med hengerfeste
	</p>
<?php } ?>

<?php if(er_logget_inn()) { ?>
	<h4>Kontaktinfo</h4>

	<?php if(isset($medlem['tlfmobil'])) { ?>
	<p>
		<strong>Mobil:</strong> 
		<a href="tel:<?php echo $medlem['tlfmobil']; ?>"><?php echo $medlem['tlfmobil']; ?></a>
	</p>
	<?php } ?>
	
	<?php if(isset($medlem['email'])) { ?>
	<p>
		<strong>E-post:</strong> 
		<a href="mailto:<?php echo $medlem['email']; ?>"><?php echo $medlem['email']; ?></a>
	</p>
	<?php } ?>
	
	<?php if(isset($medlem['adresse'])) { ?>
	<p>
		<strong>Adresse:</strong> <?php echo $medlem['adresse']; ?>
		<br /> <?php echo $medlem['postnr']." ".$medlem['poststed']; ?>
	</p>
	<?php } ?>


<?php } ?>
	
<?php if(er_logget_inn() && isset($medlem['ommegselv'])) { ?>
<h4>Litt om meg</h4>
<p><?php echo nl2br($medlem['ommegselv']); ?></p>
<?php } ?>

<?php if(!empty($medlem['bakgrunn'])){ ?>
<h4>Musikalsk bakgrunn</h4>
<p><?php echo nl2br($medlem['bakgrunn']); ?></p>
<?php } ?>

</article>
