<?php
/**
 * Базовый класс для создания экземпляров классов конфигурации cookie
 *
 * @since 30 Sep 2009
 */

/**
 * Class CookieConfig
 * @author Alexander Demidov <dimti@bk.ru>
 * TODO: Ввести версионность конфигураций, для того, чтобы при изменение конфигурации - старая удалялясь
 */
abstract class CookieConfig
{
    private static $config;

    protected $config_of_sub_class = [];

    private static $instances = [];

    /**
     * @return CookieConfig
     */
    public static function getInstance()
    {
        if (!isset(self::$instances[__CLASS__])) {
            self::$instances[__CLASS__] = new static();
        }
        return self::$instances[__CLASS__];
    }

    private function __construct()
    {
        if (is_null(self::$config)) {
            self::$config = json_decode(
                self::Cookie(
                    $this->getVarName(get_parent_class($this)),
                    json_encode([])),
                true);
        }
        $this->config_of_sub_class = isset(self::$config[$this->getVarName()]) ? self::$config[$this->getVarName()] : [];
    }

    protected function getVarName($class_name = null)
    {
        if (is_null($class_name)) {
            $class_name = get_class($this);
        }
        return substr($class_name, 0, -6);
    }

    protected function get($param_name, $default = null)
    {
        return isset($this->config_of_sub_class[$param_name]) ? $this->config_of_sub_class[$param_name] : $default;
    }

    protected function set($param_name, $param_value)
    {
        if (is_null($param_value)) {
            unset($this->config_of_sub_class[$param_name]);
        } else {
            $this->config_of_sub_class[$param_name] = $param_value;
        }
        self::$config[$this->getVarName()] = $this->config_of_sub_class;
        $this->saveConfigToCookie();
    }

    protected function saveConfigToCookie()
    {
        setcookie($this->getVarName(get_parent_class($this)), json_encode(self::$config), null, '/');
    }

    static private function Cookie($key = null, $default = false)
    {
        if ($key === null) {
            return $_COOKIE;
        }
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }
}