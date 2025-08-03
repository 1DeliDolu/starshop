<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

#[AsCommand(
    name: 'app:test-email-template',
    description: 'Test email template rendering without XSL errors',
)]
class TestEmailTemplateCommand extends Command
{
    public function __construct(
        private Environment $twig,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Test rendering the email layout template
            $rendered = $this->twig->render('email/layout.html.twig', []);
            $output->writeln('<info>✅ Email template rendered successfully!</info>');
            $output->writeln('Template content length: ' . strlen($rendered) . ' characters');
            
            // Show a sample of the rendered content
            $preview = substr($rendered, 0, 200) . '...';
            $output->writeln('<comment>Preview:</comment>');
            $output->writeln($preview);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>❌ Error rendering email template:</error>');
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $output->writeln('<error>Stack trace:</error>');
            $output->writeln('<error>' . $e->getTraceAsString() . '</error>');
            return Command::FAILURE;
        }
    }
}
