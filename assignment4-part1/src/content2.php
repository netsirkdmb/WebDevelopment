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
<title>Content2</title>
</head>
<body>
<?php
  echo 'Click <a href="content1.php">here</a> for Content1.';
?>
</body>
</html>