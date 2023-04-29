<?php

namespace App\Services;

class UrlValidator
{
    public function validateUrl($url): bool
    {
        if (empty($url)) {
            return false;
        }
        //url para estar correctamente validada debe llevar https://, en caso de no llevarlo, dará error
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }
}
