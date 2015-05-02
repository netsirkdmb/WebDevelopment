<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  header('Content-type: text/html');
  session_start();
  if(!isset($_SESSION["username"]) && !isset($_SESSION["count"]) && $_SERVER["REQUEST_METHOD"] != "POST") {  //no active session, no POST
    header("Location: login.php", true);
    exit;
  }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Content1</title>
</head>
<body>
<?php
  if(!isset($_SESSION["username"]) && !isset($_SESSION["count"])){ // no active session, POST
    if(!isset($_POST["username"]) || $_POST["username"] == null || $_POST["username"] == "") { // no active session, POST, without username
      echo 'A username must be entered.  Click <a href="login.php">here</a> to return to the login screen.';
      exit;
    } else { // no active session, POST, with username
      $_SESSION["username"] = $_POST["username"];
      $_SESSION["count"] = 0;
    }
  }
  echo 'Hello ' . $_SESSION["username"] . ', you have visited this page ' . $_SESSION["count"] . ' times before.  Click <a href="login.php">here</a> to logout.<br>';
  $_SESSION["count"]++;
  echo 'Click <a href="content2.php">here</a> for Content2.';
?>
</body>
</html>