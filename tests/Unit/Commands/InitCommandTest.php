<?php

namespace Tests\Unit\Commands;

use Ibis\Commands\InitCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends TestCase
{
    public function testInitEndsEarlyIfAssetsFolderExists(): void
    {
        chdir(__DIR__.'/../../Mocks/AssetsExist');

        $application = new Application();
        $application->add(new InitCommand());
        $command = $application->find('init');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);
        self::assertStringContainsString('Project already initialised!', $commandTester->getDisplay());
        self::assertEquals(0, $commandTester->getStatusCode());
    }
}
