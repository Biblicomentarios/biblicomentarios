<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit84556f72d4eeafc8d1e96b7ceecc80a7 {
    public static $files = [
        '689b08b7620712b04324ecd7ed167c6b' => __DIR__ . '/..' . '/yahnis-elsts/plugin-update-checker/load-v4p10.php',
        'cd48e99dc39649f6529395d65373ee16' => __DIR__ . '/../..' . '/src/autoload.php'
    ];

    public static $prefixLengthsPsr4 = [
        'D' => [
            'DevOwl\\RealProductManagerWpClient\\Test\\' => 39,
            'DevOwl\\RealProductManagerWpClient\\' => 34
        ]
    ];

    public static $prefixDirsPsr4 = [
        'DevOwl\\RealProductManagerWpClient\\Test\\' => [
            0 => __DIR__ . '/../..' . '/test/phpunit'
        ],
        'DevOwl\\RealProductManagerWpClient\\' => [
            0 => __DIR__ . '/../..' . '/src'
        ]
    ];

    public static $classMap = [
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php'
    ];

    public static function getInitializer(ClassLoader $loader) {
        return \Closure::bind(
            function () use ($loader) {
                $loader->prefixLengthsPsr4 = ComposerStaticInit84556f72d4eeafc8d1e96b7ceecc80a7::$prefixLengthsPsr4;
                $loader->prefixDirsPsr4 = ComposerStaticInit84556f72d4eeafc8d1e96b7ceecc80a7::$prefixDirsPsr4;
                $loader->classMap = ComposerStaticInit84556f72d4eeafc8d1e96b7ceecc80a7::$classMap;
            },
            null,
            ClassLoader::class
        );
    }
}
