<?php
/**
 * Class CookieConfig
 * TODO: Добавить временную метку для синхронизации конфигурации (для очистки куки при обновлении структуры классов)
 */
abstract class CookieConfig implements iCookieConfig
{
    private static $config;

    private static $instances = [];

    private static $life_time = 10800; // 3 hour

    private $domain;

    protected $config_of_sub_class = [];

    /**
     * @param $domain
     * @return CookieConfig
     */
    public static function getInstance($domain = null)
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static($domain);
        }
        return self::$instances[$class];
    }

    private function __construct($domain)
    {
        $this->domain = ($domain ? $domain : self::Server('HTTP_HOST'));
        if (is_null(self::$config)) {
            $cookie_value = self::Cookie(
                $this->getCookieConfigVarName(),
                json_encode([]));
            $cookie_value = html_entity_decode($cookie_value);
            self::$config = json_decode($cookie_value,true);
        }
        $this->config_of_sub_class = isset(self::$config[$this->getVarName()]) ? self::$config[$this->getVarName()] : [];
    }

    protected function getVarName()
    {
        $class_name = get_class($this);
        return substr($class_name, 0, -6);
    }

    private function getCookieConfigVarName()
    {
        $domain_parts = explode('.', $this->domain);
        return 'Cookie_' . $domain_parts[0];
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

    private function getExpire()
    {
        return time() + self::$life_time;
    }

    private function saveConfigToCookie()
    {
        $name = $this->getCookieConfigVarName();
        $value = json_encode(self::$config);
        $expire = $this->getExpire();
        $path = '/';
        $domain = $this->domain;
        setcookie($name, $value, $expire, $path, $domain);
    }

    private static function Cookie($key = null, $default = false)
    {
        if ($key === null) {
            return $_COOKIE;
        }
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    private static function Server($key = null, $default = null)
    {
        if ($key === null) {
            return $_SERVER;
        }
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

	public static function getSub($category, $name, $default = null) {
		$value = $default;
		$cookie_category = array();
		parse_str(html_entity_decode($_COOKIE[$category]), $cookie_category);
		if ($cookie_category && is_array($cookie_category)) {
			if (array_key_exists($name, $cookie_category)) {
				$value = $cookie_category[$name];
			}
		}
		return $value;
	}
}