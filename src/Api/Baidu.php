<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot\Api;

class Baidu
{
    private $client;

    public function search($word, $page=1)
    {
        $pn     = ($page - 1) * 5;
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
