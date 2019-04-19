<?php

use Hanson\Vbot\Extension\AbstractMessageHandler;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;
use Illuminate\Database\Capsule\Manager ;

class MysqlForever extends AbstractMessageHandler
{

    public $author = 'Jc91715';

    public $version = '1.0';

    public $name = 'mysql_save';

    public $zhName = '数据库';

    private static $array = [];

    public function handler(Collection $message)
    {
        $data = [];
        if ($message['type'] === 'text' && in_array($message['fromType'],['Group','Self'])&&(substr($message['from']['UserName'],0,2)=='@@')) {//群组消息记录

            $data = ['to_uid'=>$message['from']['UserName'],'to_nickname'=>$message['from']['NickName'],'send_uid'=>$message['sender']['UserName'],'send_nickname'=>$message['sender']['NickName'],'message'=>$message['content'],'memberlists'=> json_encode(array_column($message->only(['from'])->get('from')['MemberList'],'NickName','UserName')),'membercount'=>$message['from']['MemberCount'],'send_time'=>$message['time']->date];

        }
        if ($message['type'] === 'text' && in_array($message['fromType'],['Friend','Self'])&&(substr($message['from']['UserName'],0,2)!=='@@')) {//好友消息记录
            if($message['fromType']=='Friend'){
                $data = ['to_uid' => '', 'to_nickname' => '', 'send_uid' => $message['from']['UserName'], 'send_nickname' => $message['from']['NickName'], 'message' => $message['content'], 'memberlists' => '', 'send_time' => $message['time']->date];
            }
            if($message['fromType']=='Self'){
                $data = ['to_uid' => $message['from']['UserName'], 'to_nickname' =>$message['from']['NickName'], 'send_uid' =>'' , 'send_nickname' => '', 'message' => $message['content'], 'memberlists' => '', 'send_time' => $message['time']->date];
            }

        }

        if($message['type'] === 'text' ){
            $data['message'] = $message['content'];
            $data['extra'] = json_encode($message);
            Manager::table('message')->insert($data);
        }


    }

    /**
     * 注册拓展时的操作.
     */
    public function register()
    {

    }

}
