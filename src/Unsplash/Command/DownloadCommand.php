<?php
/*
 * Download photos from unsplash.com.
 *
 * (c) Hasse Ramlev Hansen <hasse@ramlev.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unsplash\Command;

use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DownloadCommand
 * @package Unsplash\Command
 */
class DownloadCommand extends Command {

    /**
     * @var string
     */
    private $url = 'https://unsplash.com/filter?utf8=✓&search[keyword]=&scope[featured]=0&category[2]=0&category[3]=0&category[4]=0&category[8]=0&category[6]=0&category[7]=0';

    /**
     * {@inheritdoc}
     */
    protected function configure() {
        $this->setName("download")
            ->setDescription("Download photos from Unsplash.com")
            ->setDefinition([
                new InputArgument('path', InputArgument::REQUIRED, 'Path - where photos is saved on your filesystem.'),

                new InputOption('all', null, InputOption::VALUE_NONE, 'Download everything, overwrite existing photos. (Overrule pages option)'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'Photo format [jpg, png]', 'jpg'),
                new InputOption('overwrite', null, InputOption::VALUE_NONE, 'Overwrite existing files.'),
                new InputOption('page', null, InputOption::VALUE_REQUIRED, 'Page no.', 1),
                new InputOption('pages', null, InputOption::VALUE_REQUIRED, 'How many pages to download', 1),
                new InputOption('quality', null, InputOption::VALUE_REQUIRED, 'Photo quality', 75),
                new InputOption('update-library', null, InputOption::VALUE_NONE, 'Quit script when first existing photo is found.'),
                new InputOption('width', null, InputOption::VALUE_REQUIRED, 'Download photo width [800, 1080, full]', 800),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $client = new Client();
        $counter = 1;

        // Reset page and pager options if 'all' option is set.
        if ($input->getOption('all')) {
            $input->setOption('page', 1);
            $input->setOption('pages', 1);
        }

        $page = $input->getOption('page');
        $pages = $input->getOption('pages');

        $output->writeln(PHP_EOL . '<comment>Downloading photos from Unsplash.com</comment>');
        $progressBar = new ProgressBar($output);

        // Iterate until break.
        while (true) {
            $crawler = $client->request('GET', $this->url . '&page=' . $page);
            if (200 == $client->getResponse()->getStatus()) {

                $filter = $crawler->filter('.photo-grid .photo-container img');
                if ($filter->count()) {
                    try {
                        $filter->each(function ($node) use ($input, $output, $progressBar) {
                            $srcUrl = parse_url($node->attr('src'));
                            $scheme = array_shift($srcUrl);
                            array_pop($srcUrl);

                            $queryString = [
                                'q' => $input->getOption('quality'),
                                'fm' => $input->getOption('format'),
                            ];

                            if (is_int($input->getOption('width'))) {
                                $queryString['w'] = $input->getOption('width');
                            }

                            $file = $scheme . '://' . $srcUrl['host'] . $srcUrl['path'] . '?' . http_build_query($queryString);

                            if ($input->getOption('update-library') && file_exists($input->getArgument('path') . $srcUrl['path'] . '.' . $input->getOption('format'))) {
                                $output->writeln(PHP_EOL . PHP_EOL . sprintf('<comment>Library updated (%d new photos added)</comment>', $progressBar->getProgress()));
                                throw new \Exception('damn');
                            }

                            $this->saveFile($file, $input, $output);
                            $progressBar->advance();
                        });
                    } catch (\Exception $e) {
                        return;
                    }

                    if (!$input->getOption('all') && ($counter == $pages)) {
                        break;
                    }

                    ++$page;
                    ++$counter;
                }
                else {
                    break;
                }
            }
        }
        $progressBar->finish();
    }

    /**
     * Create and save the file to filesystem.
     *
     * @param string $url
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    private function saveFile($url = null, InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $fileInfo = parse_url($url);
        $photoName = $fileInfo['path'];
        $fileName = $path . $photoName . '.' . $input->getOption('format');

        $fs = new Filesystem();
        // Create the directory, if possible.
        if (!$fs->exists($path)) {
            $fs->mkdir($path);
        }

        // If we want to overwrite and file exists, remove it.
        if ($fs->exists($fileName) && $input->getOption('overwrite')) {
            $fs->remove($fileName);
            $output->writeln('File removed');
        }

        // Save the file if we don't have it.
        if (!$fs->exists($fileName)) {
            $photo = file_get_contents($url);
            $fs->dumpFile($fileName, $photo);
        }
        else {
            $output->writeln('File exists');
        }
    }
}
