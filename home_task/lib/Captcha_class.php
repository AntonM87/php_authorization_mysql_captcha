<?php
/**
 * Created by PhpStorm.
 * User: anton
 * Date: 04.10.2018
 * Time: 22:28
 */

class Captcha_class{

    const WIDTH = 200;
    const HEIGHT = 100;
    const FONT_SIZE = 22;
    const LENGTH = 6;
    const BG_LENGTH = 130;
    const FONT = "../font/times.ttf";

    private static $signs = ['a','b','c','d','e','f','g','h','i','j'];

    static function generator(){

        session_start();

        $captcha_code = '';
        $img = imagecreatetruecolor(self::WIDTH,self::HEIGHT);
        $background = imagecolorallocate($img,255,255,255);
        imagefill($img,0,0,$background);

        for ($i = 0; $i < self::BG_LENGTH;$i++){
            $color = imagecolorallocatealpha($img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255),100);
            $sign = self::$signs[mt_rand(0,count(self::$signs)-1)];
            $font_size = mt_rand(self::FONT_SIZE-4,self::FONT_SIZE+4);
            imagettftext(
                $img,
                $font_size,
                mt_rand(-35,35),
                mt_rand(self::WIDTH*0.01,self::WIDTH*0.99),
                mt_rand(self::HEIGHT*0.01,self::HEIGHT*0.99),
                $color,
                self::FONT,
                $sign
            );
        }
        for ($i = 0; $i < self::LENGTH;$i++){
            $color = imagecolorallocatealpha($img,mt_rand(0,150),mt_rand(0,150),mt_rand(0,150),mt_rand(20,40));
            $sign = self::$signs[mt_rand(0,count(self::$signs)-1)];
            $font_size = mt_rand(self::FONT_SIZE*2-4,self::FONT_SIZE*2+4);
            $captcha_code .= $sign;
            imagettftext(
                $img,
                $font_size,
                mt_rand(-15,15),
                ($i+1)*self::FONT_SIZE + 35,
                self::HEIGHT*2/3+mt_rand(1,5),
                $color,
                self::FONT,
                $sign
            );
        }

        $_SESSION['str_code'] = $captcha_code;

        header('Content-type: image/jpeg');
        imagejpeg($img);

    }

    static function captcha_check($var){
        if (!session_id()) session_start();
        return $_SESSION['str_code'] === $var;
    }

}
