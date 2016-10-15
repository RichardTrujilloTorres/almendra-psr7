<?php


namespace Almendra\Http\Interfaces;

/**
 * Environment Interface
 *
 * @package Almendra
 */
interface EnvironmentInterface
{
    public static function init(array $settings = []);

    public static function mock(array $settings = []);
}
