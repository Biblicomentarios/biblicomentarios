<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit06b07fd27dc5a8bb1a780460f416127c {
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
                $loader->prefixLengthsPsr4 = ComposerStaticInit06b07fd27dc5a8bb1a780460f416127c::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInit06b07fd27dc5a8bb1a780460f416127c::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInit06b07fd27dc5a8bb1a780460f416127c::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
