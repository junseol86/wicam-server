<?php
    // 기기를 바꾼 사용자가 이전 닉네임을 되찾을 때 사용.
    // 이전 계정의 device id를 지금의 것으로 바꾸고, 지금의 device id로 만든 계정이 있다면 삭제한다.

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $now = date("y-m-d H:i:s");

    $result = 0;
    $sql_check = "SELECT * FROM wicam.user WHERE user_nickname = '$nickname' AND password = '$password'";
    $result_check = mysql_query($sql_check, $mydb);

    $check = mysql_num_rows($result_check);

    if ($check == 1) {

        $array_id = mysql_fetch_array($result_check);
        $id = $array_id['user_idx'];

        $sql_delete = "DELETE FROM wicam.user WHERE device_id = '$device_id' AND user_idx != $id";
        mysql_query($sql_delete, $mydb);

        $sql_modify = "UPDATE wicam.user set device_id = '$device_id' WHERE user_idx = $id";
        mysql_query($sql_modify, $mydb);


        $sql_got_id = "SELECT user_idx FROM wicam.user WHERE device_id = '$device_id'";
        $result_got_id = mysql_query($sql_got_id, $mydb);
        $row_got_id = mysql_fetch_row($result_got_id);
        $result = $row_got_id[0];
    }

    $json_data['id'] = $result;
    $json_data['nickname'] = $nickname;

    $json = json_encode($json_data);
    echo($json);
    mysql_close($mydb);

?>