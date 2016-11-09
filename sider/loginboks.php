<script type="text/javascript">

	var clearErrors = function() {
		$(".login-button").prop("disabled", false);
		$(this).removeClass("error");
	};
	
	var login = function(event) {
		event.preventDefault();
		
		$(this).attr("disabled", "disabled");
		
		var epost = $(".login-box #epost");
		var password = $(".login-box #password");
        var husk = $(".login-box #husk_meg");
        var skal_huske = "Nei";
        if (husk.is(":checked")) {
            skal_huske = "Ja";
        }
		
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
		
		var data = {epost: epost.val(), password: password.val(), husk_meg: skal_huske};
		
		$.post("login.php?ajax=true", data)
			.done(function(data){
				window.scrollTo(0, 0);
				if ($(".login #erForside").val() == "1") {
					window.location = window.location.pathname;
				} else {
					location.reload(true);
				}
			})
			.fail(function(data){
				$(".login .feilmelding").show();
				
				// Videresendes på done, så trenger ikke å fjerne spinner der, ser bare rart ut
				clearErrors();
				$(".login .spinner").hide();
			});
	}
    
    $("body")
    	.on('click', '.login-button', login)
    	.on('focus', '.login input', clearErrors);

</script>
<section class="login">
		<?php
			
			$feilmeldinger = session("Errors");
			if($feilmeldinger!=""){
				echo feilmeldinger(Array($feilmeldinger));
				$_SESSION["Errors"]  = "";
			};
							?>
	<div class="login-box">
		<h2 class="overskrift">Internsiden</h2>
		<p class="glemt-passord"><a href="?side=glemt-passord">Glemt passord?</a></p>
		<form action="login.php" method="POST">
			<input type="hidden" name="erForside" id="erForside" value="<?php echo erForside(); ?>" />
			<label><input id="epost" name="epost" type="text" placeholder="E-post" required="required" /><i class="fa fa-2x fa-user"></i></label>
			<label><input id="password" name="password" type="password" placeholder="Passord" required="required" /><i class="fa fa-2x fa-asterisk"></i></label>
            <input id="husk_meg" name="husk_meg" type="checkbox" value="Ja" /><label for="husk_meg">Husk meg <i class="fa fa-2x fa-check-square-o"></i><i class="fa fa-2x fa-square-o"></i></label>
			<button class="login-button button"><i class="spinner fa fa-circle-o-notch fa-spin"></i>Logg inn</button>
		</form>
	</div>
	<div class="clearfix"></div>
</section>
