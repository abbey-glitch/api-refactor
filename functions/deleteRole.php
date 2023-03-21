<?php include("../config.php") ?>
<?php  

function removeRole(){
global $conn;
if($_SERVER["REQUEST_METHOD"] == "DELETE"){
if(!isset($_SERVER["HTTP_AUTHORIZATION"])){
        http_response_code(401);
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    };
    $get_id = $_SERVER["HTTP_AUTHORIZATION"];
    $id = explode(" ", $get_id);
    $user_id = $id[1];
    $sql = "SELECT * FROM  `roles` WHERE `user_id` = ? LIMIT 1";
    $query = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($query, "i", $user_id);
    mysqli_stmt_execute($query);
    $stmt_result = mysqli_stmt_get_result($query);
    $role = mysqli_fetch_assoc($stmt_result);
    if(mysqli_num_rows($stmt_result)){
        http_response_code(401);
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
        
    }
    if($role["name"] == "Super-admin"){
        
        $path = explode("/", $_SERVER["REQUEST_URI"]);
        if(!isset($path[2])) {
            http_response_code(400);
            $message = "Bad request";
            $response = array("status" => "Fail", "message" => $message );
            return $response; 
        }
        $user_id = $path[2];
        $sql = "DELETE FROM `roles` WHERE `user_id` = ?";
        $query = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($query, "i", $user_id);
        mysqli_stmt_execute($query);
        if($query){
            http_response_code(200);
            $message = "Role with id " . $id . " successfully deleted";
            $response = array("status" => "Fail", "message" => $message );
            return $response;  
        }else{
            http_response_code(500);
            $message = "Something went wrong";
            $response = array("status" => "Fail", "message" => $message );
            return $response; 
        }
    }
}

            $message = "Bad request, Not the accepted request method";
            $response = array("status" => "Fail", "message" => $message );
            return $response; 

}