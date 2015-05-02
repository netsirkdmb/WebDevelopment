<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Multtable</title>
</head>
<body>
<?php
  //checking if variables are numeric: http://php.net/manual/en/function.is-numeric.php
  //checking if variables are integers: http://php.net/manual/en/function.intval.php
  //                                    http://php.net/manual/en/function.floatval.php
  $names = array("min-multiplicand", 
                 "max-multiplicand", 
                 "min-multiplier", 
                 "max-multiplier");
  $isError = false;
  foreach ($names as $name) {
    if (!isset($_GET[$name])) {
      echo "Missing parameter " . $name . ".<br>";
      $isError = true;
      continue;
    }
    $value = $_GET[$name];
    if (!is_numeric($value) || intval($value) != floatval($value) || intval($value) < 0) {
      echo $name . " must be an integer.<br>";
      $isError = true;
    }
  }
  if ($isError) {
    exit;
  }
  $min_multiplicand = $_GET["min-multiplicand"];
  $max_multiplicand = $_GET["max-multiplicand"];
  $min_multiplier = $_GET["min-multiplier"];
  $max_multiplier = $_GET["max-multiplier"];
  if ($min_multiplicand > $max_multiplicand) {
    echo "Minimum multiplicand larger than maximum.<br>";
    $isError = true;
  }
  if ($min_multiplier > $max_multiplier) {
    echo "Minimum multiplier larger than maximum.<br>";
    $isError = true;
  }
  if ($isError) {
    exit;
  }
  
?>
</body>
</html>