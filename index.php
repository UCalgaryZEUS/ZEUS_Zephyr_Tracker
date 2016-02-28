<!doctype html>
<?php
include 'config.php';
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');
session_start();

$data_array = array();
$connected = new mysqli($Database_Address, $Database_User, $Database_Password, $Database_Name);
if ($connected->connect_errno > 0) {
		die('Unable to connect to database [' . mysqli_connect_errno() . ']' . mysqli_connect_error());
	}
	// Get Last Row
	$result = $connected->prepare("SELECT DataSet.dataID FROM DataSet ORDER BY DataSet.dataID DESC LIMIT 1");
	$result->execute();
	$result->bind_result($dataID);
	$result->fetch();
	$searchID = $dataID;
	$connected->close();

	$connected = new mysqli($Database_Address, $Database_User, $Database_Password, $Database_Name);
	$query = "SELECT DataPoint.pointID, DataPoint.time, DataPoint.acceleration, DataPoint.velocity, DataPoint.latitude, DataPoint.longitude, DataPoint.altitude From DataPoint JOIN DataSet ON DataSet.DataID = DataPoint.DataID WHERE DataPoint.DataID = $searchID";
	$result2=$connected->query($query);

	while ($data = $result2->fetch_array(MYSQLI_ASSOC)) {
		$data_array[] = $data;
	}
	$result2->free();

	/* close connection */
	$connected->close();
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
	<script type="text/javascript">
	// check DOM Ready
	$(document).ready(function() {
		// execute
		(function() {
			// data points
			// [name, latitude, longitude, altitude, time, velocity, acceleration]
			var markerData = <?php echo json_encode($data_array) ?>;
			console.log(markerData);

			console.log(markerData[0][1]);
			// map options
			var options = {
				tilt: 0,
				zoom: 18,
				center: new google.maps.LatLng(markerData[0]["latitude"], markerData[0]["longitude"]),
				mapTypeId: google.maps.MapTypeId.SATELLITE,
			};

			// init map
			var map = new google.maps.Map(document.getElementById('map_canvas'), options);

			// Modify marker point appearance
			var pinIcon = new google.maps.MarkerImage(
					"http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FFFF00",
					null,
					null,
					null,
					new google.maps.Size(18, 30)
					);

			var pinIconStart = new google.maps.MarkerImage(
					"http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|00FF00",
					null,
					null,
					null,
					new google.maps.Size(18, 30)
					);

			var pinIconLast = new google.maps.MarkerImage(
					"http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|FF0000",
					null,
					null,
					null,
					new google.maps.Size(18, 30)
					);
			// Initialize array to contain travel path
			var travelPath = new Array();
			var origin = new google.maps.LatLng(markerData[0]["latitude"], markerData[0]["longitude"]);

			for (var i = 0; i < markerData.length; i++) {
				// Initialize markers
				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(markerData[i]["latitude"], markerData[i]["longitude"]),
					map: map,
					title: 'Data Point: ' + i
				});
				var markerPath = new google.maps.LatLng(markerData[i]["latitude"], markerData[i]["longitude"]);
				if(i == 0) {
					marker.setIcon(pinIconStart);
				} else {
					marker.setIcon(pinIcon);
				}
				travelPath.push(markerPath);

				// Process multiple info windows
				(function(marker, i) {
					// Add click event
					google.maps.event.addListener(marker, 'click', function() {
						infowindow = new google.maps.InfoWindow({
							content: '<b>Data Point:</b> ' + i +
								'<br>Latitude: ' + markerData[i]["latitude"] +
								'<br>Longitude: ' + markerData[i]["longitude"] +
								'<br>Altitude: ' + markerData[i]["altitude"] +
								'<br>Accelertaion: ' + markerData[i]["acceleration"] +
								'<br>Time: ' + markerData[i]["time"] +
								'<br>Velocity: ' + markerData[i]["velocity"]
						});
						infowindow.open(map, marker);
					});
				})(marker, i);
			}

			// Initialize travel path properties
			var pathProperties=new google.maps.Polyline({
				path:travelPath,
				strokeColor:"#0000FF",
				strokeOpacity:0.8,
				strokeWeight:2
			});
			console.log(pathProperties);
			pathProperties.setMap(map);

			// Event for resetting to origin on button press
			google.maps.event.addDomListener(document.getElementById("reset"), 'click', function() {
				map.setCenter(origin);
				map.setZoom(30);
			});

		})();
	});
	</script>

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
          <span class="mdl-layout-title">Home</span>
          <div class="mdl-layout-spacer"></div>
          <button class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="hdrbtn">
            <i class="material-icons">more_vert</i>
          </button>
          <ul class="mdl-menu mdl-js-menu mdl-js-ripple-effect mdl-menu--bottom-right" for="hdrbtn">
            <li class="mdl-menu__item" id="reset">Reset To Origin</li>
            <li class="mdl-menu__item">Reset To Last Point</li>
            <li class="mdl-menu__item">Reset To X Point</li>
            <li class="mdl-menu__item">Display Only Mechanical Info</li>
            <li class="mdl-menu__item">Display Only Electrical Info</li>
          </ul>
        </div>
      </header>
      <div class="demo-drawer mdl-layout__drawer mdl-color--blue-grey-900 mdl-color-text--blue-grey-50">
        <header class="demo-drawer-header">
          <h6>ZEPHYR TRACKER</h6>
          <div class="demo-avatar-dropdown">
			<span>
			<?php
			if (!empty($_SESSION["username"])) {
				echo $_SESSION["username"];
			?>
			</span>
            <div class="mdl-layout-spacer"></div>
            <button id="accbtn" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
              <i class="material-icons" role="presentation">arrow_drop_down</i>
              <span class="visuallyhidden">Accounts</span>
            </button>
            <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="accbtn">
			<li class="mdl-menu__item">
				<a class="mdl-navigation__link" href="usercp.php">User Control Panel</a>
			</li>
            </ul>
			<?php
			}
			?>
          </div>
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
          <a class="mdl-navigation__link" href="logout.php"><i class="mdl-color-text--blue-grey-400 material-icons" role="presentation">person_outline</i>Logout</a>
	<?php
	}
	?>
          <div class="mdl-layout-spacer"></div>
        </nav>
	  </div>
	  <main class="mdl-layout__content mdl-color--white-100">
		<div id="map_canvas" style="width: 100%; height:876px;"></div>
	  </main>
    </div>
    <script src="material.js"></script>
  </body>
</html>
