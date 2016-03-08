<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $file_path = '';

    $default_code = $_GET['default_code'];
    $content_id = $_GET['content_id'];
    $content_type = $_GET['content_type'];
    $image_name = $_GET['image_name'];


    switch ($default_code) {
        case 0: $file_path = "../image/custom/"; break;
        case 1: $file_path = "../image/delivery/"; break;
        case 2: $file_path = "../image/restaurant/"; break;
        case 4: $file_path = "../image/phonebook/"; break;
        case 5: $file_path = "../image/advertise/"; break;
    }

    $file_path = $file_path.$image_name;

    $result = '';

    if (file_exists($file_path)) {
        if (unlink($file_path))
            $result = 'success';
        else
            $result = 'not_success';
    }
    else
        $result = 'success';

    $json_data['result'] = $result;
    $json = json_encode($json_data);
    echo $json;

    mysql_close($mydb);

?>