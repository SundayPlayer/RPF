<?php

namespace App\Core\Service;

class ConfigParser
{
    public static function parameters($path, $basePath = 'config/')
    {
        $config = yaml_parse_file($path);

        if (isset($config['imports']))
            foreach ($config['imports'] as $file)
                $config = array_merge($config, yaml_parse_file($basePath . $file['resource']));

        return $config;
    }
}
