<?php
$response = array();
if (isset($_POST['sno']) && isset($_POST['name'])) {
    
    $sno = $_POST['sno'];
    $name = $_POST['name'];
   
    require_once __DIR__ . '/db_connect.php';
    $db = new DB_CONNECT();
    $result = mysql_query("UPDATE lfms SET mobile = '$name', status='0' WHERE sno = $sno");
    if ($result) {
        // successfully updated
        $response["success"] = 1;
        $response["message"] = "Item successfully updated.";
        
        // echoing JSON response
        echo json_encode($response);
    } else {
        
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";

    // echoing JSON response
    echo json_encode($response);
}
?>
