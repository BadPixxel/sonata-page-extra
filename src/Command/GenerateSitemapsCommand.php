<?php

/*
 *  Copyright (C) BadPixxel <www.badpixxel.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace BadPixxel\SonataPageExtra\Command;

use BadPixxel\SonataPageExtra\Services\SitemapsPathBuilder;
use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Webmozart\Assert\Assert;

#[AsCommand(
    name: "sonata:page:generate:sitemaps",
    description: "Generate Sitemaps for all actives websites"
)]
class GenerateSitemapsCommand extends Command
{
    public function __construct(
        private readonly SitemapsPathBuilder $pathBuilder,
        private readonly WebsiteManager      $hostsManager,
    ) {
        parent::__construct();
    }

    /**
     * Configure Console Command
     */
    public function configure(): void
    {
        $this
            ->addOption('scheme', null, InputOption::VALUE_OPTIONAL, 'Set the scheme', 'https')
        ;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->renderHostsList($output);

        foreach (array_keys($this->hostsManager->getSitesGroups()) as $host) {
            $this->generateHostSitemaps($host, $input, $output);
        }

        return 0;
    }

    /**
     * Render List of Websites grouped by Hosts
     */
    protected function renderHostsList(OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(array('Host', 'Website', 'Language'));
        foreach ($this->hostsManager->getSitesGroups() as $host => $group) {
            $names = $locales = array();
            foreach ($group as $site) {
                $names[] = $site->getName();
                $locales[] = $site->getLocale();
            }
            $table->addRow(array($host, implode(", ", $names), implode(", ", $locales)));
        }
        $table->render();
    }

    /**
     * Render List of Websites grouped by Hosts
     */
    protected function generateHostSitemaps(string $host, InputInterface $input, OutputInterface $output): void
    {
        $scheme = $input->getOption('scheme');
        Assert::string($scheme);
        //==============================================================================
        // Ensure Host Sitemap Storage path Exists
        $this->pathBuilder->ensureHostPathExists($host);
        //==============================================================================
        // Prepare Sub-Command Inputs
        $procInput = array(
            // PHP Version to Use
            'php' => getenv("PHP_CLI") ?: "php",
            // Sf Command to Execute
            'console' => 'bin/console',
            'command' => 'sonata:seo:sitemap',
            // Relative Path to Sitemaps
            'dir' => $this->pathBuilder->getRelativePath($host),
            // Host to Use
            'host' => $host,
            // Public Path to Sitemaps
            'path' => "--sitemap_path=/maps/".$host,
            // Public Path to Sitemaps
            'scheme' => "--scheme=".$scheme,
        );
        //==============================================================================
        // Prepare Formatter
        $formatter = $this->getHelper('formatter');
        \assert($formatter instanceof FormatterHelper);
        //==============================================================================
        // Execute Sub-Command
        $process = new Process($procInput);
        $process->run();
        if ($process->isSuccessful()) {
            $output->writeln($formatter->formatSection(
                " OK ",
                sprintf('Sitemap generated for %s => %s', $host, $this->pathBuilder->getFinalRelativePath($host)),
                'info'
            ));
        } else {
            $output->writeln($process->getOutput());
            $output->writeln($formatter->formatSection(
                " KO ",
                sprintf('Unable to sitemap for %s', $host),
                'error'
            ));
        }
    }
}
