<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  header('Content-type: text/html');
  session_start();
  if(isset($_GET["action"]) && $_GET["action"] == "logout"){
    $_SESSION = array();
    session_destroy();
    header("Location: login.php", true);
    exit;
  }
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
?>
</body>
</html>