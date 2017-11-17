<?php
/**
 *
 * User: linyi(linyi05@baidu.com)
 * Date: 2017/8/12
 * Time: 14:52
 */

namespace App\Http\Controllers;

use App\Model\TulingRobot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WechatController extends Controller
{
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $wechat = app('wechat');
        $wechat->server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    $content = $message->Content;
                    $r = $this->handle($content);
                    return $r;
                    break;
                case 'image':
                    $picUrl = $message->PicUrl;
                    return '收到图片消息： ' . $picUrl;
                    break;
                case 'voice':
                    return '收到语音消息: ' . $message->Recognition;
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });

        Log::info('return response.');

        return $wechat->server->serve();
    }

    private function handle($content)
    {
        Mail::send('emails.message', ['content' => $content], function($message)
        {
            $message->to('ahlinyi@qq.com', '我')->subject('有人发了新消息!');
        });

        switch ($content) {
            case 1:
                return '';
            default:
                return TulingRobot::getText($content);
        }
    }
}