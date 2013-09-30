<?php

class OrderSessionCookie extends CookieConfig
{
    const SESSION_CURRENT = 0;

    public function getSessionCurrent($default = null)
    {
        return $this->get(self::SESSION_CURRENT, $default);
    }

    public function setSessionCurrent($value)
    {
        $this->set(self::SESSION_CURRENT, $value);
    }

    public function removeSessionCurrent()
    {
        $this->set(self::SESSION_CURRENT, null);
    }
}