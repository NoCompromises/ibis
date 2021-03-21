<?php

namespace Ibis\Decorators;

/**
 * Class BlockQuoteClass
 *
 * This finds any block quote that has a prefix that is specified and makes it the class of the blockquote
 *
 * Example:
 *
 * `>{quote} This is a quote of mine.`
 *
 * renders partially into this:
 *
 * `<blockquote class='quote'>`
 */
class BlockQuoteClass implements DecoratorContract
{
    /**
     * @var string prefix for the blockquote
     */
    protected $prefix;

    /**
     * BlockQuoteWithPrefix constructor.
     * @param string $prefix
     */
    public function __construct(string $prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Decorates with our prefix
     * @param string $incoming
     * @return string
     */
    public function decorate(string $incoming): string
    {
        $search = sprintf("<blockquote>\n<p>{%s}", $this->prefix);
        $replace = sprintf("<blockquote class='%s'><p>", $this->prefix);
        return str_replace($search, $replace, $incoming);
    }
}
