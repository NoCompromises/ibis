<?php

namespace Ibis\Decorators;

/**
 * Class BlockQuoteClassWithPrefix
 *
 * This finds any block quote that has a prefix that is specified and replaces it with a class and a word.
 *
 * Example:
 *
 * `>{alert} This is an alert.`
 *
 * renders partially into this:
 *
 * `<blockquote class='alert'><p><strong>Alert:</strong>`
 */
class BlockQuoteClassWithPrefix implements DecoratorContract
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
        $replace = sprintf("<blockquote class='%s'><p><strong>%s:</strong>", $this->prefix, ucfirst($this->prefix));
        return str_replace($search, $replace, $incoming);
    }
}
