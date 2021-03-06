<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6396cdd806396ff5a632330c74e74993 {
    public static $prefixLengthsPsr4 = [
        'D' => [
            'DevOwl\\Freemium\\Test\\' => 21,
            'DevOwl\\Freemium\\' => 16
        ]
    ];

    public static $prefixDirsPsr4 = [
        'DevOwl\\Freemium\\Test\\' => [
            0 => __DIR__ . '/../..' . '/test/phpunit'
        ],
        'DevOwl\\Freemium\\' => [
            0 => __DIR__ . '/../..' . '/src'
        ]
    ];

    public static $classMap = [
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php'
    ];

    public static function getInitializer(ClassLoader $loader) {
        return \Closure::bind(
            function () use ($loader) {
                $loader->prefixLengthsPsr4 = ComposerStaticInit6396cdd806396ff5a632330c74e74993::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInit6396cdd806396ff5a632330c74e74993::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInit6396cdd806396ff5a632330c74e74993::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
