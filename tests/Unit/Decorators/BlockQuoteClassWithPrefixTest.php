<?php

namespace Tests\Unit\Decorators;

use PHPUnit\Framework\TestCase;
use Ibis\Decorators\BlockQuoteClassWithPrefix;

class BlockQuoteClassWithPrefixTest extends TestCase
{
    /**
     * @param string $word
     * @param string $pre
     * @param string $post
     * @dataProvider replacementsProvider
     */
    public function testReplacements(string $word, string $pre, string $post): void
    {
        $decorator = new BlockQuoteClassWithPrefix($word);
        self::assertEquals($post, $decorator->decorate($pre));
    }

    public function replacementsProvider(): array
    {
        return [
            ['', '', ''],
            ['my-word', '', ''],
            ['my-word', 'here is some content.', 'here is some content.'],
            ['my-word', 'here my-word is', 'here my-word is'],
            ['my-word', "<blockquote>\n<p>my-word</p></blockquote>", "<blockquote>\n<p>my-word</p></blockquote>"],
            ['my-word', "<blockquote>\n<p>{my-word} hello</p></blockquote>", "<blockquote class='my-word'><p><strong>My-word:</strong> hello</p></blockquote>"],
            ['here it is', "<blockquote>\n<p>{here it is} hello</p></blockquote>", "<blockquote class='here it is'><p><strong>Here it is:</strong> hello</p></blockquote>"],
            [
                'cde',
                "<blockquote>\n<p>{cde}hello</p></blockquote><blockquote>\n<p>{acdef}hello</p></blockquote>other<blockquote>\n<p>{cde}thing</p></blockquote>",
                "<blockquote class='cde'><p><strong>Cde:</strong>hello</p></blockquote><blockquote>\n<p>{acdef}hello</p></blockquote>other<blockquote class='cde'><p><strong>Cde:</strong>thing</p></blockquote>",
            ],
        ];
    }
}
