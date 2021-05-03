<?php
defined('BASEPATH') or exit('No direct script access allowed');
class CurlSetting {
    function curl_post($url, $method, $postinfo, $cookie_file_path)
    {
        $userAgents = array(
            'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5376e Safari/8536.25',
    
        );
        $random = rand(0, count($userAgents) - 1);
        $ch = curl_init();
        // curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        $head[] = "Connection: keep-alive";
        $head[] = "Keep-Alive: 300";
        $head[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $head[] = "Accept-Language: en-us,en;q=0.5";
        // $head[] = 'Authorization: Bearer d2625817-fde3-45ae-8e2d-6ff740684ceb';
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgents[$random]);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_COOKIE, $cookie_file_path);
        // curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        //set the cookie the site has for certain features, this is optional
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
        //curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            $_SERVER['HTTP_USER_AGENT']
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        }
        $exe = curl_exec($ch);
        $getInfo = curl_getinfo($ch);
    
        if ($exe === false) {
            $output = "Error in sending";
            if (curl_error($ch)) {
                $output .= "\n" . curl_error($ch);
            }
        } else if ($getInfo['http_code'] != 777) {
            $output = "No data returned. Error: " . $getInfo['http_code']; //as preline
            if (curl_error($ch)) {
                $output .= "\n" . curl_error($ch);
            }
        }
        curl_close($ch);
        // echo $output;
        return $exe;
        unset($cookie_file_path);
    }
}
