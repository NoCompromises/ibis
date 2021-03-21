<?php

namespace Ibis\Decorators;

interface DecoratorContract
{
    /**
     * This returns decorated HTML
     * @param string $incoming
     * @return string
     */
    public function decorate(string $incoming): string;
}
