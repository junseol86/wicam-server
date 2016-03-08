<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $user_id = $_GET['user_id'];
    $device_id = $_GET['device_id'];

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;

    if ($unauthorized == 0) {

        $sql_lecture_delete = "DELETE FROM wicam.lecture WHERE item_idx = $item_id";
        mysql_query($sql_lecture_delete, $mydb);

        $sql_lecture_assess_delete = "DELETE FROM wicam.lecture_assess WHERE item_idx = $item_id";
        mysql_query($sql_lecture_assess_delete, $mydb);

        $json_data['result'] = 'true';

        $json = json_encode($json_data);
        echo($json);
    }

    mysql_close($mydb);

?>