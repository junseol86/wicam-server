<?php

    // 닉네임 등록시 중복여부 확인 페이지

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $sql_overlap = "SELECT * FROM wicam.user WHERE user_nickname = '$user_nickname'";
    $result_overlap = mysql_query($sql_overlap, $mydb);

    $json_data['result'] = mysql_num_rows($result_overlap);

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);


?>