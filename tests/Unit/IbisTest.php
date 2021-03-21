<?php

namespace Tests\Unit;

use Ibis\Ibis;
use PHPUnit\Framework\TestCase;

class IbisTest extends TestCase
{
    protected function setUp(): void
    {
        chdir(__DIR__.'/../Mocks/Ibis');
        Ibis::$config = null; // more simple way of clearing out the static cache
    }

    public function testTitle(): void
    {
        self::assertEquals('I am a title', Ibis::title());
    }

    public function testOutputFileName(): void
    {
        self::assertEquals('i-am-a-title', Ibis::outputFileName());
    }

    public function testAuthor(): void
    {
        self::assertEquals('Authorson Nameski', Ibis::author());
    }

    public function testContentPath(): void
    {
        self::assertEquals('/tmp', Ibis::contentPath());
    }

    public function testExportPath(): void
    {
        self::assertEquals(realpath(__DIR__.'/../Mocks/Ibis'), Ibis::exportPath());
    }
}
