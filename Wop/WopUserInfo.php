<?php
/**
 * Created by PhpStorm.
 * User: guowei
 * Date: 17/3/25
 * Time: 下午11:46
 */

namespace LumenTool\Wop;


use LumenTool\Util\Http;

class WopUserInfo {
    public static function getWxOpenId($wop_app_id = null, $cookie_name = null) {
        $user_info = self::getWxUserInfo($wop_app_id, $cookie_name);
        if (empty($user_info['wx_open_id'])) {
            return null;
        }
        return $user_info['wx_open_id'];
    }

    public static function getWxUserInfo($wop_app_id = null, $cookie_name = null) {
        static $user_info = [];
        $url = env('WOP_URL');
        if (empty($url)) {
            dd('.env文件中未定义WOP_URL');
        }
        if (empty($wop_app_id)) {
            $wop_app_id = env('WOP_APP_ID');
        }
        if (empty($wop_app_id)) {
            dd('.env文件中未定义WOP_APP_ID');
        }

        if (empty($user_info[$wop_app_id])) {
            if (empty($cookie_name)) {
                $cookie_name = env('WOP_COOKIE', 'wop2');
            }
            if (empty($_COOKIE[$cookie_name])) {
                return [];
            }
            $cookie = $_COOKIE[$cookie_name];
            $res = Http::post($url . 'wx-base/get-user-info', ['app_id' => $wop_app_id, 'cookie' => $cookie]);
            if (!$res['result']) {
                return [];
            }
            $data = json_decode($res['data'], true);
            if (!$data['result']) {
                return [];
            }
            $user_info[$wop_app_id] = $data['data'];
        }
        return $user_info[$wop_app_id];
    }
}