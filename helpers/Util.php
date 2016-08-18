<?php

namespace app\helpers;

/**
 * Class Util 工具包类
 * @package app\helpers
 */
class Util
{

    /**
     * 创建目录
     * @param string $dir
     * @return boolean 创建返回true 失败返回false
     */
    public static function createDir($dir)
    {
        $dir = str_replace('\\', '/', $dir);
        $dirList = explode('/', $dir);
        $create = '';
        foreach ($dirList as $d) {
            $create .= $d;
            if (!($d == '.') && !($d == '..')) {
                if (!file_exists($create)) {
                    @mkdir($create, 0755);
                }
            }
            $create .= '/';
        }
        return file_exists($dir);
    }

    /**
     * 获取客户IP
     * @return string
     */
    public static function getClientIp()
    {
        static $__ip = NULL;
        if ($__ip !== NULL)
            return $__ip;
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $__ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $__ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $__ip = $_SERVER['REMOTE_ADDR'];
        }
        $__ip = (false !== ip2long($__ip)) ? $__ip : '0.0.0.0';
        return $__ip;
    }

    /**
     * 打印数据
     * @param $data
     */
    public static function dump($data)
    {
        $func = is_array($data) || is_object($data) ? "print_r" : "var_dump";
        echo '<pre>';
        $func($data);
        exit;
    }

    /**
     * 加密
     */
    public static function encrypt($code, $seed = 'net.269.com') {

        $code = strtoupper($code);
        $clen = strlen($code);
        $hash = strtoupper(md5($seed));
        $hlen = strlen($hash);
        $return = '';
        for ($i = 0; $i < $clen; $i ++) {
            $j = intval(fmod($i, $hlen));
            $s = ord($code{$i}) + ord($hash{$j});
            $s = strtoupper(dechex($s));
            $return .= $s;
        }
        return $return;
    }
    /**
     * 解密
     * @param $code
     * @param string $seed
     * @return bool|string
     */
    public static function decrypt($code, $seed = 'net.269.com') {
        $code = strtoupper($code);
        $clen = strlen($code);
        if (($clen % 2) != 0) {
            return false;
        }

        $hash = strtoupper(md5($seed));
        $hlen = strlen($hash);
        $unit = array();
        for ($i = 0; $i < $clen; $i += 2) {
            $unit[] = $code{$i} . $code{$i + 1};
        }
        $size = count($unit);
        $return = '';
        for ($i = 0; $i < $size; $i ++ ) {
            $j = intval(fmod($i, $hlen));
            $s = intval(hexdec($unit[$i])) - ord($hash{$j});
            $return .= chr($s);
        }
        return strtolower($return);
    }
    /**
     * 根据当前ip 获取地理位置
     */

      public static function getAddressByIp()
      {
          $ip = static::getClientIp();
          $url="http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=";
          $data = json_decode(file_get_contents($url));
          return $data;
      }

    /**
     * 二维数组去重
     * @param $array2D
     * @param bool $stkeep
     * @param bool $ndformat
     * @return mixed
     */
    public  static  function unique_arr($array2D,$stkeep=false,$ndformat=true)
    {
        // 判断是否保留一级数组键 (一级数组键可以为非数字)
        if($stkeep) $stArr = array_keys($array2D);

        // 判断是否保留二级数组键 (所有二级数组键必须相同)
        if($ndformat) $ndArr = array_keys(end($array2D));

        //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
        foreach ($array2D as $v){
            $v = join(",",$v);
            $temp[] = $v;
        }

        //去掉重复的字符串,也就是重复的一维数组
        $temp = array_unique($temp);

        //再将拆开的数组重新组装
        foreach ($temp as $k => $v)
        {
            if($stkeep) $k = $stArr[$k];
            if($ndformat)
            {
                $tempArr = explode(",",$v);
                foreach($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
            }
            else $output[$k] = explode(",",$v);
        }

        return $output;
    }
    /**
     * https请求（支持GET和POST）
     * @param $url
     * @param null $data
     * @return mixed
     */
    public static function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            //设置json格式
            $header = array(
                'Content-Type: application/json',
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    /**
     * 判断客户来源是手机还是电脑
     * @return bool
     */
   public static  function isMobile(){

        $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        $useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';

        function CheckSubstrs($substrs,$text){

            foreach($substrs as $substr)

                if(false!==strpos($text,$substr)){

                    return true;
                }
            return false;
        }
        $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
        $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
        $found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) || CheckSubstrs($mobile_token_list,$useragent);
        if ($found_mobile){

            return true;
        }else{

            return false;
        }
    }
}