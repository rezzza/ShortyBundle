<?php

namespace Rezzza\ShortyBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Rezzza\ShortyBundle\Pattern\PatternNotFoundException;

/**
 * ExpandCommand
 *
 * @uses ContainerAwareCommand
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class ExpandCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rezzza:shorty:expand')
            ->setDescription('Generate a expand URL')
            ->setHelp(<<<EOF
This command generates a expand URL.

<comment>./app/console rezzza:shorty:expand google http://www.verylastroom.com/</comment>

EOF
            )
            ->addArgument('service',InputArgument::REQUIRED,'The service to use')
            ->addArgument('url', InputArgument::REQUIRED, 'The URL to expand')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Time to change your underwear!</info>');

        $service = $input->getArgument('service');
        $url       = $input->getArgument('url');

        $shorty = $this->getContainer()->get(sprintf('rezzza.shorty.%s', $service));

        $output->write(sprintf('Generating expand URL for <comment>%s</comment> with <comment>%s</comment>...', $url, $service));

        $longUrl = $shorty->expand($url);
        $output->writeln(sprintf(' <info>%s</info>', $longUrl));
    }
}
