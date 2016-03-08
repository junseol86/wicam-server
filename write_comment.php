<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $user_id = $_GET['user_id'];
    $device_id = $_GET['device_id'];

    $default_code = $_GET['default_code'];
    $content_id = $_GET['content_id'];
    $content_type = $_GET['content_type'];
    $item_id = $_GET['item_id'];

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;

    if ($unauthorized == 0) {

        $table;
        $comment_idx;

        switch ($default_code) {
            case 0: $item = 'custom'; break;
            case 1: $item = 'delivery'; break;
            case 2: $item = 'restaurant'; break;
            case 4: $item = 'phonebook'; break;
            case 5: $item = 'advertise'; break;
        }

        $sql_write = "INSERT INTO wicam.".$item."_comment (item_idx, report, url_link, comment, writer_idx, writer_nickname, write_time)
                    VALUES('$item_id', $report, '$url_link', '$comment', '$user_id', '$user_nickname', '$now')";
        $result_write = mysql_query($sql_write, $mydb);

        $json_data['result'] = $result_write;

        $comment_id = '';
        $photo_array = array();
        $main_photo = '';

        $reports = 0;
        $comments = 0;

        if ($result_write == 'true') {
            $sql_comment_id = "SELECT MAX(item_comment_idx) FROM wicam.".$item."_comment";
            $result_comment_id = mysql_query($sql_comment_id, $mydb);
            $row_comment_id = mysql_fetch_row($result_comment_id);
            $comment_id = $row_comment_id[0];

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
        }

        $json_data['comment_id'] = $comment_id;
        $json_data['reports'] = $reports;
        $json_data['comments'] = $comments;

        $json = json_encode($json_data);
        echo($json);

    }

    mysql_close($mydb);

?>