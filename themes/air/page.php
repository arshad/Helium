<?php session_start(); ?>
<?php
	Global $header,$message,$content,$footer,$theme_path,$site_name;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="<?php echo $theme_path; ?>/images/favicon.ico">
<?php echo $header; ?>
</head>

<body>
<div id="wrapper">
	
	<div id="header">
		<a href="index.php"><h1 id="logo"><?php echo $site_name; ?></h1></a>
	</div>
	
	<div id="navigation" >
		<div id="menu">
			<?php echo $Module->render("industry","menu"); ?>
			<?php echo $Module->render("user","menu"); ?>
		</div>			
	</div>
	
	<div id="sub-navigation">
		<div id="menu">
			<?php echo $Module->render("job","menu"); ?>
		</div>		
	</div>	
	
	<div id="content">
		<?php echo $message; ?>
		<?php echo $content; ?>
	</div>
	
	<div id="footer">
		<?php echo $footer; ?>
	</div>	
</div><!-- end wrapper -->
</body>
</html>