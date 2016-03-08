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

        $sql_write = "INSERT INTO wicam.content (default_code, content_name, description, contact, school_idx, content_type, url_link, package_name,
                                                            writer_idx, writer_nickname, modifier_idx, modifier_nickname, write_time, modify_time)
                    VALUES('0', '$content_name', '$description', '$contact', '$school_id', '$content_type', '$url_link', '$package_name',
                            '$user_id', '$user_nickname', '$user_id', '$user_nickname', '$now', '$now')";
        $result_write = mysql_query($sql_write, $mydb);

        $id = '';
        $sql_id = "SELECT MAX(content_idx) FROM wicam.content";
        $result_id = mysql_query($sql_id, $mydb);
        $row_id = mysql_fetch_row($result_id);
        $id = $row_id[0];

        $json_data['result'] = $id;

        $json = json_encode($json_data);
        echo($json);
    }

    mysql_close($mydb);

?>