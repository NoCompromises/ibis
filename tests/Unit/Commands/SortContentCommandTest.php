<?php

namespace Tests\Unit\Commands;

use PHPUnit\Framework\TestCase;
use Ibis\Commands\SortContentCommand;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SortContentCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new SortContentCommand());
        $command = $application->find('content:sort');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * Gets rid of data that we might have in the ContentToSort mock folder.
     * Technically overkill because this runs both times even though its only used once,
     * but this keeps the main test method cleaner.
     */
    protected function tearDown(): void
    {
        $directory = __DIR__.'/../../Mocks/ContentToSort/content';
        $filesystem = new Filesystem();
        $filesystem->delete([
            $directory.'/001-big-headline.md',
            $directory.'/002-another-headline.md',
            $directory.'/003-i-am-a-headline.md'
        ]);
    }

    public function testNoFilesNothingIsDone(): void
    {
        $directory = __DIR__.'/../../Mocks/NoContent';
        chdir($directory);

        $this->commandTester->execute([]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        self::assertEmpty(glob($directory.'/content/*'));
    }

    public function testFilesAreSortedAndNamedSuccessfully(): void
    {
        $directory = __DIR__.'/../../Mocks/ContentToSort';
        chdir($directory);

        $file1Content = "# I am a headline\n\nContent here!";
        file_put_contents($directory.'/content/zz.zz', $file1Content);
        $file2Content = "## Another headline\n\nAnd More content!";
        file_put_contents($directory.'/content/middle-file.txt', $file2Content);
        $file3Content = "### Big headline\n\nlittle content";
        file_put_contents($directory.'/content/d.md', $file3Content);

        $this->commandTester->execute([]);

        self::assertEquals(0, $this->commandTester->getStatusCode());
        self::assertStringEqualsFile($directory.'/content/001-big-headline.md', $file3Content);
        self::assertStringEqualsFile($directory.'/content/002-another-headline.md', $file2Content);
        self::assertStringEqualsFile($directory.'/content/003-i-am-a-headline.md', $file1Content);
    }
}
