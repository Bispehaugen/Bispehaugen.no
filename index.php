<?php

setlocale(LC_TIME, "nb_NO.utf8");
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
	$side = "forside";
}

ob_start();
	inkluder_side_fra_undermappe($side, "sider");
$innhold = ob_get_clean();
?>


<!DOCTYPE HTML>
<html>
<head>
    <meta charset="UTF-8"/>
    <title>Bispehaugen Ungdomskorps</title>
	<link rel="stylesheet" href="css/fonts.css" type="text/css" /> 
    <link rel="stylesheet" href="css/font-awesome.css" type="text/css" />
    <link rel="shortcut icon" href="icon_logo.png" type="image/png">
    <link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/aktivitet.css" type="text/css" />
    <script type="text/javascript" src='js/jquery-1.10.2.min.js'></script>
    <script type="text/javascript" src="js/jquery.timeago.js"></script>
    <script type="text/javascript" src="js/jquery.timeago.no.js"></script>
    
    <?php if (erForside()) { ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
    <?php } ?>
    
    <?php if (er_logget_inn()) { ?>
	<link rel="stylesheet" href="css/forum.css" type="text/css" />
	<link rel="stylesheet" href="css/internside.css" type="text/css" />
   	<?php } ?>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src='scrollIt.js' type='text/javascript'></script>
    <link href='http://fonts.googleapis.com/css?family=Dosis:300,400,500&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Nunito:400,700,300' rel='stylesheet' type='text/css'>
    <link href='css/font-awesome.css' rel='stylesheet'>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  
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

<body>
	<div class="site">
		<?php if (erForside() && !er_logget_inn()) { ?>
		<section class="forside side coverflow" data-scroll-index='1'>
	   		<div class="stottemedlem reklame">Korps er ikke billig, bli <em><a class="bli-medlem" data-scroll-nav='5'>støttemedlem</a></em> i dag!</div>

		
	    	<header class="header">
				<img class="logo" src="icon_logo.png" />
		    	<h1 class="title"><span class="bispehaugen">Bispehaugen</span><br /> <span class="ungdomskorps">Ungdomskorps</span></h1>
	      	</header>
	    </section>
	    <?php } ?>
	    
		<div class='nav-container'>
	        <nav>
	            <div class="meny mobil"><i class="fa fa-bars"></i> Meny</div>
	            <?php
					inkluder_side_fra_undermappe("meny");
				?>
	        </nav>
			<div class="clearfix"></div>
    	</div>
    	
<?php
#sjekker om det er satt noen errors og evt. skriver dem ut
if (isset($_SESSION["Errors"])) {
	echo "<div class='errors'>
		" . $_SESSION["Errors"] . "
		</div>";
	
	unset($_SESSION["Errors"]);
}
?>
    	
	<?php if(!er_logget_inn() && erForside()) { ?>
		<section class="login">
			<div class="errors feilmelding">
				Kunne ikke logge inn, brukernavn eller passord er feil. Kontakt webkom hvis dette fortsetter :)
			</div>
			<div class="login-box">
				<h2 class="overskrift">Internsiden</h2>
				<form action="login.php" method="POST">
					<label><input id="username" name="username" type="text" placeholder="Brukernavn" required="required" /><i class="fa fa-2x fa-user"></i></label>
					<label><input id="password" name="password" type="password" placeholder="Passord" required="required" /><i class="fa fa-2x fa-asterisk"></i></label>
					<button class="login-button"><i class="spinner fa fa-circle-o-notch fa-spin"></i>Logg inn</button>
				</form>
			</div>
			<div class="clearfix"></div>
		</section>
	<?php } ?>
    	
		<main class="main">
			<a name="main"></a>
			<?php
			
			if (!erForside()) {
				echo "<section class=\"side side-invertert\" data-scroll-index='2'>
						<div class='content'>";
						echo $innhold;
				echo "	</div>
					</section>";
			} else {
			 	echo $innhold;
			 }
			?>
		</main>
		
<?php if(erForside() && !er_logget_inn()) { ?>
	<script>
	var onPageChange = function(index) {
		var url = $("[data-scroll-index='"+index+"']").data("scroll-url");
		changeHash(url);
	};

    $.scrollIt({
	  scrollTime: 300,       // how long (in ms) the animation takes
	  onPageChange: onPageChange,    // function(pageIndex) that is called when page is changed
	});

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

    $("nav > ul > li > a").click(function(event) {
        event.preventDefault();
        changeHash($(this).attr("href"));
    });
</script>
<?php } ?>

<script>

    var navElements = $("nav > ul > li");
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

	<?php if (er_logget_inn()) { ?>
		$("li.profilbilde")
			.mouseenter(function() {
				$(".profilbilde-valg").show();
			})
			.mouseleave(function() {
				$(".profilbilde-valg").hide();
			});
	<?php } ?>
    
    <?php if (!er_logget_inn()) { ?>
    	    	
    	var clearErrors = function() {
    		$(".login-button").prop("disabled", false);
    		$(this).removeClass("error");
    	};
    	
    	var login = function(event) {
    		event.preventDefault();
    		
    		$(this).attr("disabled", "disabled");
    		
    		var username = $(".login-box #username");
    		var password = $(".login-box #password");
    		
    		var has_error = false;
    		
    		if (username.val().length == 0) {
    			username.addClass("error");
    			has_error = true;
    		}
    		
    		if (password.val().length == 0) {
    			password.addClass("error");
    			has_error = true;
    		}
    		
    		if (has_error) {
    			$(this).prop("disabled", false);
    			
    			return;
    		}
    		
    		clearErrors();
    		
    		$(".login .spinner").show();
    		
    		var data = {username: username.val(), password: password.val()};
    		
    		$.post("login.php?ajax=true", data)
    			.done(function(data){
    				location.reload(true);
    			})
    			.fail(function(data){
    				debugger;
    				$(".login .feilmelding").show();
    				
    				// Videresendes på done, så trenger ikke å fjerne spinner der, ser bare rart ut
    				clearErrors();
    				$(".login .spinner").hide();
    			});
    	};
    	
    	$(".login-button").click(login);
    	
    	$(".login input").focus(clearErrors);
    <?php } ?>
    
</script>

<?php if (erForside() && !er_logget_inn()) { ?>
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
