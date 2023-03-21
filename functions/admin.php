<?php include("../config.php") ?>
<?php
function getAdminBlogs(){
    global $conn;
  if(!isset($_SERVER["HTTP_AUTHORIZATION"])){
        return "Unauthorized";
    };
    $get_id = $_SERVER["HTTP_AUTHORIZATION"];
    $id = explode(" ", $get_id);
    $user_id = $id[1];
    $user_sql = "SELECT * FROM `roles` WHERE `user_id`=? LIMIT 1";
    $user_query = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($user_query, "i", $user_id);
    mysqli_stmt_execute($user_query);
    $stmt_result = mysqli_stmt_get_result($user_query);
    $user_result = mysqli_fetch_assoc($stmt_result);
    if(mysqli_num_rows($stmt_result) != 1){
        http_response_code(401);
        $message = "Unauthorized User";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
    $user_policies = $user_result["policies"];
    $user_policies = json_decode($user_policies);
    $policy_array = [];
    for($i = 0; $i < count($user_policies); $i++){
    $priv_sql = "SELECT * FROM `policies` WHERE id=?";
    $policy_query = mysqli_prepare($conn, $priv_sql);
    mysqli_stmt_bind_param($policy_query, "i", $user_policies[$i]);
    mysqli_stmt_execute($policy_query);
    $stmt_result = mysqli_stmt_get_result($policy_query);
    $policy_result = mysqli_fetch_assoc($stmt_result);
    array_push($policy_array, $policy_result["privileges"]);
    }
    
    if(!in_array("can-update-blog", $policy_array)){
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
        $sql = "SELECT * FROM blogs";
        $result = mysqli_query($conn, $sql);
        $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if(count($blogs) < 1){
        http_response_code(404);
        $message =  "No blogs yet";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
        }
        http_response_code(200);
        $response = array("status" => "Success", "data" => $blogs);
        return $response;

}