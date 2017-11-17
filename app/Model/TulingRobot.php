<?php

namespace App\Model;

use EasyWeChat\Message\News;

/**
 * 图灵机器人
 * User: linyi(linyi05@baidu.com)
 * Date: 2017/8/12
 * Time: 15:40
 */
class TulingRobot
{
    private static function getPicText($title, $chatJson, $cText, $other = "")
    {
        $cList = $chatJson['list'];
        $content = array();
        $content[] = new News(["title" => $cText, "description" => "", "image" => "", "url" => ""]);
        $i = 0;
        foreach ($cList as $key => $val) {
            if ($i > 8) {
                break;
            }
            $content[] = new News([
                "title" => $val[$title] . "\n" . ($other ? $val[$other] : ""),
                "description" => "",
                "url" => $val['detailurl'],
                "image" => $val['icon'],
            ]);
            $i++;
        }
        return $content;
    }

    public static function getText($keyword = "")
    {
        $chatURL = "http://www.tuling123.com/openapi/api?key=d4979a14a52ec4bdf6a46a300a4b04b4&info={$keyword}";
        $chatStr = file_get_contents($chatURL);
        $chatJson = json_decode($chatStr, true);
        $code = empty($chatJson['code']) ? null : $chatJson['code'];
        $cText = empty($chatJson['text']) ? '' : $chatJson['text'];
        $cUrl = empty($chatJson['url']) ? '' : $chatJson['url'];
        $content = '';
        switch ($code) {
            case '100000':
                $content = $cText;
                break;

            case '200000':
                $content = $cText . $cUrl;
                break;

            case '301000':
                $content = "自己百度吧[呲牙]";
                break;

            case '302000':
                $content = self::getPicText('article', $chatJson, $cText);
                break;

            case '304000':
                $content = self::getPicText('name', $chatJson, $cText);
                break;

            case '305000':
                $cList = $chatJson['list'];
                $content = array();
                $content[] = array("Title" => $cText, "Description" => "", "PicUrl" => "", "Url" => "");
                $i = 0;
                foreach ($cList as $key => $val) {
                    if ($i > 9) {
                        break;
                    }
                    $content[] = array("Title" => $val['start'] - $val['terminal'] . "\n" . $val['trainnum'] . " " . $val['starttime'],
                        "Description" => "", "PicUrl" => $val['icon'], "Url" => $val['detailurl']);
                    $i++;
                }
                break;

            case '306000':
                $cList = $chatJson['list'];
                $content = array();
                $content[] = array("Title" => $cText, "Description" => "", "PicUrl" => "", "Url" => "");
                $i = 0;
                foreach ($cList as $key => $val) {
                    if ($i > 9) {
                        break;
                    }
                    $content[] = array("Title" => $val['flight'] . "\n" . $val['route'] . " " . $val['starttime'] . " " . $val['endtime'],
                        "Description" => "", "PicUrl" => $val['icon'], "Url" => $val['detailurl']);
                    $i++;
                }
                break;

            case '307000':
            case '309000':
            case '311000':
            case '312000':
                $content = self::getPicText('name', $chatJson, $cText, 'price');
                break;

            case '308000':
                $content = self::getPicText('name', $chatJson, $cText, 'info');
                break;

            case '310000':
                $content = self::getPicText('info', $chatJson, $cText, 'number');
                break;
        }
        return $content;
    }
}