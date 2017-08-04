<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot\Api;

use GuzzleHttp\Client;

class So
{
    const SO_IMG_URL = 'http://m.image.so.com/i';
    private $client;

    public function __construct()
    {
        $this->client  = new Client();
    }

    public function search($text)
    {
        $client = new Client();
        $res    = $client->request('GET', self::SO_IMG_URL,
            ['query'     => ['src' => 'srp'],
                    ['q' => urlencode($text)],
            ]
        );
        $res    =$res->getBody()->getContents();
        //var_dump($res);
        $content=strstr($res,'window.srcg');
        //var_dump($content);
        //preg_match_all('/(http:\/\/www.baidu.com\/link\?[\w=-]+?)"\s*target="_blank"\s*>(.*?)<\/a>\s*/', $res, $m);
        //http:\/\/p0.so.qhmsg.com\/t01ca1901b5914fc9f0.jpg
        preg_match_all('http?:\\\/\\\/.+\.(jpg|gif|png)', $content, $m);
        //preg_match_all('/(p(.*).so.qhmsg.com(.*).jpg)/i', $content, $m);
        var_dump($m);
        //var_dump($res);
        exit;
        $content=json_decode($res, true);
        $url    =isset($content['url']) ? ' ' . $content['url'] : '';

        return $content['text'] . $url;
    }
}
