<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot;

use Hanson\Vbot\Message\Text;
use Kcloze\Bot\Api\Baidu;
use Kcloze\Bot\Api\Tuling;

class Reply
{
    private $message;
    private $options;

    public function __construct($message, $options)
    {
        $this->message =$message;
        $this->options =$options;
    }

    public function send()
    {
        $type=$this->message['type'];
        vbot('console')->log('Message Type：' . $type . ' From: ' . $this->message['from']['UserName']);

        switch ($type) {
            case 'text':
                //@我或者好友发消息都自动回复
                if (true == $this->message['isAt'] || $this->message['fromType'] == 'Friend') {
                    if (strstr($this->message['pure'], '百度') !== false) {
                        $baidu   = new Baidu();
                        $return  = $baidu->search($this->message['pure']);
                        foreach ((array) $return as $key => $value) {
                            if (isset($value['title']) && isset($value['url'])) {
                                Text::send($this->message['from']['UserName'], $value['title'] . ' ' . $value['url']);
                            }
                        }
                    } else {
                        $tuling =new Tuling($this->options);
                        $return =$tuling->search($this->message['pure']);
                        Text::send($this->message['from']['UserName'], $return);
                    }
                }

                break;
            case 'voice':
                // code...
                break;
            case 'image':
                // code...
                break;
            case 'emoticon':
                // code...
                break;
            case 'red_packet':
                // code...
                break;
            case 'new_friend':
                echo '新增好友' . $this->message['from']['UserName'] . '请求' . PHP_EOL;
                Text::send($this->message['from']['UserName'], '客官，等你很久了！感谢跟 oop 交朋友，我是 kcloze 的贴身秘书，当你累了困惑了，可以随时呼叫我！' . PHP_EOL . '高山流水遇知音，知音不在谁堪听？焦尾声断斜阳里，寻遍人间已无');
                break;
            case 'request_friend':
                echo '新增好友' . $this->message['from']['UserName'] . '请求，自动通过' . PHP_EOL;
                $friends = vbot('friends');
                $friends->approve($this->message);
                break;
            case 'group_change':
                Text::send($this->message['from']['UserName'], '欢迎新人 ' . $this->message['invited'] . PHP_EOL . '邀请人：' . $this->message['inviter']);
                break;
            default:
                // code...
                break;

        }
    }
}
