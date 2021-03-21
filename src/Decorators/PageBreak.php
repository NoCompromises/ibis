<?php

namespace Ibis\Decorators;

/**
 * Class PageBreak
 *
 * This registers `[break]` as a page break control.
 */
class PageBreak implements DecoratorContract
{
    /**
     * Decorates with our prefix
     * @param string $incoming
     * @return string
     */
    public function decorate(string $incoming): string
    {
        return str_replace('[break]', '<div style="page-break-after: always;"></div>', $incoming);
    }
}
