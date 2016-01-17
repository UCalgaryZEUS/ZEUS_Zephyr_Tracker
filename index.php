<!DOCTYPE HTML>
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
<html>
	<head>
		<meta name="description" content="Trackking application used to check status of the Zephyr bike in real time." />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="keywords" content="ZEUS, Zephyr, NovAtel, tracker" />
		<meta name="viewport" content="initial-scale=1.0">
		<meta charset="utf-8">
		<title>ZEUS Zephyr Tracker</title>
		<link rel="stylesheet" type="text/css" href="styles.css" title="style" />
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
	</head>
	<body>
		<div id="main">
			<div id="header">
				<div id="logo">
					<div id="logo_text">
						<!-- class="logo_colour", allows you to change the colour of the text -->
						<h1><a href="index.php">ZEUS</a></h1>
						<under>Zephyr Tracker</under>
					</div>
				</div>
				<div id="menubar">
					<ul id="menu">
						<!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
						<li class="selected"><a href="index.php">Home</a></li>
						<li><a href="about.php">About</a></li>
						<li><a href="allraces.php">All Races</a></li>
<?php
if (empty($_SESSION["username"])) {
?>
			 <li><a href="loginpage.php">Login</a></li>
<?php
} else if (!empty($_SESSION["username"])) {
?>
	<li><a href = "setuprace.php">Setup/Modify Race</a></li>
	<li><a href="usercp.php">User CP: <?php
	if (!empty($_SESSION["username"])) {
		echo $_SESSION["username"];
	}
?></a></li> 
		   <li><a href="logout.php">Logout</a></li>              
<?php
}
?>
					</ul>
				</div>
			</div>
			<div id="site_content">
				<div id="content">
				<Bodybold>Most Recent Data Set</bodybold>
					<br></br>
						<div id="map_canvas" style="width: 800px; height:500px;"></div>
						<input type="button" id="reset" value="Reset to origin"></input>

				</div>
			</div>
		<div id="content_footer"></div>
	<div id="footer">
	<a>Created by the University of Calgary ZEUS Racing Team</a>
	</div>
	</div>
	</body>
</html>
