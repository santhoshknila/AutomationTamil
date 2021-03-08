<!DOCTYPE html>
<html lang="en">
<head>
<title><?PHP echo $title; ?> | Tamilethos Smart app</title>

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
	
	<?=$_styles?>
	
	<link rel="icon" href="<?php echo base_url(); ?>skin/default/images/favicon.png" type="images/png" sizes="24x24"> 
	<?=$_scripts?>
</head>
<body>

<?php echo $header; ?>
<?PHP echo $menu; ?>
<?PHP echo $content; ?>
<?php echo $footer; ?>

</body>
</html>

