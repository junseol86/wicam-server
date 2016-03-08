<?php
/**
 * Hyeonmin Ko
 */

    $sql_check = "SELECT device_id FROM wicam.modify_delete_authority
                  WHERE default_code = $default_code AND content_idx = $content_id
                  AND item_idx = $item_id AND user_idx = $user_id AND authorized = 1";
    $result_check = mysql_query($sql_check, $mydb);
    if (mysql_num_rows($result_check) > 0) {
        $check_row = mysql_fetch_row($result_check);
        $check_device_id = $check_row[0];
        if ($device_id != $check_device_id)
            $unauthorized = 1;
    }
    else
        $unauthorized = 1;

?>