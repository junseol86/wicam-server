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

    // 컨텐츠 작성자가 맞는지 검사
    $sql_owner_check = "SELECT writer_idx FROM wicam.content WHERE content_idx = $content_id";
    $result_owner_check = mysql_query($sql_owner_check, $mydb);
    $row_result_check = mysql_fetch_row($result_owner_check);

    if ($user_id != $row_result_check[0])
        $unauthorized = 1;

    if ($unauthorized == 0) {

        $sql_modify = "UPDATE wicam.content SET content_name = '$content_name', description = '$description', contact = '$contact', school_idx = '$school_id', url_link = '$url_link', package_name = '$package_name',
                        modifier_idx = '$user_id', modifier_nickname = '$user_nickname', modify_time = '$now'
                        WHERE content_idx = '$content_id'";
        $result_modify = mysql_query($sql_modify, $mydb);

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