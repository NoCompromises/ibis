<?php

namespace Tests\Unit\Commands;

use Ibis\Commands\InitCommand;
use PHPUnit\Framework\TestCase;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class InitCommandTest
 *
 * Note about this test: Because the current code is using current working directory, we're unable
 * to mock out filesystem data with something like vfsStream.  This is why we have to make actual
 * filesystem changes.
 */
class InitCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new InitCommand());
        $command = $application->find('init');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * Gets rid of data that we might have in the AssertNotExists mock folder.
     * Technically overkill because this runs both times even though its only used once,
     * but this keeps the main test method cleaner.
     */
    protected function tearDown(): void
    {
        $directory = __DIR__.'/../../Mocks/AssetsNotExist';
        $filesystem = new Filesystem();
        $filesystem->deleteDirectories($directory);
        $filesystem->delete("{$directory}/ibis.php");
    }

    public function testInitEndsEarlyIfAssetsFolderExists(): void
    {
        chdir(__DIR__.'/../../Mocks/AssetsExist');

        $this->commandTester->execute([]);

        self::assertStringContainsString('Project already initialised!', $this->commandTester->getDisplay());
        self::assertEquals(0, $this->commandTester->getStatusCode());
    }

    public function testInitWritesFilesSuccessfully(): void
    {
        $directory = __DIR__.'/../../Mocks/AssetsNotExist';
        chdir($directory);

        $this->commandTester->execute([]);

        self::assertStringContainsString('Done!', $this->commandTester->getDisplay());
        self::assertEquals(0, $this->commandTester->getStatusCode());

        $stubsDirectory = __DIR__.'/../../../stubs';

        self::assertFileExists($directory.'/assets');
        self::assertFileEquals($stubsDirectory.'/assets/cover.jpg', $directory.'/assets/cover.jpg');
        self::assertFileEquals($stubsDirectory.'/assets/theme-dark.html', $directory.'/assets/theme-dark.html');
        self::assertFileEquals($stubsDirectory.'/assets/theme-light.html', $directory.'/assets/theme-light.html');
        self::assertFileDoesNotExist($directory.'/assets/cover.html'); // this is not copied over in the command

        self::assertFileExists($directory.'/assets/fonts');

        self::assertFileExists($directory.'/content');
        foreach (glob($stubsDirectory.'/content/*') as $file) {
            self::assertFileEquals($file, $directory.'/content/'.basename($file));
        }

        self::assertFileExists($directory.'/ibis.php');
    }
}
