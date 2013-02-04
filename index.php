<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
include_once "db_config.php";
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

if (isset($_GET["loggut"])) {
	logg_ut();
}

//Henter ut mobilnummeret til leder
$leder = hent_og_putt_inn_i_array("SELECT tlfmobil, fnavn, enavn FROM medlemmer, verv WHERE medlemmer.medlemsid=verv.medlemsid AND verv.komiteid='3' AND verv.tittel='Leder'");

//lagrer alt innhold som en variabel
if(isset($_GET['side'])){
	$side = $_GET['side'];
} else {
	$side = "sider";
}

ob_start();
	inkluder_side_fra_undermappe($side, "sider");
$innhold = ob_get_clean();
?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="ISO-8859-1"/>
    <title>Bispehaugen Ungdomskorps</title>
	<link rel="stylesheet" href="css/fonts.css" type="text/css" /> 
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="shortcut icon" href="bilder/icon_logo.png" type="image/png">
    <link rel='stylesheet' href='http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css' />
    <link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/forum.css" type="text/css" />
	<link rel="stylesheet" href="css/aktivitet.css" type="text/css" />
    <link rel="shortcut icon" href="bilder/icon_logo.png" type="image/png" />
    <script type="text/javascript" src='http://code.jquery.com/jquery-1.8.2.js'></script>
    <script type="text/javascript" src='http://code.jquery.com/ui/1.9.0/jquery-ui.js'></script>
  
	<script type="text/javascript">
		$(document).ready(function() {
			$('.banner_picture h3').mouseover(function() {
				$('.banner_picture').removeClass("choosen");
				$(this).parent().parent().addClass("choosen");

				$("#banner_picture").attr("src", $(this).parent().parent().find("img").attr("src"));
			});

		});
	</script>
</script>
</head>

<body class="">
	<div id="header_stripe"></div>
	<div class="site">
		<div class="logo_column">
			<a href=""><div class="logo"></div></a>
			<div class="figurer"></div>
			
			<?php
			if(er_logget_inn()){
				
				inkluder_side_fra_undermappe("liten_profil");
				
			} else { 
				
				inkluder_side_fra_undermappe("innloggingsform");
			}
			?>
			
		</div>
		<div class="center_and_right_column">
			<div class="header">
				<h1 class="hidden">Bispehaugen Ungdomskorps</h1>
				<?php
				inkluder_side_fra_undermappe("meny");
				?>
			</div>
			
			<div class="center_column">

				<?php
				#sjekker om det er satt noen errors og evt. skriver dem ut
				if (isset($_SESSION["errors"])) {
					echo "<div class='errors'>
						" . $_SESSION['errors'] . "
						</div>";
				}
				?>
				
				<div class="content">			
					<?php
					 echo $innhold;
					?>
				</div>
			</div>	
		
			<div class="right_column">
				<?php
				inkluder_side_fra_undermappe("informasjonskolonne", "sider");
				?>
			</div>
			
		</div>
	</div>

<div class="footer">
	<div class="footer_content">
		<h5>Kontakt oss</h5>
		<div class="box">
			Bispehaugen Ungdomskorps<br />
			Postboks 9012<br />
			Rosenborg 7455 Trondheim<br />
			<br />
			Organisasjonnummer: 975.729.141<br />
			Kontonummer: 4200 07 51280
		</div>
		<div class="box">
			<table style="border: 0; width: 100%;">
				<tr>
					<td>E-post:</td>
					<td><script type="text/javascript">document.write("<a href=\"mailto:sty" + "ret" + "@" + "bispe" + "haugen" + "." + ".no\">");</script>styre<span class="hidden">EAT THIS ROBOTS</span>@bispehaugen.no</a></td>
				</tr>
				<tr>
					<td>Tlf leder:</td>
					<td>+47 <?php
					echo $leder['tlfmobil'];
					?></td>
				</tr>
				<tr>
					<td>Facebook:</td>
					<td><a href="https://www.facebook.com/BispehaugenUngdomskorps">facebook.com/BispehaugenUngdomskorps</a></td>
				</tr>
			</table>
		</div>
	</div>
</div>

</body>
</html>
