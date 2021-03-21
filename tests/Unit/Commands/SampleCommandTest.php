<?php

namespace Tests\Unit\Commands;

use Smalot\PdfParser\Parser;
use PHPUnit\Framework\TestCase;
use Ibis\Commands\SampleCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SampleCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new SampleCommand());
        $command = $application->find('sample');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * Gets rid of data that we might have in the Sample mock folder.
     */
    protected function tearDown(): void
    {
        $directory = __DIR__.'/../../Mocks/Sample/export';
        $filesystem = new Filesystem();
        $filesystem->delete("{$directory}/sample-.i-am-a-title-here-light.pdf");
    }

    public function testSampleCreatedSuccessfullyWithDefaultArgument(): void
    {
        $directory = __DIR__.'/../../Mocks/Sample';
        chdir($directory);

        $this->commandTester->execute([]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        $generatedFilePath = $directory.'/export/sample-.i-am-a-title-here-light.pdf';
        self::assertFileExists($generatedFilePath);

        $this->examinePDFContent($generatedFilePath);
    }

    public function testSampleCreatedSuccessfullyWithAnArgument(): void
    {
        $directory = __DIR__.'/../../Mocks/Sample';
        chdir($directory);

        $this->commandTester->execute(['theme' => 'themery']);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        $generatedFilePath = $directory.'/export/sample-.i-am-a-title-here-themery.pdf';
        self::assertFileExists($generatedFilePath);

        $this->examinePDFContent($generatedFilePath);
    }

    protected function examinePDFContent($generatedFilePath): void
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($generatedFilePath);
        $pages = $pdf->getPages();
        self::assertCount(5, $pages);
        self::assertEquals('IAmPDFPage2', trim($pages[0]->getText()));
        self::assertEquals('IAmPDFPage3', trim($pages[1]->getText()));
        self::assertEquals('IAmPDFPage5', trim($pages[2]->getText()));
        self::assertEquals('IAmPDFPage6', trim($pages[3]->getText()));
        self::assertEquals('My Sample Notice here.', trim($pages[4]->getText()));
    }
}
