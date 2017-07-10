<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) php-team@yaochufa <php-team@yaochufa.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Kcloze\Bot;

class Robots
{
    private $robot;
    private $options;

    public function __construct($options)
    {
        $this->robot   = new \Hanson\Vbot\Foundation\Vbot($options);
        $this->options =$options;
    }

    public function run()
    {
        $this->robot->messageHandler->setHandler(function ($message) {
            \Hanson\Vbot\Message\Text::send($message['from']['UserName'], 'Hi, I\'m Vbot!');
        });
        $this->robot->server->serve();
    }
}
