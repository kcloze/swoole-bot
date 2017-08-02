<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot\Api;

use GuzzleHttp\Client;

class Baidu
{
    private $client;

    public function __construct($text)
    {
        //$this->client  = new Client();
        $this->text    = $text;
    }

    // public function search()
    // {
    //     $client = new Client();
    //     //$url=$this->options['params']['tulingApi'];
    //     $url    ='https://image.baidu.com/search/index?tn=baiduimage&ipn=r&ct=201326592&cl=2&lm=-1&st=-1&sf=1&fmq=&pv=&ic=0&nc=1&z=&se=1&showtab=0&fb=0&width=&height=&face=0&istype=2&ie=utf-8&fm=index&pos=history&word=' . $this->text;
    //     $res    = $client->request('POST', $url);
    //     $res    =$res->getBody()->getContents();
    //     var_dump($res);
    //     exit;
    //     $content=json_decode($res, true);
    //     $url    =isset($content['url']) ? ' ' . $content['url'] : '';

    //     return $content['text'] . $url;
    // }

    public function search($word, $page=1)
    {
        $pn     = ($page - 1) * 10;
        $url    = "http://www.baidu.com/s?wd={$word}&pn={$pn}";
        $url    = str_replace(' ', '+', $url);
        $header =  [
        'Host:www.baidu.com',
        'Connection: keep-alive',
        'Referer:http://www.baidu.com',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.94 Safari/537.36',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = curl_exec($ch);
        preg_match_all('/(http:\/\/www.baidu.com\/link\?[\w=-]+?)"\s*target="_blank"\s*>(.*?)<\/a>\s*/', $content, $m);
        $arr = [];
        for ($i=0; $i < count($m[1]); $i++) {
            $title = $m[2][$i];
            $title = str_replace('<em>', '', $title);
            $title = str_replace('</em>', '', $title);
            $url   = $m[1][$i];
            $arr[] = ['title'=>$title, 'url'=>$url];
        }
        for ($i=0; $i < count($arr); $i++) {
            if (strstr($arr[$i]['title'], '<img')) {
                unset($arr[$i]);
            }
        }

        return $arr;
    }

    // public function num()
    // {
    //     $res = $this->search();
    //     preg_match_all('/结果约([,\d]+)/', $res, $m);

    //     return (int) str_replace(',', '', $m[1][0]);
    // }
}
