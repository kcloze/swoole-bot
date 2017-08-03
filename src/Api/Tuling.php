<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot\Api;

use GuzzleHttp\Client;

class Tuling
{
    private $client;

    public function __construct($options)
    {
        $this->client  = new Client();
        $this->options =$options;
    }

    public function search($text)
    {
        $client = new Client();
        $res    = $client->request('POST', $this->options['params']['tulingApi'],
        ['body' => json_encode(
            [
                'key'   => $this->options['params']['tulingKey'],
                'info'  => $text,
            ]
        )]);
        $res    =$res->getBody()->getContents();
        $content=json_decode($res, true);
        $url    =isset($content['url']) ? ' ' . $content['url'] : '';

        return $content['text'] . $url;
    }
}
