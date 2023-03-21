<?php
include ('../config.php');
?>
<?php

function imgUpload($img, $fileName, $uploads, $uploadTo)
{
    global $conn;
    $uploader = $_FILES;
    $uploads = $_FILES['uploader']['tmp_name'];
    $uploadTo = basename($_FILES['uploader']['name']);
    $uploader = json_encode($uploader['uploader']);
    $uploader = json_decode($uploader);
    $info = pathinfo($uploader->full_path);
    $img = $info['basename'];
    $fileName = $info['filename'];
    $extension = $info['extension'];
    $extensions = ['png', 'jpg', 'webp'];
    if (in_array($extension, $extensions)) {
        return array(
            $uploads,
            $uploadTo,
            $info
        );
    } else {
        return 'not a match';
    }
}

function createBlog()
{
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] = 'POST') {
        if (!isset($_SERVER['HTTP_AUTHORIZATION']) && !isset($_COOKIE['imgInfo'])) {
            return 'Unauthorized';
        };
        $get_id = $_SERVER['HTTP_AUTHORIZATION'];
        $id = explode(' ', $get_id);
        $user_id = $id[1];
        $imgPath = $_COOKIE['imgInfo'];
        $imgPath = json_decode($imgPath);
        $imgPath = json_encode($imgPath);
        $imgPath = json_decode($imgPath);
        $imgPath_url = $imgPath->url;
        $img_name = $imgPath->file_name;
        $size = $imgPath->size;
        $user_sql = 'SELECT * FROM users WHERE id=? LIMIT 1';
        $user_query = mysqli_prepare($conn, $user_sql);
        mysqli_stmt_bind_param($user_query, 'i', $user_id);
        mysqli_stmt_execute($user_query);
        $stmt_user_result = mysqli_stmt_get_result($user_query);
        if (mysqli_num_rows($stmt_user_result) != 1) {
            http_response_code(401);
            $message = 'Unauthorized User';
            $response = json_encode(['status' => 'Fail', 'message' => $message]);

            return $response;
        }
        $data = file_get_contents('php://input');
        $data = json_decode($data);
        $title = $data->title;
        $content = htmlentities($data->content);
        $category = $data->category_id;
        // $imgPath_url = $data;
        // $img_name = $data;
        // $size = $data;
        if (!$title || !$content || !$category) {
            http_response_code(400);
            $message = 'All fields are required';
            $response = json_encode(['status' => 'Fail', 'message' => $message]);

            return $response;
        }
        $title = esc($data->title);
        $content = esc($data->content);
        $category = esc($data->category_id);
        $sql = "SELECT * FROM `blogs` WHERE `title` = '$title'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1) {
            //remove cookies
            return 'blogs exist';
        }
        $sql = 'INSERT INTO blogs (`title`, `content`, `author`, `imgPath_url`, `img_name`, `bytes`, `category_id`) VALUES (?,?,?,?,?,?,?)';
        $query = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($query, 'ssissii', $title, $content, $user_id, $imgPath_url, $img_name, $size, $category);
        mysqli_stmt_execute($query);
        if (!$query) {
            http_response_code(500);
            $message = 'Something went wrong, try again';
            $response = json_encode(['status' => 'Fail', 'message' => $message]);

            return $response;
        }
        http_response_code(201);
        $message = 'Blog created successfully';
        $response = json_encode(['status' => 'Success', 'message' => $message]);

        return $response;
    } else {
        http_response_code(400);
        $message = 'Bad request';
        $response = json_encode(['status' => 'Fail', 'message' => $message]);

        return $response;
    }
}

function esc(String $value)
{
    // bring the global db connect object into function
    global $conn;

    $val = trim($value);  // remove empty space sorrounding string
    $val = mysqli_real_escape_string($conn, $value);

    return $val;
}
