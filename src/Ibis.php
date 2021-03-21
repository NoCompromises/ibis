<?php

namespace Ibis;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Ibis
{
    /**
     * @var array
     */
    private static $config;

    /**
     * Provides the ebook title
     */
    public static function title()
    {
        return self::config()['title'];
    }

    /**
     * Provides the ebook author
     */
    public static function author()
    {
        return self::config()['author'];
    }

    /**
     * Provides the path for ebook assets
     */
    public static function assetsPath()
    {
        return Arr::get(self::config(), 'assets_path', getcwd().'/assets');
    }

    /**
     * Provides the path for ebook content
     */
    public static function contentPath()
    {
        return Arr::get(self::config(), 'content_path', getcwd().'/content');
    }

    /**
     * Provides the path for ebook export
     */
    public static function exportPath()
    {
        return self::config()['export_path'];
    }

    /**
     * Profiles the ebook output file name without extension
     */
    public static function outputFileName()
    {
        return Str::slug(self::config()['title']);
    }

    /**
     * Provides the sample config declaration
     */
    public static function sample()
    {
        return self::config()['sample'];
    }

    /**
     * Provides the sample notice config declaration
     */
    public static function sampleNotice()
    {
        return self::config()['sample_notice'];
    }

    /**
     * Get the configuration array
     *
     * Opt for named static methods when you can.
     * @return array
     */
    public static function config()
    {
        if (static::$config) {
            return static::$config;
        }

        $configFile = getcwd().'/ibis.php';

        if (file_exists($configFile)) {
            static::$config = require $configFile;
        }

        return static::$config;
    }

    /**
     * Resets the Ibis configuration
     * Probably only used in tests
     */
    public static function reset()
    {
        static::$config = null;
    }
}
