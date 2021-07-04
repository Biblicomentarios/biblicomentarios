<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit737a1349a6ae123c48f464c6f6d5e1ef {
    public static $prefixLengthsPsr4 = [
        'M' => [
            'MatthiasWeb\\Utils\\Test\\' => 23,
            'MatthiasWeb\\Utils\\' => 18
        ]
    ];

    public static $prefixDirsPsr4 = [
        'MatthiasWeb\\Utils\\Test\\' => [
            0 => __DIR__ . '/../..' . '/test/phpunit'
        ],
        'MatthiasWeb\\Utils\\' => [
            0 => __DIR__ . '/../..' . '/src'
        ]
    ];

    public static $classMap = [
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php'
    ];

    public static function getInitializer(ClassLoader $loader) {
        return \Closure::bind(
            function () use ($loader) {
                $loader->prefixLengthsPsr4 = ComposerStaticInit737a1349a6ae123c48f464c6f6d5e1ef::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInit737a1349a6ae123c48f464c6f6d5e1ef::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInit737a1349a6ae123c48f464c6f6d5e1ef::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
