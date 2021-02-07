<?php

namespace App\Command;

use Imagine\Image\Box;
use Imagine\Imagick\Imagine;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImgTraitCommand extends Command
{
    protected static $defaultName = 'img:trait';

    protected function configure()
    {
        $this
            ->setDescription('Traitement de commande')
            ->addArgument('path', InputArgument::REQUIRED, 'File path')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = $input->getArgument('path');

        if ($path) {
            $io->note(sprintf('You passed an argument: %s', $path));
        }

        $imagine = new Imagine();

        list($iwidth, $iheight) = getimagesize($path);
        $ratio = $iwidth / $iheight;
        $width = 3840;
        $height = 2160;

        if ($width / $height > $ratio) {
            $width = $height * $ratio;
        } else {
            $height = $width / $ratio;
        }

        $photo = $imagine->open($path);

        $photo->resize(new Box($width, $height))->save(dirname($path) . "/resized.jpeg");

        $optimizerChain = OptimizerChainFactory::create();

        $optimizerChain->optimize($path, dirname($path) . "/optimized.jpeg");
        $optimizerChain->optimize(dirname($path) . "/resized.jpeg", dirname($path) . "/rOptimized.jpeg");

        return Command::SUCCESS;
    }
}
