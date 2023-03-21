<?php include("../config.php") ?>
<?php
function getUsers(){
    global $conn;
    if($_SERVER["REQUEST_METHOD"] == "GET"){
        if(!isset($_SERVER["HTTP_AUTHORIZATION"])){
        http_response_code(401);
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
        }
        $get_id = $_SERVER["HTTP_AUTHORIZATION"];
        $id = explode(" ", $get_id);
        $user_id = $id[1];
        $sql = "SELECT * FROM  `roles` WHERE `user_id` = ? LIMIT 1";
        $query = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($query, "i", $user_id);
        mysqli_stmt_execute($query);
        $stmt_result = mysqli_stmt_get_result($query);
        $role = mysqli_fetch_assoc($stmt_result);
        if(mysqli_num_rows($stmt_result) != 1){
        http_response_code(401);
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
        }
        $user_policies = $role["policies"];
        $user_policies = json_decode($user_policies);
        $policy_array = [];
        for($i = 0; $i < count($user_policies); $i++){
        $priv_sql = "SELECT * FROM `policies` WHERE id=?";
        $policy_query = mysqli_prepare($conn, $priv_sql);
        mysqli_stmt_bind_param($policy_query, "i", $user_policies[$i]);
        mysqli_stmt_execute($policy_query);
        $stmt_policy_result = mysqli_stmt_get_result($policy_query);
        $policy_result = mysqli_fetch_assoc($stmt_policy_result);
        array_push($policy_array, $policy_result["privileges"]);
        }
        if(!in_array("can-create-admin", $policy_array)){
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
        }
        $sql = "SELECT `email`, `id`, `lname`, `fname` FROM `users`";
        $query = mysqli_query($conn, $sql);
        $result = mysqli_fetch_all($query, MYSQLI_ASSOC);
        if(!$query){
        $message = "Something went wrong";
        $response = array("status" => "Fail", "message" => $message);
        return $response;   
        }
        if(count($result) < 1){
        $message = "No registered user yet";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
        }
        $response = array("status" => "Success", "data" => $result);
        return $response;
    }
}