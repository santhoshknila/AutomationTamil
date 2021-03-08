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
					<img src="<?PHP echo default_theme_skin_path("images/logo.png")?>" alt="Tamil Ethos" style="width:250px;"/>
				</div>
				<form id="adminloginForm" method="post" name="adminloginForm" action="<?php echo site_url('admin/login');?>" data-parsley-validate >	
					<?php if($this->session->flashdata('message')){ echo $this->session->flashdata('message'); } ?>
					<div class="form-group">
						<label>Email Address <span class="tx-danger">*</span></label>
						<input type="text" class="form-control" name="username" required />
					</div><!-- form-group -->
					<div class="form-group">
						<label>Password <span class="tx-danger">*</span></label>
						<input type="password" class="form-control" name="password" required />
						<!-- <a href="" class="tx-info tx-12 d-block mg-t-10">Forgot password?</a> -->
					</div><!-- form-group -->
					<button type="submit" class="btn btn-info btn-block">Sign In</button>
				</form>
			</div><!-- login-wrapper -->
		</div><!-- d-flex -->
	</body>
</html>