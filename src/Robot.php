<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot;

use Hanson\Vbot\Collections\Group;
use Hanson\Vbot\Core\Http;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\GroupChange;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Location;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Mina;
use Hanson\Vbot\Message\Entity\NewFriend;
use Hanson\Vbot\Message\Entity\Official;
use Hanson\Vbot\Message\Entity\Recall;
use Hanson\Vbot\Message\Entity\Recommend;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Message\Entity\RequestFriend;
use Hanson\Vbot\Message\Entity\Share;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Touch;
use Hanson\Vbot\Message\Entity\Transfer;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Message\Entity\Voice;

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

class Robot
{
    private $robot;
    private $config;
    private $path;

    public function __construct($config)
    {
        $this->robot = new Vbot([
                    'tmp'   => $config['logPath'],
                    'debug' => $config['debug'],
                ]);
        $this->config =$config;
        $this->path   =$config['logPath'];
    }

    // 图灵自动回复
    public function reply($str)
    {
        $result=Http::getInstance()->post($this->config['params']['tulingApi'], [
            'key'  => $this->config['params']['tulingKey'],
            'info' => $str,
        ], true)['text'];
        //记录日志
        $this->log($result);

        return $result;
    }

    // 设置管理员
    public function isAdmin($message)
    {
        $adminAlias = $this->config['params']['adminAlias'];

        if (in_array($message->fromType, ['Contact', 'Group'], true)) {
            if ($message->fromType === 'Contact') {
                return $message->from['Alias'] === $adminAlias;
            }

            return isset($message->sender['Alias']) && $message->sender['Alias'] === $adminAlias;
        }

        return false;
    }

