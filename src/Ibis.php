<?php

namespace Ibis;

use Illuminate\Support\Str;

class Ibis
{
    /**
     * @var array
     */
    public static $config;

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
     * Provides the path for ebook content
     */
    public static function contentPath()
    {
        return self::config()['content_path'];
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
     * @return array
     */
    private static function config()
    {
        if (static::$config) {
            return static::$config;
        }

        static::$config = require getcwd().'/ibis.php';

        return static::$config;
    }
}
