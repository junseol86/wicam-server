<?php
$daum_key = '681d7d93fd97950cb18e9a648f60c4cd';

// 주소를 가지고 해당 좌표를 가지고 오는 함수

    $url ="http://apis.daum.net/local/geo/addr2coord?apikey=".$daum_key."&q=".urlencode($address)."&output=json&inputCoordSystem=WGS84";

    $retvalfo = parse_url($url);
    $host = $retvalfo["host"];
    $port = 80;
    $path = $retvalfo["path"];
    $sxe = '';
    $retval = '';

    if ($retvalfo["query"] != "") $path .= "?" . $retvalfo["query"];
    $out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";

    $fp = fsockopen($host, $port, $errno, $errstr, 30);

    if(!$fp){
        echo "$errstr ($errno) <br>\n";
    }
    else
    {
        fputs($fp, $out);
        $body = false;

        while(!feof($fp)) {
            $s = fgets($fp, 128);
            if ($body)
                $retval.= $s;
            if ($s == "\r\n")
                $body = true;
        }

        fclose($fp);
        //$retVal;
    }

    $sxe = str_replace('\\', '', $sxe);
    $sxe = json_decode($retval, true);
//    $sxe = substr($sxe, 14, strlen($sxe)-16);
    $return = json_encode($sxe);
    echo substr($return, 11, strlen($return)-12);

?>
