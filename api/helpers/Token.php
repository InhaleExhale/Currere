<?php
/**
 * Created by PhpStorm.
 * User: Ian
 * Date: 11/09/2017
 * Time: 18:55
 */

namespace Helpers;


class Token
{
    const iv = 'abcdefghabcdefgh'; /// TODO: Make this more secure...

    static function encrypt($string) {
        $secret = \Config::get('core/appSecret');
        $method = self::getCypher();

        return openssl_encrypt($string, $method, $secret, 0, self::iv);
    }

    static function decrypt($encoded) {
        $secret = \Config::get('core/appSecret');
        $method = self::getCypher();

        return openssl_decrypt($encoded, $method, $secret,0, self::iv);
    }

    static function getCypher() {
        $cyphers = openssl_get_cipher_methods();
        $method = 'AES-256-CBC';

        if(!in_array($method, $cyphers)) {
            $method = 'AES-128-CBC';
        }
        if(!in_array($method, $cyphers)) {
            throw new \Exception('No supported encryption cyphers are available.');
        }

        return $method;
    }
}