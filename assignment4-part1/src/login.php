<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  header('Content-type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Login</title>
</head>
<body>
<?php
  echo '<form action="content1.php" method="post">
         Username: 
         <input type="text" name="username">
         <input type="submit" value="Login">
       </form>';
  session_start();
  $_SESSION = array();
  session_destroy();
?>
</body>
</html>