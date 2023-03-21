<?php include("../config.php") ?>
<?php 
function updateBlog(){
    global $conn;
     if($_SERVER["REQUEST_METHOD"] == "PUT" || $_SERVER["REQUEST_METHOD"] == "PATCH"){
        if(!isset($_SERVER["HTTP_AUTHORIZATION"])){
        http_response_code(401);
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    };
    $get_id = $_SERVER["HTTP_AUTHORIZATION"];
    $id = explode(" ", $get_id);
    $user_id = $id[1];
    $user_sql = "SELECT * FROM `roles` WHERE `user_id`=? LIMIT 1";
    $query = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($query, 'i', $user_id);
    mysqli_stmt_execute($query);
    $user_query = mysqli_stmt_get_result($query);
    $user_result = mysqli_fetch_assoc($user_query);
    if(mysqli_num_rows($user_query) != 1){
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
    $stmt_policy_result = mysqli_stmt_get_result($policy_query);
    $policy_result = mysqli_fetch_assoc($stmt_policy_result);
    array_push($policy_array, $policy_result["privileges"]);
    }
    
    if(!in_array("can-update-blog", $policy_array)){
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }

    
    $path = explode("/", $_SERVER["REQUEST_URI"]);
    if(!isset($path[3])) {
         http_response_code(400);
        $message = "Bad request";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
    $data = file_get_contents("php://input");
    $data = json_decode($data);
    
    $blog_id = $path[3]; 
    $title = $data->title;
    $content = $data->content;
    $category = $data->category_id;
    $published = $data->published;
    $author_id = $data->author;

    if(!$title || !$content || !$category || !$author_id || !$published){
        http_response_code(400);
        $message = "All fields are required";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }

    $title = esc($data->title);
    $author_id = esc($data->author);
    $content = esc($data->content);
    $category = esc($data->category_id);
    $published = esc($data->published);
    $sql = "SELECT * FROM blogs WHERE id = ? LIMIT 1";
    $query = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($query, "i", $blog_id);
    mysqli_stmt_execute($query);
    $stmt_result = mysqli_stmt_get_result($query);
    if(mysqli_num_rows($stmt_result) != 1){
        http_response_code(404);
        $message = "No blog with matching id";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
    $update_sql = "UPDATE blogs SET `title`=?, `content`=?, `category_id`=?, `published`=?, `author`=?, updated_at=now() WHERE id=?";
    $update_query = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_query, "ssiiii", $title, $content, $category,  $published, $author_id, $blog_id);
    mysqli_stmt_execute($update_query);
    if(!$update_query){
        http_response_code(500);
         $message = "Something went wrong, try again";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
        http_response_code(201);
        $message =  "Blog with id " . $id . " successfully updated";
        $response = array("status" => "Success", "message" => $message);
        return $response;
}

}
function deleteBlog(){
    global $conn;
    if($_SERVER["REQUEST_METHOD"] == "DELETE"){
        if(!isset($_SERVER["HTTP_AUTHORIZATION"])){
        $message = "Unauthorized User";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    };
    $get_id = $_SERVER["HTTP_AUTHORIZATION"];
    $id = explode(" ", $get_id);
    $user_id = $id[1];
    $user_sql = "SELECT * FROM `roles` WHERE `user_id`=? LIMIT 1";
    $query = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($query, 'i', $user_id);
    mysqli_stmt_execute($query);
    $user_query = mysqli_stmt_get_result($query);
    $user_result = mysqli_fetch_assoc($user_query);
    if(mysqli_num_rows($user_query) != 1){
        http_response_code(401);
        $message = "Unauthorized User";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }

    $user_policies = $user_result["policies"];
    $user_policies = json_decode($user_policies);
    $policy_array = [];
    for($i = 0; $i <= count($user_policies); $i++){
    $priv_sql = "SELECT * FROM `policies` WHERE id=?";
    $policy_query = mysqli_prepare($conn, $priv_sql);
    mysqli_stmt_bind_param($policy_query, "i", $user_policies[$i]);
    mysqli_stmt_execute($policy_query);
    $stmt_policy_result = mysqli_stmt_get_result($policy_query);
    $policy_result = mysqli_fetch_assoc($stmt_policy_result);
    array_push($policy_array, $policy_result["privileges"]);
    }
    if(!in_array("can-delete-blog", $policy_array)){
        http_response_code(401);
        $message = "Unauthorized";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }

    $path = explode("/", $_SERVER["REQUEST_URI"]);
    if(!isset($path[3])) {
        http_response_code(400);
        $message = "Bad request";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
    $blog_id = $path[3];
    $sql = "SELECT * FROM blogs WHERE id = ? LIMIT 1";
    $query = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($query, "i", $blog_id);
    mysqli_stmt_execute($query);
    $stmt_result = mysqli_stmt_get_result($query);
    $result = mysqli_fetch_assoc($stmt_result);
    if(mysqli_num_rows($stmt_result) < 1){
        http_response_code(404);
        $message = "No blog with matching id";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
    $delete_sql = "DELETE FROM blogs WHERE id = ?";
    $query = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($query, "i", $blog_id);
    mysqli_stmt_execute($query);
    if(!$query){
        http_response_code(500);
        $message = "Something went wrong, try again";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
    }
    http_response_code(200);
        $message =  "Blog with id " . $id . " successfully deleted";
        $response = array("status" => "Fail", "message" => $message);
        return $response;
     }
     http_response_code(400);
     $message =  "Bad request";
     $response = array("status" => "Fail", "message" => $message);
     return $response;
    }



function esc(String $value)
	{	
		// bring the global db connect object into function
		global $conn;

		$val = trim($value); // remove empty space sorrounding string
		$val = mysqli_real_escape_string($conn, $value);
        return $val;

    }