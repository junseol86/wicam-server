<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $sql = "SELECT current_version FROM wicam.app_version";

    $result = mysql_query($sql, $mydb);
    $row = mysql_fetch_row($result);
    $result = $row[0];


    $json_data['result'] = $result;

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);


?>