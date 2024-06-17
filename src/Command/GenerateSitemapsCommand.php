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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
        parent::__construct(null);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->renderHostsList($output);

        foreach (array_keys($this->hostsManager->getSitesGroups()) as $host) {
            $this->generateHostSitemaps($host, $output);
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
    protected function generateHostSitemaps(string $host, OutputInterface $output): void
    {
        //==============================================================================
        // Ensure Host Sitemap Storage path Exists
        $this->pathBuilder->ensureHostPathExists($host);
        //==============================================================================
        // Prepare Sub-Command Inputs
        $sitemapInput = new ArrayInput(array(
            // the command name is passed as first argument
            'command' => 'sonata:seo:sitemap',
            'dir' => $this->pathBuilder->getRelativePath($host),
            '--sitemap_path' => "/maps/".$host,
            'host' => $host,
        ));
        //==============================================================================
        // Prepare Formatter
        $formatter = $this->getHelper('formatter');
        \assert($formatter instanceof FormatterHelper);
        //==============================================================================
        // Execute Sub-Command
        $application = $this->getApplication();
        if ($application && $application->doRun($sitemapInput, $output)) {
            $output->writeln($formatter->formatSection(
                " KO ",
                sprintf('Unable to sitemap for %s', $host),
                'error'
            ));
        } else {
            $output->writeln($formatter->formatSection(
                " OK ",
                sprintf('Sitemap generated for %s => %s', $host, $this->pathBuilder->getFinalRelativePath($host)),
                'info'
            ));
        }
    }
}
