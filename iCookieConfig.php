<?php

interface iCookieConfig
{
    /**
     * @return OrderSessionCookie
     */
    public static function getInstance();
}