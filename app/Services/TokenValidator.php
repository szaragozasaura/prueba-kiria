<?php

namespace App\Services;

class TokenValidator
{
    public function validateToken($token): bool
    {
        $stack = [];

        for ($i = 0; $i < strlen($token); $i++) {
            $char = $token[$i];

            if ($char === '{' || $char === '[' || $char === '(') {
                $stack[] = $char;
            } elseif ($char === '}' || $char === ']' || $char === ')') {
                $last = array_pop($stack);

                if (($char === '}' && $last !== '{') ||
                    ($char === ']' && $last !== '[') ||
                    ($char === ')' && $last !== '(')) {
                    return false;
                }
            }
        }

        return count($stack) === 0;
    }
}
