<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  header('Content-type: text/html');
  session_start();
  $_SESSION = array();
  session_destroy();
?>