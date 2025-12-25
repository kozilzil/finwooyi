<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Common_lib
{
    // TEXT 형식으로 변환
    function get_text($str, $html=0, $restore=false)
    {
        $source[] = "<";
        $target[] = "&lt;";
        $source[] = ">";
        $target[] = "&gt;";
        $source[] = "\"";
        $target[] = "&#034;";
        $source[] = "\'";
        $target[] = "&#039;";

        if($restore)
            $str = str_replace($target, $source, $str);

        // 3.31
        // TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
        if ($html == 0) {
            $str = html_symbol($str);
        }

        if ($html) {
            $source[] = "\n";
            $target[] = "<br/>";
        }

        return str_replace($source, $target, $str);
    }

    // 휴대폰번호의 숫자만 취한 후 중간에 하이픈(-)을 넣는다.
    function hyphen_hp_number($hp)
    {
        $hp = preg_replace("/[^0-9]/", "", $hp);
        return preg_replace("/([0-9]{3})([0-9]{3,4})([0-9]{4})$/", "\\1-\\2-\\3", $hp);
    }

    function const_define()
    {
        $define['DOMAIN'] = '';
        $define['COOKIE_DOMAIN'] = '';
        $define['ADMIN_DIR'] = '';
        $define['DATA_DIR'] = '';
        $define['SNS_DIR'] = '';
        $define['SESSION_DIR'] = '';
        $define['EDITOR_DIR'] = '';
        $define['URL'] = '';

        return $define;
    }
}

?>