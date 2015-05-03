<?php
  error_reporting(E_ALL);
  ini_set('display_errors',1);
  header('Content-type: application/json');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Loopback</title>
</head>
<body>
<?php
  $output = array();
  $method = $_SERVER["REQUEST_METHOD"];
  if($method == "GET") {
    $output["Type"] = "GET";
    $variables = $_GET;
  } else if ($method == "POST") {
    $output["Type"] = "POST";
    $variables = $_POST;
  } else {
    echo "Requires GET or POST request method.";
    exit;
  }
  if (count($variables) == 0) {
    $output["parameters"] = null;
  } else {
    $output["parameters"] = $variables;
  }
  echo json_encode($output);
?>
</body>
</html>