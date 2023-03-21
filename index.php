<?php include("./config.php") ?>

<?php include(ROOT_PATH . "/src/db/connection.php")?>

<?php

session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
$parts = explode("/", $_SERVER['REQUEST_URI']);

if($_SERVER["REQUEST_METHOD"] == "GET"){
    if($parts[1] == 'blogs'){
    http_response_code(200);
    $result = (object) array("name" => "David", "age" => 30, "height" => 50);
    echo json_encode($result);
    exit;
};

if($parts[1] != 'blogs'){
    http_response_code(404);
    echo "Can't seem to find the document you are looking for";
    exit;
};
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    global $conn;
    $data = file_get_contents("php://input");
    echo $data;


    
}



// $id = $parts[2] ?? null;