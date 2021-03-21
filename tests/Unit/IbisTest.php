<?php

namespace Tests\Unit;

use Ibis\Ibis;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;

class IbisTest extends TestCase
{
    protected function setUp(): void
    {
        Ibis::reset();

        chdir(__DIR__.'/../Mocks/Ibis');
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

    public function testAssetsPath(): void
    {
        self::assertEquals('/assets-here', Ibis::assetsPath());
    }

    public function testAssetsPathNoConfigFile(): void
    {
        chdir(__DIR__);
        self::assertEquals(__DIR__.'/assets', Ibis::assetsPath());
    }

    public function testContentPath(): void
    {
        self::assertEquals('/tmp', Ibis::contentPath());
    }

    public function testContentPathNoConfigFile(): void
    {
        chdir(__DIR__);
        self::assertEquals(__DIR__.'/content', Ibis::contentPath());
    }

    public function testExportPath(): void
    {
        self::assertEquals(realpath(__DIR__.'/../Mocks/Ibis'), Ibis::exportPath());
    }

    public function testSample(): void
    {
        self::assertEquals([[1, 2], [3, 4]], Ibis::sample());
    }

    public function testSampleNotice(): void
    {
        self::assertEquals('This is a sample notice.', Ibis::sampleNotice());
    }

    public function testConfig(): void
    {
        $config = Ibis::config();

        self::assertEquals([
            'title' => 'I am a title',
            'author' => 'Authorson Nameski',
            'content_path' => '/tmp',
            'assets_path' => '/assets-here',
            'sample' => [
                [1, 2],
                [3, 4],
            ],
            'sample_notice' => 'This is a sample notice.',
        ], Arr::except($config, 'export_path'));

        self::assertEquals(getcwd(), $config['export_path']);
    }
}
