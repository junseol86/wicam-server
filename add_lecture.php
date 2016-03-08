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

        $result = 0;

        $sql_overlap_check = "SELECT * FROM wicam.lecture WHERE lecture_name = '$lecture_name' AND professor_name = '$professor_name'";

        $result_overlap_check = mysql_query($sql_overlap_check, $mydb);

        if (mysql_num_rows($result_overlap_check) == 0) {


            $sql_write = "INSERT INTO wicam.lecture (lecture_name, professor_name, major, school_idx, writer_idx, write_time)
                    VALUES('$lecture_name', '$professor_name', '$major', '$school_id', '$user_id', '$now')";
            $result_write = mysql_query($sql_write, $mydb);

            $id = '';
            $sql_id = "SELECT MAX(item_idx) FROM wicam.lecture";
            $result_id = mysql_query($sql_id, $mydb);
            $row_id = mysql_fetch_row($result_id);
            $result = $row_id[0];
        }

        $json_data['result'] = $result;

        $json = json_encode($json_data);
        echo($json);
    }

    mysql_close($mydb);

?>