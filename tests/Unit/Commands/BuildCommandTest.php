<?php

namespace Tests\Unit\Commands;

use Ibis\Ibis;
use Smalot\PdfParser\Parser;
use Ibis\Commands\BuildCommand;
use PHPUnit\Framework\TestCase;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class BuildCommandTest
 *
 * This class does its best at testing the PDF generation process. It can be pretty difficult
 * to actually test all of the PDF properties, though. That doesn't mean we shouldn't, just that
 * it may take more time to build up this test with more detailed tests.
 */
class BuildCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * Ran on MOST of the methods, so leaving this in setup. If more methods are added,
     * maybe move this to a protected method for just those methods that need it.
     */
    protected function setUp(): void
    {
        Ibis::reset();

        $application = new Application();
        $application->add(new BuildCommand());
        $command = $application->find('build');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * Gets rid of data that we might have in the Sample mock folder.
     */
    protected function tearDown(): void
    {
        $filesystem = new Filesystem();

        $fullBuildDirectory = __DIR__.'/../../Mocks/FullBuild';
        $filesystem->delete("{$fullBuildDirectory}/export/my-title-here-light.pdf");
        $filesystem->delete("{$fullBuildDirectory}/export/my-title-here-another.pdf");

        $buildAddsDirectory = __DIR__.'/../../Mocks/BuildAddsExportDirectory';
        $filesystem->deleteDirectory("{$buildAddsDirectory}/export");

        $coverJPgDirectory = __DIR__.'/../../Mocks/BuildCoverJpg';
        $filesystem->deleteDirectory("{$coverJPgDirectory}/export");

        $coverHtmlDirectory = __DIR__.'/../../Mocks/BuildCoverHtml';
        $filesystem->deleteDirectory("{$coverHtmlDirectory}/export");
    }

    public function testBuildsSuccessfullyWithDefaultArgument(): void
    {
        $directory = __DIR__.'/../../Mocks/FullBuild';
        chdir($directory);

        $this->commandTester->execute([]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        $commandOutputArray = explode("\n", $this->commandTester->getDisplay());
        self::assertEquals([
            '==> Preparing Export Directory ...',
            '==> Parsing Markdown ...',
            '==> No assets/cover.jpg File Found. Skipping ...',
            '==> Building PDF ...',
            '==> Writing PDF To Disk ...',
            '',
            '✨✨ 5 PDF pages ✨✨', // tests that ignored txt file is ignored
            '',
            'Book Built Successfully!',
            '',
        ], $commandOutputArray);

        $exportedFilePath = $directory.'/export/my-title-here-light.pdf';
        self::assertFileExists($exportedFilePath);

        $this->examinePDFContentFullBuild($exportedFilePath);
    }

    public function testBuildsSuccessfullyWithSpecifiedThemeArgument(): void
    {
        $directory = __DIR__.'/../../Mocks/FullBuild';
        chdir($directory);

        $this->commandTester->execute([
            'theme' => 'another',
        ]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        $commandOutputArray = explode("\n", $this->commandTester->getDisplay());
        self::assertEquals([
            '==> Preparing Export Directory ...',
            '==> Parsing Markdown ...',
            '==> No assets/cover.jpg File Found. Skipping ...',
            '==> Building PDF ...',
            '==> Writing PDF To Disk ...',
            '',
            '✨✨ 5 PDF pages ✨✨',
            '',
            'Book Built Successfully!',
            '',
        ], $commandOutputArray);

        $exportedFilePath = $directory.'/export/my-title-here-another.pdf';
        self::assertFileExists($exportedFilePath);

        $this->examinePDFContentFullBuild($exportedFilePath);
    }

    /**
     * For the two methods above, this is the same test methodology
     * @param $exportedFilePath
     * @throws \Exception
     */
    protected function examinePDFContentFullBuild($exportedFilePath): void
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($exportedFilePath);

        $details = $pdf->getDetails();
        self::assertEquals('My Title Here', $details['Title']);
        self::assertEquals('Mister Authorson', $details['Author']);
        self::assertEquals('Mister Authorson', $details['Creator']);

        $pages = $pdf->getPages();
        self::assertCount(6, $pages);

        $page0Array = $pages[0]->getTextArray();
        self::assertCount(17, $page0Array);
        self::assertEquals('Table of Contents', $page0Array[0]);
        self::assertEquals('Chapter 1', $page0Array[1]);
        self::assertEquals('2nd Level', $page0Array[5]);
        self::assertEquals('Second chapter', $page0Array[9]);
        self::assertEquals('Last Chapter', $page0Array[13]);

        $page1Array = $pages[1]->getTextArray();
        self::assertCount(3, $page1Array);
        self::assertEquals('2', $page1Array[0]);
        self::assertEquals('Chapter 1', $page1Array[1]);
        self::assertEquals('Here is some of chapter 1.', $page1Array[2]);

        $page2Array = $pages[2]->getTextArray();
        self::assertCount(3, $page2Array);
        self::assertEquals('3', $page2Array[0]);
        self::assertEquals('2nd Level', $page2Array[1]);
        self::assertEquals('Here is more of chapter 1.', $page2Array[2]);

        $page3Array = $pages[3]->getTextArray();
        self::assertCount(3, $page3Array);
        self::assertEquals('4', $page3Array[0]);
        self::assertEquals('Third level', $page3Array[1]);
        self::assertEquals('Here is more content of chapter 1.', $page3Array[2]);

        $page4Array = $pages[4]->getTextArray();
        self::assertCount(4, $page4Array);
        self::assertEquals('5', $page4Array[0]);
        self::assertEquals('Second chapter', $page4Array[1]);
        self::assertEquals('Here is the content of chapter 2.', $page4Array[2]);
        self::assertEquals('And some more.', $page4Array[3]);

        $page5Array = $pages[5]->getTextArray();
        self::assertCount(3, $page5Array);
        self::assertEquals('6', $page5Array[0]);
        self::assertEquals('Last Chapter', $page5Array[1]);
        self::assertEquals('This is the last one.', $page5Array[2]);
    }

    public function testCreatesExportDirectoryIfMissing(): void
    {
        $directory = __DIR__.'/../../Mocks/BuildAddsExportDirectory';
        chdir($directory);

        try {
            $this->commandTester->execute([]);
        } catch (FileNotFoundException $e) {
            self::assertDirectoryExists($directory.'/export');
        } catch (\Exception $e) {
            self::fail(get_class($e).' thrown');
        }
    }

    public function testBuildAddsCoverJpgIfItExists(): void
    {
        $directory = __DIR__.'/../../Mocks/BuildCoverJpg';
        chdir($directory);

        $this->commandTester->execute([]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        $commandOutputArray = explode("\n", $this->commandTester->getDisplay());

        self::assertEquals([
            '==> Preparing Export Directory ...',
            '==> Parsing Markdown ...',
            '==> Adding Book Cover ...',
            '==> Building PDF ...',
            '==> Writing PDF To Disk ...',
            '',
            '✨✨ 2 PDF pages ✨✨',
            '',
            'Book Built Successfully!',
            '',
        ], $commandOutputArray);

        $exportedFilePath = $directory.'/export/the-book-title-light.pdf';
        self::assertFileExists($exportedFilePath);

        $parser = new Parser();
        $pdf = $parser->parseFile($exportedFilePath);

        $details = $pdf->getDetails();
        self::assertEquals('The Book Title', $details['Title']);
        self::assertEquals('Author Person', $details['Author']);
        self::assertEquals('Author Person', $details['Creator']);

        $pages = $pdf->getPages();
        self::assertCount(3, $pages);

        $page0Array = $pages[0]->getTextArray();
        self::assertCount(1, $page0Array);
        self::assertEquals('', $page0Array[0]);

        $page1Array = $pages[1]->getTextArray();
        self::assertCount(5, $page1Array);
        self::assertEquals('Table of Contents', $page1Array[0]);
        self::assertEquals('Chapter 1 Content', $page1Array[1]);
        self::assertEquals('3', $page1Array[3]);

        $page2Array = $pages[2]->getTextArray();
        self::assertCount(3, $page2Array);
        self::assertEquals('3', $page2Array[0]);
        self::assertEquals('Chapter 1 Content', $page2Array[1]);
        self::assertEquals('I am content.', $page2Array[2]);
    }

    public function testBuildUsesCoverHtmlIfItExists(): void
    {
        $directory = __DIR__.'/../../Mocks/BuildCoverHtml';
        chdir($directory);

        $this->commandTester->execute([]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        $commandOutputArray = explode("\n", $this->commandTester->getDisplay());

        self::assertEquals([
            '==> Preparing Export Directory ...',
            '==> Parsing Markdown ...',
            '==> Adding Book Cover ...',
            '==> Building PDF ...',
            '==> Writing PDF To Disk ...',
            '',
            '✨✨ 2 PDF pages ✨✨',
            '',
            'Book Built Successfully!',
            '',
        ], $commandOutputArray);

        $exportedFilePath = $directory.'/export/title-of-the-book-light.pdf';
        self::assertFileExists($exportedFilePath);

        $parser = new Parser();
        $pdf = $parser->parseFile($exportedFilePath);

        $details = $pdf->getDetails();
        self::assertEquals('Title of the Book', $details['Title']);
        self::assertEquals('Author of the Book', $details['Author']);
        self::assertEquals('Author of the Book', $details['Creator']);

        $pages = $pdf->getPages();
        self::assertCount(3, $pages);

        $page0Array = $pages[0]->getTextArray();
        self::assertCount(2, $page0Array);
        self::assertEquals('I am a cover line', $page0Array[0]);
        self::assertEquals('Another cover LINE', $page0Array[1]);

        // this section demonstrates a bug of parsing html data from the front cover
        $page1Array = $pages[1]->getTextArray();
        self::assertCount(9, $page1Array);
        self::assertEquals('Table of Contents', $page1Array[0]);
        self::assertEquals('I am a cover line', $page1Array[1]);
        self::assertEquals('1', $page1Array[3]);
        self::assertEquals('Chapter 1 Content', $page1Array[5]);
        self::assertEquals('3', $page1Array[7]);

        $page2Array = $pages[2]->getTextArray();
        self::assertCount(3, $page2Array);
        self::assertEquals('3', $page2Array[0]);
        self::assertEquals('Chapter 1 Content', $page2Array[1]);
        self::assertEquals('I am content.', $page2Array[2]);
    }

    public function testDeepInspectionPrepareForPdfReplacesStandardItems(): void
    {
        $directory = __DIR__.'/../../Data/PrepareForPdf';
        $source = file_get_contents($directory.'/standard-replace-source.html');

        $result = $this->getPrepareForPdf($source, 1);

        self::assertStringEqualsFile($directory.'/standard-replace-prepared.html', $result);
    }

    public function testDeepInspectionPrepareForPdfBreaksH1IfFileGreaterThanOne(): void
    {
        $directory = __DIR__.'/../../Data/PrepareForPdf';
        $source = file_get_contents($directory.'/h1-break-source.html');

        $result = $this->getPrepareForPdf($source, 2);

        self::assertStringEqualsFile($directory.'/h1-break-prepared.html', $result);
    }

    /**
     * Normally you don't want to do something like this. However, since these are hard-coded
     * in the command and not something like decorators, this is just a faster/easier
     * way to test this functionality.  In the future hopefully these are refactored out into
     * decorators and then this type of testing is not a better solution.
     *
     * @param $html
     * @param $file
     * @return string
     * @throws \ReflectionException
     */
    protected function getPrepareForPdf($html, $file): string
    {
        $buildCommand = new BuildCommand();
        $class = new \ReflectionClass($buildCommand);
        $method = $class->getMethod('prepareForPdf');
        $method->setAccessible(true);
        return $method->invokeArgs($buildCommand, [$html, $file]);
    }
}
