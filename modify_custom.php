<?php

    include 'login_info.php';

    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;
    else {
        $sql_writer = "SELECT writer_idx FROM wicam.custom WHERE item_idx = $item_id";
        $result_writer = mysql_query($sql_writer, $mydb);
        $row_writer = mysql_fetch_row($result_writer);
        $writer_id = $row_writer[0];

        $sql_device_id = "SELECT device_id FROM wicam.user WHERE user_idx = $writer_id";
        $result_device_id = mysql_query($sql_device_id, $mydb);
        $row_device_id = mysql_fetch_row($result_device_id);
        $writer_device_id = $row_device_id[0];

        if (!($user_id == $writer_id && $device_id == $writer_device_id)) {
            include 'authority_check.php';
        }
    }

    if ($unauthorized == 0) {

        $sql_modify = "UPDATE wicam.custom SET
                        item_name = '$custom_name', url_link = '$url_link', description = '$description',
                        phone1 = '$phone1', phone2 = '$phone2',
                        modifier_idx = '$user_id', modifier_nickname = '$user_nickname', modify_time = '$now'
                        WHERE item_idx = '$item_id'";
        $result_modify = mysql_query($sql_modify, $mydb);

        if ($result_modify == true) {
            $json_data['result'] = $item_id;

            $json = json_encode($json_data);
            echo($json);
        }
    }

    mysql_close($mydb);

?>