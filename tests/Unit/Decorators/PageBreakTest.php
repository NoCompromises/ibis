<?php

namespace Tests\Unit\Decorators;

use Ibis\Decorators\PageBreak;
use PHPUnit\Framework\TestCase;

class PageBreakTest extends TestCase
{
    /**
     * @param string $pre
     * @param string $post
     * @dataProvider replacementsProvider
     */
    public function testReplacements(string $pre, string $post): void
    {
        $decorator = new PageBreak();
        self::assertEquals($post, $decorator->decorate($pre));
    }

    public function replacementsProvider(): array
    {
        return [
            ['', ''],
            ['here is some content.', 'here is some content.'],
            ['here [breaks] something', 'here [breaks] something'],
            ['here [BREAK] IT', 'here [BREAK] IT'],
            ['<p>I am something[break]to break</p>', '<p>I am something<div style="page-break-after: always;"></div>to break</p>'],
            [
                "This is a {break} complicated \n[break]\n\n<div broken[break]more",
                "This is a {break} complicated \n<div style=\"page-break-after: always;\"></div>\n\n<div broken<div style=\"page-break-after: always;\"></div>more",
            ],
        ];
    }
}
