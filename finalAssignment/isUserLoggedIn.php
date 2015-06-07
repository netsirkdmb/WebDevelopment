<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  header('Content-type: application/json');
  session_start();
  if(isset($_SESSION["username"])) {
    $output = array("logged_in"=>true, "username"=>$_SESSION["username"]);
  } else {
    $output = array("logged_in"=>false);
  }
  echo json_encode($output);
?>