    public function run()
    {
        $groupMap = [
            [
                'nickname' => $this->config['params']['nickname'],
                'id'       => 1,
            ],
        ];

        $this->robot->server->setOnceHandler(function () use ($groupMap) {
            Group::getInstance()->each(function ($group, $key) use ($groupMap) {
                foreach ($groupMap as $map) {
                    if ($group['NickName'] === $map['nickname']) {
                        $group['id'] = $map['id'];
                        $groupMap[$key] = $map['id'];
                        Group::getInstance()->setMap($key, $map['id']);
                    }
                }

                return $group;
            });
        });
        $path=$this->path;
        $this->robot->server->setMessageHandler(function ($message) use ($path) {
            /** @var $message Message */
                // 位置信息 返回位置文字
                if ($message instanceof Location) {
                    /* @var $message Location */
                    Text::send('地图链接：' . $message->from['UserName'], $message->url);

                    return '位置：' . $message;
                }

                // 文字信息
                if ($message instanceof Text) {
                    /** @var $message Text */
                    if (str_contains($message->content, 'vbot') && !$message->isAt) {
                        return '你好，我叫bot机器人，欢迎欢迎！';
                    }

                    // 联系人自动回复
                    if ($message->fromType === 'Contact') {
                        if ($message->content === '拉我') {
                            $username = Group::getInstance()->getUsernameById(1);

                            Group::getInstance()->addMember($username, $message->from['UserName']);
                        }

                        if ($message->content === '测试') {
                            $username = Group::getInstance()->getUsernameById(1);
                            print_r($username);
                            print_r(Group::getInstance()->get($username));
                        }
                        $this->log('UserName: ' . $message->from['UserName']);

                        return $this->reply($message->content);
                        // 群组@我回复
                    } elseif ($message->fromType === 'Group') {
                        if (str_contains($message->content, '设置群名称') && $this->isAdmin($message)) {
                            Group::getInstance()->setGroupName($message->from['UserName'], str_replace('设置群名称', '', $message->content));
                        }

                        if (str_contains($message->content, '搜人') && $this->isAdmin($message)) {
                            $nickname = str_replace('搜人', '', $message->content);
                            $members = Group::getInstance()->getMembersByNickname($message->from['UserName'], $nickname, true);
                            $result = '搜索结果 数量：' . count($members) . "\n";
                            foreach ($members as $member) {
                                $result .= $member['NickName'] . ' ' . $member['UserName'] . "\n";
                            }

                            return $result;
                        }

                        if (str_contains($message->content, '踢人') && $this->isAdmin($message)) {
                            $username = str_replace('踢人', '', $message->content);
                            Group::getInstance()->deleteMember($message->from['UserName'], $username);
                        }

                        if (str_contains($message->content, '踢我') && $message->isAt) {
                            Text::send($message->from['UserName'], '拜拜 ' . $message->sender['NickName']);
                            Group::getInstance()->deleteMember($message->from['UserName'], $message->sender['UserName']);

                            return 'vbot 从未见过这么犯贱的人';
                        }
                        $this->log('isAt: ' . (bool) $message->isAt);
                        $this->log('content: ' . (bool) $message->content);

                        if ($message->isAt) {
                            $this->log('NickName: ' . $message->from['NickName']);

                            return $this->reply($message->content);
                        }
                    }
                }

            // 图片信息 返回接收到的图片
            if ($message instanceof Image) {
                //        return $message;
            }

            // 视频信息 返回接收到的视频
            if ($message instanceof Video) {
                //        return $message;
            }

            // 表情信息 返回接收到的表情
            if ($message instanceof Emoticon) {
                Emoticon::sendRandom($message->from['UserName']);
            }

            // 语音消息
            if ($message instanceof Voice) {
                /* @var $message Voice */
                //return '收到一条语音并下载在' . $message::getPath($message::$folder) . "/{$message->msg['MsgId']}.mp3";
            }

            // 撤回信息
            if ($message instanceof Recall && $message->msg['FromUserName'] !== myself()->username) {
                /** @var $message Recall */
                if ($message->origin instanceof Image) {
                    Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一张照片");
                    Image::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
                } elseif ($message->origin instanceof Emoticon) {
                    Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个表情");
                    Emoticon::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
                } elseif ($message->origin instanceof Video) {
                    Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个视频");
                    Video::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
                } elseif ($message->origin instanceof Voice) {
                    Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条语音");
                } else {
                    Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条信息 \"{$message->origin->msg['Content']}\"");
                }
            }

            // 红包信息
            if ($message instanceof RedPacket) {
                // do something to notify if you want ...
                return $message->content . ' 来自 ' . $message->from['NickName'];
            }

            // 转账信息
            if ($message instanceof Transfer) {
                /* @var $message Transfer */
                return $message->content . ' 收到金额 ' . $message->fee;
            }

            // 推荐名片信息
            if ($message instanceof Recommend) {
                /** @var $message Recommend */
                if ($message->isOfficial) {
                    return $message->from['NickName'] . ' 向你推荐了公众号 ' . $message->province . $message->city .
                    " {$message->info['NickName']} 公众号信息： {$message->description}";
                }

                return $message->from['NickName'] . ' 向你推荐了 ' . $message->province . $message->city .
                    " {$message->info['NickName']} 头像链接： {$message->bigAvatar}";
            }

            // 请求添加信息
            if ($message instanceof RequestFriend) {
                /** @var $message RequestFriend */
                $groupUsername = Group::getInstance()->getGroupsByNickname($this->config['params']['nickname'], true)->first()['UserName'];

                Text::send($groupUsername, "{$message->info['NickName']} 请求添加好友 \"{$message->info['Content']}\"");

                if ($message->info['Content'] === $this->config['params']['cipher']) {
                    Text::send($groupUsername, '暗号正确');
                    $message->verifyUser($message::VIA);
                } else {
                    Text::send($groupUsername, '暗号错误');
                }
            }

            // 分享信息
            if ($message instanceof Share) {
                /** @var $message Share */
                $reply = "收到分享\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}";
                if ($message->app) {
                    $reply .= "\n来源APP：{$message->app}";
                }

                return $reply;
            }

            // 分享小程序信息
            if ($message instanceof Mina) {
                /** @var $message Mina */
                $reply = "收到小程序\n小程序名词：{$message->title}\n链接：{$message->url}";

                return $reply;
            }

            // 公众号推送信息
            if ($message instanceof Official) {
                /** @var $message Official */
                $reply = "收到公众号推送\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}\n来源公众号名称：{$message->app}";

                return $reply;
            }

            // 手机点击聊天事件
            if ($message instanceof Touch) {
                //        Text::send($message->msg['ToUserName'], "我点击了此聊天");
            }

            // 新增好友
            if ($message instanceof NewFriend) {
                \Hanson\Vbot\Support\Console::debug('新加好友：' . $message->from['NickName']);
                Text::send($message->from['UserName'], '客官，等你很久了！感谢跟 vbot 交朋友，如果可以帮我点个star，谢谢了！https://github.com/HanSon/vbot');
                Group::getInstance()->addMember(Group::getInstance()->getUsernameById(1), $message->from['UserName']);

                return '现在拉你进去vbot的测试群，进去后为了避免轰炸记得设置免骚扰哦！如果被不小心踢出群，跟我说声“拉我”我就会拉你进群的了。';
            }

            // 群组变动
            if ($message instanceof GroupChange) {
                /** @var $message GroupChange */
                if ($message->action === 'ADD') {
                    $this->log('新人进群');

                    return '欢迎新人 ' . $message->nickname;
                } elseif ($message->action === 'REMOVE') {
                    $this->log('群主踢人了');

                    return $message->content;
                } elseif ($message->action === 'RENAME') {
                    //            $this->log($message->from['NickName'] . ' 改名为 ' . $message->rename);
                    if (Group::getInstance()->getUsernameById(1) === $message->from['UserName'] && $message->rename !== 'vbot 测试群') {
                        Group::getInstance()->setGroupName($message->from['UserName'], 'vbot 测试群');

                        return '行不改名,坐不改姓！';
                    }
                } elseif ($message->action === 'BE_REMOVE') {
                    $this->log('你被踢出了群 ' . $message->group['NickName']);
                } elseif ($message->action === 'INVITE') {
                    $this->log('你被邀请进群 ' . $message->from['NickName']);
                }
            }

            return false;
        });

        $this->robot->server->setExitHandler(function () {
            $this->log('其他设备登录');
        });

        $this->robot->server->setExceptionHandler(function () {
            $this->log('异常退出');
        });

        $this->robot->server->run();
    }

    public function log($info)
    {
        file_put_contents($this->config['logPath'] . '/reply.log', date('Y-m-d H:i:s') . ' ' . $info . PHP_EOL, FILE_APPEND);
    }
}
