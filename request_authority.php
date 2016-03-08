<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $user_id = $_GET['user_id'];

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;

    if ($unauthorized == 0) {

        $sql_request = "INSERT INTO wicam.modify_delete_authority (`default_code`, `content_idx`, `item_idx`, `item_name`, `user_idx`, `user_nickname`, `device_id`, `phone_number`, `reason`, `request_time`)
                         VALUES ('$default_code', '$content_idx', '$item_id', '$item_name', '$user_id', '$user_nickname', '$device_id', '$phone_number', '$reason', '$now')";

        $json_data['result'] = mysql_query($sql_request, $mydb);

        $json = json_encode($json_data);
        echo($json);

    }


    mysql_close($mydb);

?>