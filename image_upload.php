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

    switch ($default_code) {
        case 0: $file_path = "../image/custom/"; break;
        case 1: $file_path = "../image/delivery/"; break;
        case 2: $file_path = "../image/restaurant/"; break;
        case 4: $file_path = "../image/phonebook/"; break;
        case 5: $file_path = "../image/advertise/"; break;
    }

    $file_path = $file_path . basename( $_FILES['uploaded_file']['name']);

    // 같은 이름의 파일이 있을 때 삭제
    if (file_exists($file_path))
        unlink($file_path);

    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
        echo "success";
    } else{
        echo "fail";
    }

    mysql_close($mydb);

?>