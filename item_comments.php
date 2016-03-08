<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $unit = 5;
    $page = $_GET['page'];
    $item_id = $_GET['item_id'];
    $user_id = $_GET['user_id'];
    $report_only = $_GET['report_only'];

    $default_code = $_GET['default_code'];
    $content_id = $_GET['content_id'];
    $content_type = $_GET['content_type'];
    $item_id = $_GET['item_id'];


    $table;
    $item_idx;

    switch ($default_code) {
        case 0: $item = 'custom'; break;
        case 1: $item = 'delivery'; break;
        case 2: $item = 'restaurant'; break;
        case 4: $item = 'phonebook'; break;
        case 5: $item = 'advertise'; break;
    }

    $condition = $report_only == 0 ? ' ' : ' AND report = 1 ';

    $sql_count = "SELECT * FROM wicam.".$item."_comment
                  WHERE item_idx = $item_id".$condition."ORDER BY item_comment_idx DESC";
    $result_count = mysql_query($sql_count, $mydb);
    $total_count = mysql_num_rows($result_count);
    $to_be_added = $total_count - ($page * $unit);

    $json_data['num_results'] = iconv("CP949", "UTF-8", "$total_count");
    $json_data['page'] = $page;
    $json_data['unit'] = $unit;

    $sql_list = $sql_count." LIMIT ".strval($unit*$page).", {$unit}";
    $result_list = mysql_query($sql_list, $mydb);

    for ($i = 0; $i < (($unit < $to_be_added) ? $unit : $to_be_added); $i++) {
        mysql_data_seek($result_list, $i);
        $row = mysql_fetch_array($result_list);

        $has_photo = 0;

        if (file_exists('../image/'.$item.'/'.$row['item_comment_idx'].'.jpg'))
            $has_photo = 1;

        $likes = 0;
        $my_like = 0;

        $item_comment_idx = $row['item_comment_idx'];
        $item_idx = $row['item_idx'];

        $sql_likes = "SELECT * FROM wicam.".$item."_comment_like WHERE item_comment_idx = $item_comment_idx";
        $result_likes = mysql_query($sql_likes, $mydb);
        $likes = mysql_num_rows($result_likes);

        if ($likes > 0) {
            $sql_my_like = $sql_likes." AND user_idx = $user_id";
            $result_my_like = mysql_query($sql_my_like, $mydb);
            $my_like = mysql_num_rows($result_my_like);
        }

        $json_data['results'][$i] = array("item_comment_id"=>"$item_comment_idx", "item_id"=>"$item_idx",
                                            "url_link"=>"$row[url_link]", "comment"=>"$row[comment]",
                                            "writer_id"=>"$row[writer_idx]", "writer_nickname"=>"$row[writer_nickname]", "write_time"=>"$row[write_time]",
                                            "likes"=>$likes, "my_like"=>$my_like,
                                            "has_photo"=>$has_photo, "report"=>$row['report']);
    }

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>