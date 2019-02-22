<?php

namespace App\Core\Service;

final class Container extends \League\Container\Container
{
    /**
     * @var $this
     */
    private static $_instance;

    /**
     * array that represent all services in the application configuration
     *
     * @var array
     */
    private $services;

    private function __clone() {}
    private function __construct()
    {
        parent::__construct();
    }

    /**
     * @return Container
     */
    public static function getContainer()
    {
        if (!(self::$_instance instanceof self))
            self::$_instance = new self();

        return self::$_instance;
    }

    /**
     * @param string $service
     * @return mixed
     */
    public function getService(string $service)
    {
        if (!isset($this->services))
            $this->services = ConfigParser::parameters('config/services.yml');

        if ($this->has($service))
            return $this->get($service);

        if (isset($this->services[$service]))
            return $this->createService($this->services[$service], $service);

        return null;
    }

    /**
     * @param array $configuration
     * @param string $name
     * @return mixed
     */
    public function createService(array $configuration, string $name)
    {
        $def = $this->add($name, $configuration['class']);

        if (isset($configuration['parameters'])) {
            foreach ($configuration['parameters'] as $parameter) {
                $obj = $this->getService($parameter);

                $def->addArgument($obj);
            }
        }

        return $this->get($name);
    }
}
