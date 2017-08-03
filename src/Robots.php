<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot;

use Hanson\Vbot\Foundation\Vbot;

class Robots
{
    private $robot;
    private $options;

    public function __construct($options)
    {
        $this->options =$options;
    }

    public function run()
    {
        $this->robot = new Vbot($this->options);
        $this->robot->messageHandler->setHandler(function ($message) {
            $reply=new Reply($message, $this->options);
            $reply->send();
        });
        $this->robot->server->serve();
    }
}
