<?php

namespace Tests\Unit\Commands;

use Ibis\Commands\BuildCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class BuildCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new BuildCommand());
        $command = $application->find('build');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * Just quickly make this easier to understand that these are stubs
     */
    protected function todo(): void
    {
        self::markTestIncomplete('This test needs to be done yet.');
    }

    public function testBuildsSuccessfullyWithDefaultArgument(): void
    {
        $this->todo();
    }

    public function testBuildsSuccessfullyWithSpecifiedThemeArgument(): void
    {
        $this->todo();
    }

    public function testCreatesExportDirectoryIfMissing(): void
    {
        $this->todo();
    }

    public function testBuildFiltersOutNonMarkdownFiles(): void
    {
        $this->todo();
    }

    public function testDeepInspectionPrepareForPdfReplacesItems(): void
    {
        $this->todo();
    }

    public function testBuildAddsCoverJpgIfItExists(): void
    {
        $this->todo();
    }

    public function testBuildUsesCoverHtmlIfItExists(): void
    {
        $this->todo();
    }
}
