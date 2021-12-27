<?php

namespace App\Service\Security;

interface PasswordGeneratorInterface
{
    public function generate(int $charsCount = 8): string;
}
