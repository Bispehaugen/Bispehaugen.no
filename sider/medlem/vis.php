<?php 
//TODO Bilde og fikse kolonnebredde
	
	//funksjonalitet

	
	$id=get('id');
	
	if (empty($id)) {
		inkluder_side_fra_undermappe("ikke_funnet");	
	}
		
	//henter valgte medlem fra databasen
	$medlemmer = hent_brukerdata($id);
	
	echo '
		<section class="informasjonslinje">
			<h2 class="back-link"><a href="?side=medlem/liste" title="Vis medlemsliste">
				<i class="fa fa-chevron-left"></i> Medlemmer</a>
			</h2>
			
			';
			
			if(session('medlemsid')==$id || session('rettigheter') >2){
				echo"<div class='verktoy'><a href='?side=medlem/endre&id=".$id."'><i class='fa fa-edit'></i> Endre</a></div>";
			}
echo '
		</section>';
	
	//printer ut skjema med forhåndsutfylte verdier hvis disse eksisterer
	
	$bilde = isset($medlemmer['foto']) ? $medlemmer['foto'] : "";
	?>

<article class="medlem">
	
<?php if (!empty($bilde)) { ?>
<div class="profilbilde"><img src='<?php echo $bilde; ?>' /></div>
<?php } ?>

<h1><?php echo $medlemmer['fnavn']." ".$medlemmer['enavn']; ?></h1>

<p>
	<strong>Instrument:</strong> <?php echo $medlemmer['instrument']; ?>
	<?php if($medlemmer['grleder']==1){ ?>
		<span class="tag gruppeleder">Gruppeleder</span></b>
	<?php } ?>
</p>

<p>
	<strong>Status:</strong> <?php echo $medlemmer['status']; ?>
</p>

<?php if(!er_logget_inn()){ ?>
	<p>
		<strong>Født:</strong> <?php echo isset($medlemmer['fdato']) ? date("d. m. Y", strtotime($medlemmer['fdato'])) : "Ukjent"; ?>
	</p>
	
	<?php if(isset($medlemmer['startetibuk_date'])) { ?>
	<p>
		<strong>Startet i BUK:</strong> <?php echo date("d. M Y", strtotime($medlemmer['startetibuk_date'])); ?>
	</p>
	<?php } ?>
	<?php if(isset($medlemmer['sluttetibuk_date'])) { ?>
	<p>
		<strong>Sluttet i BUK:</strong> <?php echo date("d. M Y", strtotime($medlemmer['sluttetibuk_date'])); ?>
	</p>
	<?php } ?>
	
	<?php if(isset($medlemmer['studieyrke'])) { ?>
	<p>
		<strong>Studie/yrke:</strong> <?php echo $medlemmer['studieyrke']; ?>
	</p>
	<?php } ?>
	
	<h4>Kontaktinfo</h4>
	<?php if(isset($medlemmer['tlfmobil'])) { ?>
	<p>
		<strong>Mobil:</strong> 
		<a href="tel:<?php echo $medlemmer['tlfmobil']; ?>"><?php echo $medlemmer['tlfmobil']; ?></a>
	</p>
	<?php } ?>
	
	<?php if(isset($medlemmer['email'])) { ?>
	<p>
		<strong>E-post:</strong> 
		<a href="mailto:<?php echo $medlemmer['email']; ?>"><?php echo $medlemmer['email']; ?></a>
	</p>
	<?php } ?>
	
	<?php if(isset($medlemmer['adresse'])) { ?>
	<p>
		<strong>Adresse:</strong> <?php echo $medlemmer['adresse']; ?>
		<br /> <?php echo $medlemmer['postnr']." ".$medlemmer['poststed']; ?>
	</p>
	<?php } ?>


<?php } ?>
<?php if(!empty($medlemmer['kommerfra'])){ ?>
<p>
	<strong>Kommer fra:</strong> <?php echo $medlemmer['kommerfra']; ?>
</p>
<?php } ?>

	
<?php if(er_logget_inn() && isset($medlemmer['ommegselv'])) { ?>
<h4>Litt om meg</h4>
<p><?php echo nl2br($medlemmer['ommegselv']); ?></p>
<?php } ?>

<?php if(!empty($medlemmer['bakgrunn'])){ ?>
<h4>Musikalsk bakgrunn</h4>
<p><?php echo nl2br($medlemmer['bakgrunn']); ?></p>
<?php } ?>

</article>