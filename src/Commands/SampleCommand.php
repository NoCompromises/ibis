<?php

namespace Ibis\Commands;

use Ibis\Ibis;
use Mpdf\Mpdf;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SampleCommand extends Command
{
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
            ->setName('sample')
            ->addArgument('theme', InputArgument::OPTIONAL, 'The name of the theme', 'light')
            ->setDescription('Generate a sample.');
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

        $exportPath = Ibis::exportPath();

        $mpdf = new Mpdf();

        $fileName = Ibis::outputFileName().'-'.$input->getArgument('theme');

        $mpdf->setSourceFile($exportPath.'/'.$fileName.'.pdf');

        foreach (Ibis::sample() as $range) {
            foreach (range($range[0], $range[1]) as $page) {
                $mpdf->useTemplate(
                    $mpdf->importPage($page)
                );
                $mpdf->AddPage();
            }
        }

        $mpdf->WriteHTML('<p style="text-align: center; font-size: 16px; line-height: 40px;">'.Ibis::sampleNotice().'</p>');

        $mpdf->Output(
            $exportPath.'/sample-.'.$fileName.'.pdf'
        );

        return 0;
    }
}
