<?php

namespace App\Command;

use App\Controller\PostUpdatesController;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

#[AsCommand(
    name: 'app:post-update',
    description: 'Posts updates to telegram channel',
)]
class PostUpdateCommand extends Command
{
    public function __construct(
        private PostUpdatesController $postUpdatesController
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->postUpdatesController->doUpdatePosting();
        foreach (json_decode($response->getContent(), true) as $element) {
            $output->writeln(
                json_encode($element, JSON_PRETTY_PRINT)
            );
        }

        return Command::SUCCESS;
    }
}
