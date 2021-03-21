<?php

namespace Ibis\Commands;

use Ibis\Ibis;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var Filesystem
     */
    private $disk;

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Initialize a new project in the current directory.');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Mpdf\MpdfException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disk = new Filesystem();
        $this->output = $output;

        $currentPath = getcwd();

        $assetsPath = Ibis::assetsPath();

        if ($this->disk->isDirectory($assetsPath)) {
            $this->output->writeln('');
            $this->output->writeln('<info>Project already initialised!</info>');

            return 0;
        }

        $this->disk->makeDirectory(
            $assetsPath
        );

        $this->disk->makeDirectory(
            $assetsPath.'/fonts'
        );

        $contentPath = Ibis::contentPath();

        $this->disk->makeDirectory(
            $contentPath
        );

        $this->disk->copyDirectory(
            __DIR__.'/../../stubs/content',
            $contentPath
        );

        $this->disk->put(
            $currentPath.'/ibis.php',
            $this->disk->get(__DIR__.'/../../stubs/ibis.php')
        );

        $this->disk->put(
            $assetsPath.'/cover.jpg',
            $this->disk->get(__DIR__.'/../../stubs/assets/cover.jpg')
        );

        $this->disk->put(
            $assetsPath.'/theme-dark.html',
            $this->disk->get(__DIR__.'/../../stubs/assets/theme-dark.html')
        );

        $this->disk->put(
            $assetsPath.'/theme-light.html',
            $this->disk->get(__DIR__.'/../../stubs/assets/theme-light.html')
        );

        $this->output->writeln('');
        $this->output->writeln('<info>Done!</info>');

        return 0;
    }
}
