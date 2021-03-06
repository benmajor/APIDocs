<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita003e30a76c0297b948ccaf5a70d516b
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'd767e4fc2dc52fe66584ab8c6684783e' => __DIR__ . '/..' . '/adbario/php-dot-notation/src/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Webuni\\FrontMatter\\' => 19,
        ),
        'T' => 
        array (
            'Twig\\Extra\\Markdown\\' => 20,
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
        ),
        'M' => 
        array (
            'Minwork\\Helper\\' => 15,
        ),
        'A' => 
        array (
            'Adbar\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Webuni\\FrontMatter\\' => 
        array (
            0 => __DIR__ . '/..' . '/webuni/front-matter/src',
        ),
        'Twig\\Extra\\Markdown\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/markdown-extra/src',
        ),
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Minwork\\Helper\\' => 
        array (
            0 => __DIR__ . '/..' . '/minwork/array/src',
        ),
        'Adbar\\' => 
        array (
            0 => __DIR__ . '/..' . '/adbario/php-dot-notation/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'ParsedownExtra' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown-extra',
            ),
            'Parsedown' => 
            array (
                0 => __DIR__ . '/..' . '/erusev/parsedown',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita003e30a76c0297b948ccaf5a70d516b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita003e30a76c0297b948ccaf5a70d516b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInita003e30a76c0297b948ccaf5a70d516b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
