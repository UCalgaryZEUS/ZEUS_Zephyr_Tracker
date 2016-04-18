<!doctype html>
<?php
include 'config.php';
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On'); 
session_start();
?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Website to track ZEUS bike">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ZEUS Zephyr Tracker</title>

    <!-- Add to homescreen for Chrome on Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="icon" sizes="192x192" href="images/android-desktop.png">

    <!-- Add to homescreen for Safari on iOS -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Material Design Lite">
    <link rel="apple-touch-icon-precomposed" href="images/ios-desktop.png">

    <!-- Tile icon for Win8 (144x144 + tile color) -->
    <meta name="msapplication-TileImage" content="images/touch/ms-touch-icon-144x144-precomposed.png">
    <meta name="msapplication-TileColor" content="#3372DF">

    <link rel="shortcut icon" href="images/zeuslogo.ico">

    <!-- SEO: If your mobile URL is different from the desktop URL, add a canonical link to the desktop page https://developers.google.com/webmasters/smartphone-sites/feature-phones -->
    <!--
    <link rel="canonical" href="http://www.example.com/">
    -->

    <link href="https://fonts.googleapis.com/css?family=Roboto:regular,bold,italic,thin,light,bolditalic,black,medium&amp;lang=en" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="material.css">
    <link rel="stylesheet" href="styles.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.11&sensor=false" type="text/javascript"></script>

	<style>
	body, html {
	  height: 100%;
	  width: 100%;
	}

	div#content {
	  width: 100%; height: 100%;
	}
	#view-source {
	  position: fixed;
	  display: block;
	  right: 0;
	  bottom: 0;
	  margin-right: 40px;
	  margin-bottom: 40px;
	  z-index: 900;
	}
	</style>
  </head>
  <body>
    <div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-drawer mdl-layout--fixed-header">
      <header class="demo-header mdl-layout__header mdl-color--grey-100 mdl-color-text--grey-600">
        <div class="mdl-layout__header-row">
          <span class="mdl-layout-title">Modify Race</span>
          <div class="mdl-layout-spacer"></div>
        </div>
      </header>
      <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
            <span class="indra-title">ZEPHYR TRACKER</span>
        </header>
        <nav class="demo-navigation mdl-navigation mdl-color--blue-grey-800">
          <a class="mdl-navigation__link" href="index.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">home</i>Home</a>
          <a class="mdl-navigation__link" href="about.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">motorcycle</i>About</a>
		  <a class="mdl-navigation__link" href="allraces.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">list</i>All Races</a>
	<?php
	if (empty($_SESSION["username"])) {
	?>
          <a class="mdl-navigation__link" href="loginpage.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">person</i>Login</a>
	<?php
	} else if (!empty($_SESSION["username"])) {
	?>
          <a class="mdl-navigation__link" href="setuprace.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">build</i>Setup/Modify Race</a>
                <a class="mdl-navigation__link" href="usercp.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">person</i><?php echo $_SESSION ["username"];?>: Control Panel</a>
          <a class="mdl-navigation__link" href="logout.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">person_outline</i>Logout</a>
	<?php
	}
	?>
          <div class="mdl-layout-spacer"></div>
        </nav>
	  </div>
	  <main class="mdl-layout__content mdl-color--white-100">
			<?php
			$connected = new mysqli($Database_Address, $Database_User, $Database_Password, $Database_Name);
			if ($connected->connect_errno > 0) {
				die('Unable to connect to database [' . mysqli_connect_errno() . ']' . mysqli_connect_error());
			}

			$raceID = $_GET['ID'];

			$query = "SELECT Race.racename, Race.location, Race.description From Race WHERE Race.raceID = ?";
			$statement = $connected->prepare($query); 
			$statement->bind_param("i",$raceID);
			$statement->execute();
			$statement->bind_result($racename, $location, $description);
			$statement->fetch();
			?>
			<bodybold>Current Race Details</bodybold>
			<br>
			<b>Race Name:</b>
			<?php echo $racename; ?>
			<br><br>
			<b>Race Location:</b>
			<?php echo $location; ?>
			<br><br>
			<b>Race Description:</b>
			<?php echo $description; ?>
			<br><br>
				
			<bodybold>Modify Race Details</bodybold>
			<br>
			<form action= "modifyraceconfirm.php" method= "POST">
				<b>Race Name</b>
				<br>
				<input type="text" name="racename" value="" />
				<br>
				<br>
				<b>Race Location</b>
				<br>
				<input type="text" name="location" value= "" />
				<br>
				<br>
				<b>Description</b>
				<br>
				<textarea cols="60" rows="8" name="desc" /></textarea><br>
				<br>
				<input type="submit" value="Modify race" />
			</form>
			<br>
			<br>
	  </main>
    </div>
    <script src="material.js"></script>
  </body>
</html>
