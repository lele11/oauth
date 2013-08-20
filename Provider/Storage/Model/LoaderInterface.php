<?php
namespace Pi\Oauth\Provider\Storage\Model;

interface LoaderInterface
{
    public static function setConfig($config);
    public static function load($identifier);
}