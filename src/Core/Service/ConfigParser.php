<?php

namespace App\Core\Service;

class ConfigParser
{
    public static function parameters($path)
    {
        return yaml_parse_file($path);
    }
}
