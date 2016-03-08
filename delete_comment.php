<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $default_code = $_GET['default_code'];
    $content_id = $_GET['content_id'];
    $content_type = $_GET['content_type'];
    $item_id = $_GET['item_id'];
    $user_id = $_GET['user_id'];
    $writer_id = $_GET['writer_id'];
    $device_id = $_GET['device_id'];
    $comment_id = $_GET['comment_id'];
    $authority = $_GET['authority_code'];

    $unauthorized = 0;

    // 수정하거나 지울 권한이 있는지 확인3
    $sql_check_device = "SELECT device_id from wicam.user WHERE user_idx = $writer_id";
    $result_check_device = mysql_query($sql_check_device);
    if (mysql_num_rows($result_check_device) == 0 || $user_id != $writer_id)
        $unauthorized = 1;
    else {
        $row_check_device = mysql_fetch_row($result_check_device);
        if ($row_check_device[0] != $device_id) {
            $unauthorized = 1;

//            댓글이 아닌 아이템, 즉 수정권한을 부여할 수 있는 정보는 아래를 실행하여 수정권한을 검색한다.
//            include 'authority_check.php';
        }
    }

    // 운영자에게는 무조건 허가한다.
    if ($authority == $authority_code)
        $unauthorized = 0;


    $table;
    $comment_idx;


    switch ($default_code) {
        case 0: $item = 'custom'; break;
        case 1: $item = 'delivery'; break;
        case 2: $item = 'restaurant'; break;
        case 4: $item = 'phonebook'; break;
        case 5: $item = 'advertise'; break;
    }

    if ($unauthorized == 0) {

        $sql_write = "DELETE FROM wicam.".$item."_comment WHERE item_comment_idx = ".$comment_id;
        $result_write = mysql_query($sql_write, $mydb);

        $json_data['result'] = $result_write;

        $reports = 0;
        $comments = 0;

        if ($result_write == 'true') {
            if (file_exists('../image/'.$item.'/'.$comment_id.'.jpg'))
                unlink('../image/'.$item.'/'.$comment_id.'.jpg');

            $sql_comments = "SELECT * FROM wicam.".$item."_comment WHERE item_idx = $item_id";
            $result_comments = mysql_query($sql_comments, $mydb);
            $comments = mysql_num_rows($result_comments);

            $sql_comment_counts = "UPDATE wicam.".$item." SET comments = $comments WHERE item_idx = $item_id";
            mysql_query($sql_comment_counts, $mydb);

            $sql_reports = "SELECT * FROM wicam.".$item."_comment WHERE item_idx = $item_id AND report = 1";
            $result_reports = mysql_query($sql_reports, $mydb);
            $reports = mysql_num_rows($result_reports);

            $sql_report_counts = "UPDATE wicam.".$item." SET reports = $reports WHERE item_idx = $item_id";
            mysql_query($sql_report_counts, $mydb);

            $sql_comment_like_delete = "DELETE FROM wicam.".$item."_comment_like WHERE item_comment_idx = $comment_id";
            mysql_query($sql_comment_like_delete, $mydb);
        }

        $json_data['reports'] = $reports;
        $json_data['comments'] = $comments;

        $json = json_encode($json_data);
        echo($json);

    }

    mysql_close($mydb);

?>