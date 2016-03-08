<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $user_id = $_GET['user_id'];

    $content_id = $_GET['content_id'];

    $sql_favorite = "SELECT * FROM wicam.content_favorite WHERE content_idx = $content_id AND user_idx = $user_id";
    $result_favorite = mysql_query($sql_favorite, $mydb);

    $sql_toggle = mysql_num_rows($result_favorite) == 0 ? "INSERT INTO wicam.content_favorite (content_idx, user_idx) VALUES ($content_id, $user_id)"
                                                            : "DELETE FROM wicam.content_favorite WHERE content_idx = $content_id AND user_idx = $user_id";
    mysql_query($sql_toggle);

    $result_favorite = mysql_query($sql_favorite, $mydb);

    $json_data['result'] = mysql_num_rows($result_favorite);

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>