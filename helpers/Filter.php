<?php
namespace app\helpers;

class Filter
{
    const INT = 0;
    const STRING = 1;
    const TEXT = 2;
    const TEXT_BR = 3;
    const FLOAT = 4;
    const DATE = 5;
    const DATE_TIME = 6;

    //
    static function int($name, $value = 0, $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::INT);
    }

    static function float($name, $value = 0, $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::FLOAT);
    }

    static function str($name, $value = '', $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::STRING);
    }

    static function txt($name, $value = '', $max = NULL, $min = NULL, $remote = FALSE)
    {
        return self::_getPara($name, $value, $max, $min, self::TEXT, FALSE, $remote);
    }

    static function txtbr($name, $value = '', $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::TEXT_BR);
    }

    static function date($name, $value = '', $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::DATE);
    }

    static function datetime($name, $format = "Y-m-d", $value = 0, $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::DATE_TIME, FALSE, FALSE, $format);
    }

    //
    static function intArr($name, $value = 0, $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::INT, TRUE);
    }

    static function floatArr($name, $value = 0, $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::FLOAT, TRUE);
    }

    static function strArr($name, $value = '', $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::STRING, TRUE);
    }

    static function txtArr($name, $value = '', $max = NULL, $min = NULL, $remote = FALSE)
    {
        return self::_getPara($name, $value, $max, $min, self::TEXT, TRUE, $remote);
    }

    static function txtbrArr($name, $value = '', $max = NULL, $min = NULL)
    {
        return self::_getPara($name, $value, $max, $min, self::TEXT_BR, TRUE);
    }

    //
    static function Get($name, $value = '', $max = NULL, $min = NULL, $type = self::STRING, $array = FALSE, $remote = FALSE)
    {
        if ($array) {
            return self::_getArrayParas($name, $value, $max = NULL, $min = NULL, $type, $remote);
        } else {
            return self::_getParas($name, $value, $max = NULL, $min = NULL, $type, $remote);
        }
    }

    //
    private static function _getPara($name, $value = '', $max = NULL, $min = NULL, $type = self::STRING, $array = FALSE, $remote = FALSE, $format = "")
    {
        if ($array) {
            return self::_getArrayParas($name, $value, $max, $min, $type, $remote, $format);
        } else {
            return self::_getParas($name, $value, $max, $min, $type, $remote, $format);
        }
    }

    //
    private static function _getParas($name, $value = '', $max = NULL, $min = NULL, $type = self::STRING, $remote = FALSE, $format = "")
    {
        if (isset ($_POST [$name])) {
            $val = trim($_POST [$name]);
        } elseif (isset ($_GET [$name])) {
            $val = trim($_GET [$name]);
        } else {
            return $value;
        }

        switch ($type) {
            case self::INT :
                $val = is_numeric($val) ? intval($val) : $value;
                if ($min !== NULL && $val < $min) {
                    $val = $value;
                } elseif ($max !== NULL && $val > $max) {
                    $val = $value;
                }
                break;

            case self::FLOAT :
                $val = is_numeric($val) ? $val : $value;
                if ($min !== NULL && $val < $min) {
                    $val = $value;
                } elseif ($max !== NULL && $val > $max) {
                    $val = $value;
                }
                break;

            case self::TEXT :
                if ($remote) {
                    $val = @self::saveRemoteImages($val);
                }
                if ($val) {
                    $val = self::filterHtml($val);
                    if ($min !== NULL && strlen($val) < $min) {
                        $val = $value;
                    } elseif ($max !== NULL && strlen($val) > $max) {
                        $val = substr($val, 0, $max);
                    }
                }
                break;

            case self::TEXT_BR :
                if ($val) {
                    $val = self::filterHtml(self::unHtml($val));
                    if ($min !== NULL && strlen($val) < $min) {
                        $val = $value;
                    } elseif ($max !== NULL && strlen($val) > $max) {
                        $val = substr($val, 0, $max);
                    }
                }
                break;

            case self::DATE :
                if (!self::isdate($val, $format)) {
                    $val = $value;
                }
                break;

            case self::DATE_TIME :
                if (self::isdate($val, $format)) {
                    $val = self::getTime($val);
                    if ($min !== NULL && $val < $min) {
                        $val = $value;
                    } elseif ($max !== NULL && $val > $max) {
                        $val = $value;
                    }
                } else {
                    $val = $value;
                }
                break;

            default : // string
                $val = strip_tags($val);
                // $val = mysql_real_escape_string($val);
                if (get_magic_quotes_gpc()) {
                    $val = str_replace("\"", "&quot;", $val);
                    $val = str_replace("'", "&#039;", $val);
                } else {
                    $val = self::checkString($val);
                }
                if ($name == 'kw' && !self::isUtf8($val)) {
                    $val = iconv('gbk', 'utf-8', $val);
                }
                $val = (!$val && $value) ? $value : $val;
                if ($val) {
                    if ($min !== NULL && strlen($val) < $min) {
                        $val = $value;
                    } elseif ($max !== NULL && strlen($val) > $max) {
                        $val = substr($val, 0, $max);
                    }
                }
                break;
        }
        return $val;
    }

    //
    private static function _getArrayParas($name, $value = '', $max = NULL, $min = NULL, $type = self::STRING, $remote = FALSE, $format = "")
    {
        $i = 0;
        $result = null;
        if (isset ($_REQUEST [$name])) {
            if (is_array($_REQUEST [$name])) {
                foreach ($_REQUEST [$name] as $val) {
                    if ($val) {
                        switch ($type) {
                            case self::INT :
                                $val = is_numeric($val) ? intval($val) : $value;
                                if ($min !== NULL && $val < $min) {
                                    $val = $value;
                                } elseif ($max !== NULL && $val > $max) {
                                    $val = $value;
                                }
                                break;

                            case self::FLOAT :
                                $val = is_numeric($val) ? $val : $value;
                                if ($min !== NULL && $val < $min) {
                                    $val = $value;
                                } elseif ($max !== NULL && $val > $max) {
                                    $val = $value;
                                }
                                break;

                            case self::TEXT :
                                if ($remote) {
                                    $val = @self::saveRemoteImages($val);
                                }
                                if ($val) {
                                    $val == self::filterHtml($val);
                                    if ($min !== NULL && strlen($val) < $min) {
                                        $val = $value;
                                    } elseif ($max != NULL && strlen($val) > $max) {
                                        $val = substr($val, 0, $max);
                                    }
                                }
                                break;

                            case self::TEXT_BR :
                                if ($val) {
                                    $val = self::filterHtml(self::unHtml($val));
                                    if ($min !== NULL && strlen($val) < $min) {
                                        $val = $value;
                                    } elseif ($max !== NULL && strlen($val) > $max) {
                                        $val = substr($val, 0, $max);
                                    }
                                }
                                break;

                            case self::DATE :
                                if (!self::isdate($val, $format)) {
                                    $val = $value;
                                }
                                break;

                            case self::DATE_TIME :
                                if (self::isdate($val, $format)) {
                                    $val = self::getTime($val);
                                    if ($min !== NULL && $val < $min) {
                                        $val = $value;
                                    } elseif ($max !== NULL && $val > $max) {
                                        $val = $value;
                                    }
                                } else {
                                    $val = $value;
                                }
                                break;

                            default : // string
                                $val = strip_tags($val);
                                // $val = mysql_real_escape_string($val);
                                if (get_magic_quotes_gpc()) {
                                    $val = str_replace("\"", "&quot;", $val);
                                    $val = str_replace("'", "&#039;", $val);
                                } else {
                                    $val = self::checkString($val);
                                }
                                if ($name == 'kw' && !self::isUtf8($val)) {
                                    $val = iconv('gbk', 'utf-8', $val);
                                }
                                $val = (!$val && $value) ? $value : $val;
                                if ($val) {
                                    if ($min !== NULL && strlen($val) < $min) {
                                        $val = $value;
                                    } elseif ($max !== NULL && strlen($val) > $max) {
                                        $val = substr($val, 0, $max);
                                    }
                                }
                                break;
                        }
                        $result [$i] = trim($val);
                    } else {
                        $result [$i] = trim($value);
                    }
                    $i++;
                }
            } else {
                if (is_array($value)) {
                    foreach ($value as $v) {
                        $result[] = trim($v);
                    }
                } else {
                    $result = trim($value);
                }
            }
        }
        return $result;
    }

    static function filterHtml($str)
    {
        $farr = array(
            "/<!DOCTYPE([^>]*?)>/eis", // 过滤
            "/<(\/?)(html|body|head|link|meta|base|input)([^>]*?)>/eis", // <script
            "/<(script|i?frame|style|title|form)(.*?)<\/\\1>/eis", // 等可能引入恶意内容或恶意改变显示布局的代码,如果不需要插入flash等,还可以加入<object的过滤
            "/(<[^>]*?\s+)on[a-z]+\s*?=(\"|')([^\\2]*)\\2([^>]*}-->)/isU", // 过滤javascript的on事件
            "/\s+/", // 过滤多余的空白
            "/'/"
        );
        $tarr = array(
            "",
            "",
            "",
            "\\1\\4",
            " ",
            "&#039;"
        );
        $str = preg_replace($farr, $tarr, $str);
        return $str;
    }

    private static function checkString($str)
    {
        $farr = array(
            "//",
            "/(\'|--|;|exec|insert|update|delete|select)/isU"
        );
        $tarr = array(
            "",
            ""
        );
        $str = preg_replace($farr, $tarr, trim($str));
        return $str;
    }

    //
    static function unHtml($str)
    {
        $unrow = array(
            "不雅用语1",
            "不雅用语2"
        );
        $str = str_ireplace($unrow, "*", $str);
        // $str = htmlspecialchars ( $str, ENT_COMPAT );
        if (phpversion() > '5.4') {
            $str = htmlspecialchars($str, ENT_COMPAT, 'ISO-8859-1');
        } else {
            $str = htmlspecialchars($str, ENT_COMPAT);
        }
        $str = str_replace(chr(32) . chr(32), "&nbsp;&nbsp;", $str);
        $str = str_replace(chr(9) . chr(9), "&nbsp;&nbsp;", $str);
        $str = str_replace(chr(13), "", $str);
        $str = str_replace(chr(10), "<br />", $str);
        $str = str_replace(chr(34), " ", $str);
        $str = str_replace("&quot;", "\"", $str);
        $str = str_replace("&#039;", "'", $str);
        return trim($str);
    }

    //
    static function reHtml($str)
    {
        $str = str_replace("&nbsp;&nbsp;", chr(32) . chr(32), $str);
        $str = str_replace("&nbsp;&nbsp;", chr(9) . chr(9), $str);
        $str = str_replace("<br />", chr(10), $str);
        // $str = str_replace(" ", chr(34), $str);
        $str = str_replace("\"", "&quot;", $str);
        $str = str_replace("'", "&#039;", $str);
        return trim($str);
    }

    //
    static function isUtf8($str)
    {
        if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $str) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $str) == true || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $str) == true) {
            return true;
        } else {
            return false;
        }
    }

    //
    static function saveRemoteImages($details, $dirdate = "")
    {
        if ($details) {
            preg_match_all("/ src=(\"|\'){0,}(http:\/\/(.+?))(\"|\'|\s)/is", $details, $img_array);
            // $img_array = array_unique ( dhtmlspecialchars ( $img_array [2] )
            // );
            $img_array = array_unique($img_array [2]);
            if (count($img_array) > 0) {
                if (empty ($dirdate)) {
                    $dirdate = date('Ymd');
                }
                $savedir = _ETU6_UPLOADPATH_ . 'remote/' . $dirdate . '/';
                if (!is_dir($savedir)) {
                    @mkdir($savedir, 0777);
                    @fclose(fopen($savedir . '/index.html', 'w'));
                }
                foreach ($img_array as $key => $value) {
                    $ext = strtolower(substr(strrchr($value, "."), 1));
                    $extallow_array = explode('|', strtolower('GIF|JPG|JPEG|BMP|PNG'));
                    if (in_array($ext, $extallow_array)) {
                        ob_start();
                        @readfile($value);
                        $img = ob_get_contents();
                        ob_end_clean();
                        $size = strlen($img);
                        $filename = "etu6_" . date("YmdHis") . rand(1000, 10000) . "." . strtolower($ext);
                        $fp = @fopen($savedir . $filename, "w");
                        @fwrite($fp, $img);
                        @fclose($fp);
                        $details = str_replace($value, "/" . str_replace('../', '', $savedir) . $filename, $details);
                    }
                }
            }
        }
        return $details;
    }

    //
    private static function isdate($str, $format = "Y-m-d")
    {
        $format = $format ? $format : "Y-m-d";
        $strArr = explode("-", $str);
        if (empty ($strArr)) {
            return false;
        }
        foreach ($strArr as $val) {
            if (strlen($val) < 2) {
                $val = "0" . $val;
            }
            $newArr [] = $val;
        }
        $str = implode("-", $newArr);
        $unixTime = strtotime($str);
        $checkDate = date($format, $unixTime);
        if ($checkDate == $str) {
            return true;
        } else {
            return false;
        }
    }

    private static function getTime($str, $format = "Y-m-d")
    {
        $time = strtotime($str);
        if ($time == 0 && date("Y-m-d", $str) != '1970-01-01') {
            $time = 1970 * 2 - (date("Y", $str));
            $str = $time . date(str_replace("Y-", "", $format), $str);
            $time = strtotime($str);
            $time -= 2 * $time;
        }
        return $time;
    }
}