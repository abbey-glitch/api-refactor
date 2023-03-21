<?php include("./config.php"); ?>
<?php require_once(ROOT_PATH . "/functions/getBlogs.php") ?>
<?php $blog = getSingleBlog(); ?>
<?php $blogs = getAllBlogs(); ?>
<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Content-type: application/json");

$paths = explode("/", $_SERVER['REQUEST_URI']);

if($_SERVER["REQUEST_METHOD"] == "GET"){
     if(isset($paths[3])){        
        echo json_encode($blog);
        exit;
    }
   if(isset($paths[2])){
    if($paths[2] == "blogs"){

        http_response_code(200);
        echo json_encode($blogs);
        
        exit;
    }else{
        echo "Route does not exist";
    }
   }
    
   
    return "Bad Request";
}