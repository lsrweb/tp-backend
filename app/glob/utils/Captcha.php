<?php

namespace app\glob\utils;

use Gregwar\Captcha\CaptchaBuilder;
use think\facade\Session;


class Captcha
{

    protected CaptchaBuilder $builder;

    public function __construct($width = 150, $height = 40, $maxFont = 30, $minFont = 25)
    {
        $this->builder = new CaptchaBuilder;
    }

    /**
     * 生成验证码 图片 base64
     * @return string
     */
    public function generate()
    {

        $font = public_path('/static/fonts/') . "Montserrat-Black.ttf";
        $this->builder->build('150', '40', $font);
        $phrase = $this->builder->getPhrase();
        Session::set('captcha', $phrase);
        return $this->builder->inline();
    }

    /**
     * 验证用户输入的验证码是否正确
     */
    public function verify($inputText)
    {
        try {
            if (empty($inputText)) {
                return false;
            }
            if ($inputText && strtolower(trim($inputText)) === strtolower(trim(Session::get('captcha')))) {
                Session::delete('captcha');
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

}