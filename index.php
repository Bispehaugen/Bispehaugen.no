<?php

// Fjern denne linja
setlocale(LC_TIME, "Norwegian");
include_once "db_config.php";
include_once "funksjoner.php";

$tilkobling = koble_til_database($database_host, $database_user, $database_string, $database_database);

if ($tilkobling === false) {
	exit ;
}

if (has_get("loggut")) {
	logg_ut();
}


if (!$er_produksjon) {
	if(file_exists("migrering.php")){
		include("migrering.php");
	}
}

//lagrer alt innhold som en variabel
if(has_get('side')){
	$side = get('side');
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
    <link rel="stylesheet" href="css/style.css" type="text/css" />
    <link rel="stylesheet" href="css/font-awesome.css" type="text/css" />
    <link rel="stylesheet" href="css/font-awesome-ie7.css" type="text/css" />
    <link rel="shortcut icon" href="bilder/icon_logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/forum.css" type="text/css" />
	<link rel="stylesheet" href="css/aktivitet.css" type="text/css" />
    <link rel="shortcut icon" href="bilder/icon_logo.png" type="image/png" />
    <script type="text/javascript" src='http://code.jquery.com/jquery-1.11.0.js'></script>
    <script type="text/javascript" src="javascript/jquery.timeago.js"></script>
    <script type="text/javascript" src="javascript/jquery.timeago.no.js"></script>
    
    <?php if (erForside()) { ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
    <?php } ?>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='scrollIt.js' type='text/javascript'></script>
    <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Nunito:400,700,300' rel='stylesheet' type='text/css'>
    <link href='css/font-awesome.css' rel='stylesheet'>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
  
	<script type="text/javascript">
		$(document).ready(function() {
			$("abbr.timeago").timeago();
		});

		function confirm_url(url, tekst) {
			var bekreft = confirm(tekst);
			if (bekreft) {
				window.location = url;
			}
		}
	</script>
</script>
</head>

<body class="">
	<div class="site">
		<section class="forside side coverflow" data-scroll-index='1'>
	      <a name="forside"></a>
	      <div class="stottemedlem reklame">Korps er ikke billig, bli <em><a class="bli-medlem" data-scroll-nav='5'>støttemedlem</a></em> i dag!</div>
	      
	      <header class="header">
	      <img class="logo" src="icon_logo.png" />
	      <h1 class="title"><span class="bispehaugen">Bispehaugen</span><br /> <span class="ungdomskorps">Ungdomskorps</span></h1>
	      </header>
	    </section>
		<div class='nav-container'>
	        <nav>
	            <div class="meny mobil"><i class="fa fa-bars"></i> Meny</div>
	            <?php
					inkluder_side_fra_undermappe("meny");
				?>
	        </nav>
    	</div>
		<main class="main">
			<a name="main"></a>
			<?php
			#sjekker om det er satt noen errors og evt. skriver dem ut
			if (isset($_SESSION["Errors"])) {
				echo "<div class='errors'>
					" . $_SESSION["Errors"] . "
					</div>";
				
				unset($_SESSION["Errors"]);
			}
			
			if (!erForside()) {
				echo "<section class=\"side alene-side\" data-scroll-index='2'>";
				echo $innhold;
				echo "</section>";
			} else {
			 	echo $innhold;
			 }
			?>
		</main>

<script>
    $.scrollIt();

    function changeHash(href) {
        if (href) {
            if (history.pushState) {
                history.pushState(null, null, href);
            } else {
                var scrollTopBeforeHashChange = $('body').scrollTop();
                window.location.hash = href;
                $('html,body').scrollTop(scrollTopBeforeHashChange);
            }
        }
    }

    $("nav a").click(function(event) {
        event.preventDefault();
        changeHash($(this).attr("href"));
    });

    var navElements = $("nav li");
    var nav = $("nav").first();

    function resizeHeight() {
        var height = getHeight();

        var forsideHeight = height - nav.height();


        $(".coverflow").css("height", height+"px");
        $(".forside.coverflow").css("height", forsideHeight+"px");
    }

    function getHeight() {
        var height = window.innerHeight;
        if (!window.innerHeight) {
            height = documentElement.clientHeight;
        }
        return height;
    }

    $("nav .meny").click(function(event) {
        $("nav.top ul").toggle();
    });

    resizeHeight();
    $( window ).resize(function() {
        resizeHeight();
    });

    $( window ).scroll(function() {

        var top = $(window).scrollTop();
        var navContainer = $(".nav-container");
        var navContainerTop = navContainer.position().top;
        var normalNavHeight = false;

        nav.toggleClass("top", (top >= navContainerTop));

        if ($(window).width() > 700) {

            if (nav.hasClass("top")) {
                var windowHeight = getHeight();
                var navHeight = navContainer.height();

                normalNavHeight = true;

                if ((top >= (navContainerTop))) {
                    var newNavHeight = navHeight-(top - navContainerTop);

                    if (newNavHeight <= 113 && newNavHeight >= 49) {
                        navElements.css("line-height", newNavHeight+"px");
                        normalNavHeight = false;
                    }
                }
            }

            if (normalNavHeight) {
                nav.css("height", "49px");
                navElements.css("line-height", "49px");
            }

            if (!nav.hasClass("top")) {
                nav.css("height", "113px");
                navElements.css("line-height", "113px");
            }
        }
    });
</script>

<?php if (erForside()) { ?>
<footer id="map_canvas" class="map"></footer>

<script>
function initialize() {
    var mapCanvas = document.getElementById('map_canvas');
    var latLong = new google.maps.LatLng(63.431466, 10.414018)
    var mapOptions = {
      center: latLong,
      zoom: 15,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    var map = new google.maps.Map(mapCanvas, mapOptions);
    
    var marker = new google.maps.Marker({
        position: latLong,
        map: map,
        title: "Bispehaugen Skole"
     });
     
     marker.setMap(map);
}

google.maps.event.addDomListener(window, 'load', initialize);
</script>
<?php } ?>

</body>
</html>
