<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit84556f72d4eeafc8d1e96b7ceecc80a7 {
    private static $loader;

    public static function loadClassLoader($class) {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader() {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(
            ['ComposerAutoloaderInit84556f72d4eeafc8d1e96b7ceecc80a7', 'loadClassLoader'],
            true,
            true
        );
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(['ComposerAutoloaderInit84556f72d4eeafc8d1e96b7ceecc80a7', 'loadClassLoader']);

        $useStaticLoader =
            PHP_VERSION_ID >= 50600 &&
            !defined('HHVM_VERSION') &&
            (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require __DIR__ . '/autoload_static.php';

            call_user_func(
                \Composer\Autoload\ComposerStaticInit84556f72d4eeafc8d1e96b7ceecc80a7::getInitializer($loader)
            );
        } else {
            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        if ($useStaticLoader) {
            $includeFiles = Composer\Autoload\ComposerStaticInit84556f72d4eeafc8d1e96b7ceecc80a7::$files;
        } else {
            $includeFiles = require __DIR__ . '/autoload_files.php';
        }
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequire84556f72d4eeafc8d1e96b7ceecc80a7($fileIdentifier, $file);
        }

        return $loader;
    }
}

function composerRequire84556f72d4eeafc8d1e96b7ceecc80a7($fileIdentifier, $file) {
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        require $file;

        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
    }
}
