<?php

namespace App\Service\Security;


class PasswordGenerator implements PasswordGeneratorInterface
{
    public function generate(int $charsCount = 8): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $password = '';
        $alphabetLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $charsCount; $i++) {
            $password .= $alphabet[rand(0, $alphabetLength)];
        }
       return $password;
    }
}
