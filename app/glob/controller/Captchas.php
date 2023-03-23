<?php

namespace app\glob\controller;
use app\glob\utils\Captcha;

class Captchas
{

    public function generate()
    {
        $captcha = new Captcha();
        return json($captcha->generate());
    }
}