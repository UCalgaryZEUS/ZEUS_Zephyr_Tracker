<!DOCTYPE HTML>
<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');  //On or Off to display errors
include 'config.php';
session_start();

if (count($_POST) > 0) {
    // Establish connection and test that it works properly
    $connected = mysql_connect($Database_Address, $Database_User, $Database_Password, $Database_Name);
    if (!$connected) {
        die("MySQL connection failed" . mysql_error());
    }

    $db_connected = mysql_select_db("$Database_Name", $connected);
    if (!$db_connected) {
        die("Database connection failed" . mysql_error());
    }
    $result = mysql_query("SELECT * FROM Member WHERE username='" . $_POST["username"] . "' and password = '" . $_POST["pword"] . "'");
    $row = mysql_fetch_array($result);
    if (is_array($row)) {
        $_SESSION["ID"] = $row[userID];
        $_SESSION["username"] = $row[username];
    } else {
        header("Location:badlogin.php");
    }
}
if (isset($_SESSION["ID"])) {
    header("Location:index.php");
}
?>
