<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot;

use GuzzleHttp\Client;
use Hanson\Vbot\Message\Text;

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
        switch ($type) {
            case 'text':
                //isAt
                if (true == $this->message['isAt']) {
                    $return=$this->getTulingBot();
                    Text::send($this->message['from']['UserName'], $return);
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
            case 'request_friend':
                // code...
                break;
            case 'group_change':
                // code...
                break;

            default:
                // code...
                break;
        }
    }

    private function getTulingBot()
    {
        $client = new Client();
        $str    =$this->message['pure'];
        $res    = $client->request('POST', $this->options['params']['tulingApi'],
        ['body' => json_encode(
            [
                'key'   => $this->options['params']['tulingKey'],
                'info'  => $str,
            ]
        )]);
        $res    =$res->getBody()->getContents();
        $content=json_decode($res, true);
        $url    =isset($content['url']) ? ' ' . $content['url'] : '';

        return $content['text'] . $url;
    }
}
