<?php
$response = array();
require_once __DIR__ . '/db_connect.php';
$db = new DB_CONNECT();
$result = mysql_query("SELECT * FROM lfms") or die(mysql_error());
if ($result && mysql_num_rows($result) > 0) {
    $response["items"] = array();
    while ($row = mysql_fetch_array($result)) {
        $item = array();
        $item["sno"] = $row["sno"];
        $item["particulars"] = $row["particulars"];
        $item["Brand_Name"] = $row["Brand_Name"];
        $item["Found_By"] = $row["Found_By"];
        array_push($response["items"], $item);
    }
    $response["success"] = 1;
    echo json_encode($response);
} else {
    $response["success"] = 0;
    $response["message"] = "No Items found";
    echo json_encode($response);
}
?>