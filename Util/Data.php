<?php
/**
 * Created by PhpStorm.
 * User: dxc
 * Date: 2016/5/27
 * Time: 14:00
 */

namespace LumenTool\Util;


class Data
{
    /**
     *    作用：array转xml
     */
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";

            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     *    作用：将xml转为array
     */
    public static function xmlToArray($xml)
    {
        //将XML转为array
        if (!is_string($xml)) {
            return null;
        }
        libxml_disable_entity_loader(true);
        try {
            $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
            $array_data = json_decode(json_encode($data), true);
        } catch (\Exception $e) {
            return [];
        }

        return $array_data;
    }

    public static function json_encode_cn($res)
    {
        return json_encode($res, JSON_UNESCAPED_UNICODE);
    }
}