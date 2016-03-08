<?php

    include 'login_info.php';


    // 세션 시작
    session_start();

    date_default_timezone_set('Asia/Seoul');
    $today = date("y-m-d");

    $unauthorized = 0;
    $sql_authority_check = "SELECT * FROM wicam.user WHERE user_idx = $user_id AND device_id = '$device_id' AND blacklist != 1";
    $result_authority_check = mysql_query($sql_authority_check);
    if (mysql_num_rows($result_authority_check) == 0)
        $unauthorized = 1;

    if ($unauthorized == 0) {

        if ($default_code == 0) {
            $sql_check = "SELECT * FROM wicam.content_user_views WHERE content_idx = $content_id AND user_idx = $user_id AND view_date = '$today'";
            $result_check = mysql_query($sql_check, $mydb);

            if (mysql_num_rows($result_check) == 0) {

                $sql_insert = "INSERT INTO wicam.content_user_views (content_idx, user_idx, view_date) VALUES ($content_id, $user_id, '$today')";
                mysql_query($sql_insert, $mydb);

                $sql_check_again = "SELECT * FROM wicam.content_views WHERE content_idx = $content_id AND view_date = '$today'";
                $result_check_again = mysql_query($sql_check_again, $mydb);

                if (mysql_num_rows($result_check_again) == 0) {
                    $sql_insert = "INSERT INTO wicam.content_views (content_idx, views, view_date) VALUES ($content_id, 1, '$today')";
                    mysql_query($sql_insert, $mydb);
                }
                else {
                    $array_views = mysql_fetch_array($result_check_again);
                    $views = $array_views['views'] + 1;
                    $sql_update = "UPDATE wicam.content_views SET views = $views WHERE content_idx = $content_id AND view_date = '$today'";
                    mysql_query($sql_update, $mydb);
                }

            }

        }
        else {
            $sql_check = "SELECT * FROM wicam.content_default_user_views WHERE content_idx = $content_id AND user_idx = $user_id AND view_date = '$today'";
            $result_check = mysql_query($sql_check, $mydb);

            if (mysql_num_rows($result_check) == 0) {

                $sql_insert = "INSERT INTO wicam.content_default_user_views (content_idx, school_idx, user_idx, view_date) VALUES ($content_id, $school_id, $user_id, '$today')";
                mysql_query($sql_insert, $mydb);

                $sql_check_again = "SELECT * FROM wicam.content_default_views WHERE content_idx = $content_id AND view_date = '$today'";
                $result_check_again = mysql_query($sql_check_again, $mydb);

                if (mysql_num_rows($result_check_again) == 0) {
                    $sql_insert = "INSERT INTO wicam.content_default_views (content_idx, school_idx, views, view_date) VALUES ($content_id, $school_id, 1, '$today')";
                    mysql_query($sql_insert, $mydb);
                }
                else {
                    $array_views = mysql_fetch_array($result_check_again);
                    $views = $array_views['views'] + 1;
                    $sql_update = "UPDATE wicam.content_default_views SET views = $views WHERE content_idx = $content_id AND view_date = '$today'";
                    mysql_query($sql_update, $mydb);
                }

            }
        }

    }

    echo 'done';

    mysql_close($mydb);

?>