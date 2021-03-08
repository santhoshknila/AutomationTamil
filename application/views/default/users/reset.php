<!DOCTYPE html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		 <!-- Twitter -->
		<meta name="twitter:site" content="#">
		<meta name="twitter:creator" content="#">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:title" content="Tamil Chamber of Commerce South Africa">
		<meta name="twitter:description" content="Tamil Chamber of Commerce South Africa">
		<meta name="twitter:image" content="http://tamilchamber.org.za/gallery_gen/7ec8795f84616e84cd354da3a0b056fb_130x130.jpg">

		<!-- Facebook -->
		<meta name="og:title" content="Home - Tamil Chamber of Commerce South Africa" />
		<meta name="og:description" content="Tamil Chamber of Commerce South Africa" />
		<meta name="og:image" content="http://tamilchamber.org.za/gallery_gen/7ec8795f84616e84cd354da3a0b056fb_130x130.jpg" />
		<meta name="og:type" content="article" />
		<meta name="og:url" content="http://tamilchamber.org.za/" />
		<!-- Meta -->
		<meta name="description" content="Tamil Chamber of Commerce South Africa.">
		<meta name="author" content="Tamil Chamber">

		<title><?PHP echo $title; ?></title>
		
		<?=$_scripts?> 
		<link rel="icon" href="<?php echo base_url(); ?>skin/default/images/favicon.png" type="images/png" sizes="24x24"> 
		<?=$_styles?>
	</head>
	<body>
		<div class="d-flex align-items-center justify-content-right bg-br-primary ht-100v">
			<div class="login-wrapper wd-300 wd-xs-350 pd-25 pd-xs-30 bg-black rounded shadow-base">
				<div class="signin-logo tx-center tx-28 mg-b-30 tx-bold tx-inverse">
					<img src="<?PHP echo default_theme_skin_path("images/logo.png")?>" alt="Tamil Ethos" style="width:250px;" />
				</div>
				<form id="resetpassForm" method="post" name="resetpassForm" action="<?php echo site_url('users/resetpassword');?>" data-parsley-validate >	
					<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
					<div class="form-group">
						<input type="hidden" class="form-control" name="token" value="<?PHP echo $token; ?>" />
						<input type="hidden" class="form-control" name="emailid" value="<?PHP echo $emailid; ?>" />
						<label>Password <span class="tx-danger">*</span></label>
						<input type="password" class="form-control" name="password" id="password" required />
					</div><!-- form-group -->
					<div class="form-group">
						<label>Confirm Password <span class="tx-danger">*</span></label>
						<input type="password" class="form-control" name="conpassword" id="conpassword" required />
						<!-- <a href="" class="tx-info tx-12 d-block mg-t-10">Forgot password?</a> -->
					</div><!-- form-group -->
					<button type="submit" class="btn btn-info btn-block">Submit</button>
				</form>
			</div><!-- login-wrapper -->
		</div><!-- d-flex -->
<script type="text/javascript">
$(document).ready(function() {		
	/**
	 * Custom validator for contains at least one lower-case letter
	 */
	jQuery.validator.addMethod("atLeastOneLowercaseLetter", function (value, element) {
		return this.optional(element) || /[a-z]+/.test(value);
	}, "Must have at least one lowercase letter");
	 
	/**
	 * Custom validator for contains at least one upper-case letter.
	 */
	jQuery.validator.addMethod("atLeastOneUppercaseLetter", function (value, element) {
		return this.optional(element) || /[A-Z]+/.test(value);
	}, "Must have at least one uppercase letter");
	 
	/**
	 * Custom validator for contains at least one number.
	 */
	jQuery.validator.addMethod("atLeastOneNumber", function (value, element) {
		return this.optional(element) || /[0-9]+/.test(value);
	}, "Must have at least one number");
	 
	/**
	 * Custom validator for contains at least one symbol.
	 */
	jQuery.validator.addMethod("atLeastOneSymbol", function (value, element) {
		return this.optional(element) || /[!@#$%^&*()]+/.test(value);
	}, "Must have at least one symbol");
		
	$("#resetpassForm").validate({
		debug:false,
		rules: {
			'password':{
				required: true,
				minlength: 6,
				maxlength: 20,
				atLeastOneLowercaseLetter: true,
				atLeastOneUppercaseLetter: true,
				atLeastOneNumber: true,
				atLeastOneSymbol: true
			},
			'conpassword':{
				required: true,
				minlength: 6,
				maxlength: 20,
				atLeastOneLowercaseLetter: true,
				atLeastOneUppercaseLetter: true,
				atLeastOneNumber: true,
				atLeastOneSymbol: true,
				equalTo: "#password",
			},	
		},
		messages: { 			
			conpassword: {				
				equalTo:"Please retype the correct new password"
			},
		},			
		highlight: function(element) {
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error');
		},
		success: function(element) {
			$(element).closest('.form-group').removeClass('has-error').addClass('has-success');
		},
		
	});
		
	$("#password").on("keydown", function(e) {
		var len = $("#password").val().length;
		if (len == 0) {
			return e.which !== 32;
		} else {
			var pos = $("#password").val();
			if (pos == 0) {
				return e.which !== 32;
			}
		}
	});	
	
	$("#conpassword").on("keydown", function(e) {
		var len = $("#conpassword").val().length;
		if (len == 0) {
			return e.which !== 32;
		} else {
			var pos = $("#conpassword").val();
			if (pos == 0) {
				return e.which !== 32;
			}
		}
	});	
});
</script>
	</body>
	
</html>
