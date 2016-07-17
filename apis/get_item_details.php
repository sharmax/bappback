<?php
$response = array();
require_once __DIR__ . '/db_connect.php';
$db = new DB_CONNECT();
if (isset($_GET["sno"])){
$id = $_GET["sno"];
    $result = mysql_query("SELECT * FROM lfms WHERE sno = $id");
    if (!empty($result)) {
        if (mysql_num_rows($result) > 0) {
            $row = mysql_fetch_array($result);
            $item = array();
            $item["sno"] = $row["sno"];
			$item["particulars"] = $row["particulars"];
            $response["success"] = 1;
            $response["item"] = array();
            array_push($response["item"], $item);
            echo json_encode($response);
        } else {
            $response["success"] = 0;
            $response["message"] = "No item found";
            echo json_encode($response);
        }
    } else {
        $response["success"] = 0;
        $response["message"] = "No item found";
        echo json_encode($response);
    }
} else {
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";
    echo json_encode($response);
}
?>