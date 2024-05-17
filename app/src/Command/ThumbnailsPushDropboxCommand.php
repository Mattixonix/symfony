<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Command;

use Spatie\Dropbox\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MateuszJagielskiRekrutacjaSmartiveapp\Entity\Thumbnail;

#[AsCommand(
    name: 'smartiveapp:thumbnails-push-dropbox',
    description: 'Command for uploading files into Dropbox.',
)]
class ThumbnailsPushDropboxCommand extends Command
{

    private const THUMBNAILS_DIRECTORY = '/smartiveapp/thumbnails/';
    private $entityManager;

    public function __construct(EntityManagerInterface $entity_manager)
    {
        parent::__construct();
        $this->entityManager = $entity_manager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repository = $this->entityManager->getRepository(Thumbnail::class);
        $thumbnails = $repository->findBy(['sendStatus' => 0]);
        $status_info = [
            'deleted_no_file' => 0,
            'deleted_invalid_token' => 0,
            'deleted_other_errors' => 0,
            'uploaded' => 0
        ];

        foreach ($thumbnails as $thumbnail) {
            $client = new Client($thumbnail->getDropboxToken());

            if (!file_exists($thumbnail->getImagePath())) {
                $this->removeThumbnail($thumbnail);
                $status_info['deleted_no_file']++;

                continue;
            }

            $thumbnail_image = file_get_contents($thumbnail->getImagePath());
            $thumbnail_name = pathinfo($thumbnail->getImagePath())['basename'];

            try {
                $client->upload(self::THUMBNAILS_DIRECTORY  . $thumbnail_name, $thumbnail_image);
            } catch (\Exception $e) {
                if ($e->getCode() === 401) {
                    $status_info['deleted_invalid_token']++;
                    $this->removeThumbnail($thumbnail);

                    continue;
                } else {
                    $status_info['deleted_other_errors']++;
                    $this->removeThumbnail($thumbnail);

                    continue;
                }
            }

            $thumbnail->setSendStatus(1);
            $this->entityManager->flush();
            $status_info['uploaded']++;
        }

        if ($status_info['deleted_no_file']) {
            $io->note('Thumbnails deleted due to missing file - ' . $status_info['deleted_no_file']);
        }
        if ($status_info['deleted_invalid_token']) {
            $io->note('Thumbnails deleted due to invalid token - ' . $status_info['deleted_invalid_token']);
        }
        if ($status_info['deleted_other_errors']) {
            $io->error('Thumbnails deleted due to other error - ' . $status_info['deleted_other_errors']);
        }
        if ($status_info['uploaded']) {
            $io->success('Thumbnails upladed successfully - ' . $status_info['uploaded']);
        }

        $io->success('Command finished.');

        return Command::SUCCESS;
    }

    private function removeThumbnail(Thumbnail $thumbnail)
    {
        if (file_exists($thumbnail->getImagePath())) {
            unlink($thumbnail->getImagePath());
        }

        $this->entityManager->remove($thumbnail);
        $this->entityManager->flush();
    }
}
