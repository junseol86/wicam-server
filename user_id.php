<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $device_id = $_GET['device_id'];

    $user_id = "";
    $user_nickname = "";

    $sql_check = "SELECT COUNT(*) FROM wicam.user WHERE device_id = '$device_id'";
    $result_check = mysql_query($sql_check, $mydb);
    $row_check = mysql_fetch_row($result_check);

    if ($row_check[0] == 1) {
        $sql_get = "SELECT * FROM wicam.user WHERE device_id = '$device_id'";
        $result_get = mysql_query($sql_get, $mydb);
        $array_get = mysql_fetch_array($result_get);
        $user_id = $array_get['user_idx'];
        $user_nickname = $array_get['user_nickname'];
        $blacklist = $array_get['blacklist'];

        $user_memo = '';
        if ($array_get['admin'] == 1)
            $user_memo = $authority_code;

        $json_data['user_id'] = $user_id;
        $json_data['user_nickname'] = $user_nickname;
        $json_data['user_memo'] = $user_memo;
        $json_data['blacklist'] = $blacklist;
        $json = json_encode($json_data);
        echo($json);
    }
    else {
        $sql_insert = "INSERT INTO wicam.user (device_id, sign_up_time) VALUES('$device_id', '$now')";
        $result_insert = mysql_query($sql_insert, $mydb);

        if ($result_insert == 1) {
            $sql_get = "SELECT * FROM wicam.user WHERE device_id = '$device_id'";
            $result_get = mysql_query($sql_get, $mydb);
            $array_get = mysql_fetch_array($result_get);
            $user_id = $array_get['user_idx'];
            $user_nickname = $array_get['user_nickname'];
            $blacklist = $array_get['blacklist'];

            $user_memo = '';

            $json_data['user_id'] = $user_id;
            $json_data['user_nickname'] = $user_nickname;
            $json_data['user_memo'] = $user_memo;
            $json_data['blacklist'] = $blacklist;
            $json = json_encode($json_data);
            echo($json);
        }
    }


    mysql_close($mydb);


?>