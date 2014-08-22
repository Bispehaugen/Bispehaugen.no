<?php

setlocale(LC_TIME, "Norwegian", "nb_NO", "nb_NO.utf8");
include_once "db_config.php";
include_once "funksjoner.php";

if (er_logget_inn()) {
	include_once "sider/intern/funksjoner.php";
}

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
    <meta name="Description" CONTENT="Bispehaugen Ungdomskorps er Trondheims eldste amatørkorps, startet i 1923. På denne siden finner du våre neste konsert, hvem som er medlem og hvordan du kan bli medlem." />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="keywords" content="Bispehaugen Ungdomskorps, Bispehaugen, Ungdomskorps, Korps, Band, Woodwind band, Trondheim, Korps Trondheim, Janitsjar, Janitsjarkorps, Janitsjarkorps Trondheim, 1923, NTNU, studentkorps, student, student korps" />
	
	<link rel="shortcut icon" href="icon_logo.png" type="image/png" />
	
	<link rel="stylesheet" href="css/fonts.css" type="text/css" /> 
	<link rel="stylesheet" href="css/style.css" type="text/css" />
	<link rel="stylesheet" href="css/aktivitet.css" type="text/css" />
	
	<link async href='http://fonts.googleapis.com/css?family=Dosis:300,400,500&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
	<link async href='http://fonts.googleapis.com/css?family=Nunito:400,700,300' rel='stylesheet' type='text/css'>
	
	<?php if (er_logget_inn()) { ?>
	<link rel="stylesheet" href="css/forum.css" type="text/css" />
	<link rel="stylesheet" href="css/internside.css" type="text/css" />
	<?php } ?>
</head>

<body>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-50487161-1', 'auto');
	  ga('send', 'pageview');
	</script>
	<div class="site<?php if(erForside()) { echo " forside"; } ?>">
		<?php if (erForside() && !er_logget_inn()) { ?>
		<section class="forside side coverflow" data-scroll-index='1'>
	   		<div class="stottemedlem reklame">Bli <em><a class="bli-medlem" data-scroll-nav='5'>støttemedlem</a></em> i dag!</div>

		
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
    	
	<?php if(!er_logget_inn() && erForside()) { ?>
		<section class="login">
			<div class="errors feilmelding">
				<?php
					echo feilmeldinger($_SESSION["Errors"]);
				?>
			</div>
			<div class="login-box">
				<h2 class="overskrift">Internsiden</h2>
				<p class="glemt-passord"><a href="?side=glemt-passord">Glemt passord?</a></p>
				<form action="login.php" method="POST">
					<label><input id="epost" name="epost" type="text" placeholder="E-post" required="required" /><i class="fa fa-2x fa-user"></i></label>
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
<link rel="stylesheet" href="css/font-awesome.css" type="text/css" />
<script type="text/javascript" src='js/jquery-1.10.2.min.js'></script>
<script type="text/javascript" src="js/jquery.timeago.js"></script>
<script type="text/javascript" src="js/jquery.timeago.no.js"></script>

<?php if (visKartNederst()) { ?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js"></script>
<?php } ?>

<?php if (er_logget_inn()) { ?>
<link rel="stylesheet" href="vendor/pickadate/themes/default.css" type="text/css" />
<link rel="stylesheet" href="vendor/pickadate/themes/default.date.css" type="text/css" />
<link rel="stylesheet" href="vendor/pickadate/themes/default.time.css" type="text/css" />

<script type="text/javascript" src="vendor/pickadate/legacy.js"></script>
<script type="text/javascript" src="vendor/pickadate/picker.js"></script>
<script type="text/javascript" src="vendor/pickadate/picker.date.js"></script>
<script type="text/javascript" src="vendor/pickadate/picker.time.js"></script>
<script type="text/javascript" src="vendor/pickadate/translations/no_NO.js"></script>

<?php } ?>
<script src='scrollIt.js' type='text/javascript'></script>
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

    $("nav .meny").click(function(event) {
        $("nav .menyliste").toggle();
    });

	<?php if (er_logget_inn()) { ?>
		$("li.profilbilde")
			.mouseenter(function() {
				$(".profilbilde-valg").show();
			})
			.mouseleave(function() {
				$(".profilbilde-valg").hide();
			});

		$("li.arkiv")
			.mouseenter(function() {
				$(".arkiv-valg").show();
			})
			.mouseleave(function() {
				$(".arkiv-valg").hide();
			});
	<?php } ?>
    
    <?php if (!er_logget_inn()) { ?>


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
    	    	
	var clearErrors = function() {
		$(".login-button").prop("disabled", false);
		$(this).removeClass("error");
	};
	
	var login = function(event) {
		event.preventDefault();
		
		$(this).attr("disabled", "disabled");
		
		var epost = $(".login-box #epost");
		var password = $(".login-box #password");
		
		var has_error = false;
		
		if (epost.val().length == 0) {
			epost.addClass("error");
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
		
		var data = {epost: epost.val(), password: password.val()};
		
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


<footer class="footer">
	<?php
	if (er_logget_inn()) {
		inkluder_side_fra_undermappe("intern/bunn");
	} else if (visKartNederst()) {
		inkluder_side_fra_undermappe("kart_bunn");
	}
	?>
</footer>
</body>
</html>
