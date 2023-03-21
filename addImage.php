<?php
header('Access-Allow-Control-Origin: *');
header('Access-Allow-Control-Methods: *');
header('Access-Allow-Control-Headers: *');
require_once(__DIR__ . '/vendor/autoload.php');
use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
if(isset($_FILES['uploader'])){
    $data = file_get_contents('php://input');
    $data = json_decode($data);
    $img = $data;
    $fileName = $data;
    $extension = $data;
    require "createBlog.php";
    $feed = imgUpload($img, $fileName, $uploads, $uploadTo);
    $img_name = json_encode($feed[2]['filename']);
    $img_name = json_decode($img_name);
    echo json_encode($img_id);
    if($feed){
      if(!is_dir("users")){
        mkdir("users");
        return;
      }
      move_uploaded_file($feed[0], "users/$feed[1]");
      // this is to create a pattern and delete all files that matches it
      // array_map('unlink', glob("users/*.webp"));
      $cloudinary = new Cloudinary(
        [
            'cloud' => [
                'cloud_name' => 'doaqrbuxc',
                'api_key'    => '152229312993198',
                'api_secret' => 'lzwf-9ewt3PSFOVBqewUc_Px8s4',
            ],
        ]
       );
      
       $response = $cloudinary->uploadApi()->upload(
        "users/$feed[1]",
        ['public_id' => $img_name,
          'folder' => 'lodge'
        ]
      );
       $response = json_encode($response);
       $response = json_decode($response);
       $key = json_encode($response);
       $asset_id = json_encode($response->asset_id);
       $bytes = json_encode($response->bytes);
       $fileName = json_encode($response->original_filename);
       echo $fileName;
       $type = json_encode($response->type);
       $url = json_encode($response->url);
       $url = json_decode($url);
       $image = strval($url);
       $cloudinary->image($key)->resize(Resize::fill(100, 150))->toUrl();
         
    }else{
      echo "failed to load";
    }
  
}else{
    header("http: 403, unauthorized");
}