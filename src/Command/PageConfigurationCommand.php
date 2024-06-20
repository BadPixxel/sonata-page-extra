<?php

declare(strict_types=1);

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

use BadPixxel\SonataPageExtra\Configuration\ConfigurationLoader;
use BadPixxel\SonataPageExtra\Configuration\ConfigurationResolver;
use BadPixxel\SonataPageExtra\Configuration\PageConfigurator;
use BadPixxel\SonataPageExtra\Configuration\PageIdentifier;
use BadPixxel\SonataPageExtra\Services\WebsiteManager;
use Sonata\PageBundle\Model\PageInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[AsCommand(
    name: 'sonata:page:configure',
    description: 'Configure Sonata Pages Using Yaml Files'
)]
class PageConfigurationCommand extends Command
{
    public function __construct(
        private readonly WebsiteManager $websiteManager,
        private readonly ConfigurationLoader $loader,
        private readonly PageIdentifier $identifier,
        private readonly PageConfigurator $configurator,
        private readonly ConfigurationResolver $resolver
    ) {
        parent::__construct(null);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$output instanceof ConsoleOutputInterface) {
            throw new \LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
        }
        //==============================================================================
        // Render List of Detected Files
        $this->renderFilesList($output);
        //==============================================================================
        // Create Results Table
        $table = new Table($output->section());
        $table->setHeaders(array('Site', 'ID', 'Url', 'Status', 'Error'));
        $table->render();
        //==============================================================================
        // Walk on Available Websites
        foreach ($this->websiteManager->getAvailableSites() as $site) {
            //==============================================================================
            // Walk on Available Websites
            foreach ($this->loader->loadConfigurations() as $configs) {
                //==============================================================================
                // Walk on Defined Configurations
                foreach ($configs as $key => $config) {
                    //==============================================================================
                    // Identify Target Page
                    Assert::notEmpty(
                        $page = $this->identifier->identify($site, $key),
                        "Unable to Identify Sonata Page to Configure"
                    );
                    //==============================================================================
                    // Apply Page Configuration
                    if ($error = $this->configurePage($page, $config)) {
                        $table->appendRow(
                            array($site->getName(), $page->getId(), $page->getUrl(), "<error> KO </error>", $error)
                        );
                    } else {
                        $table->appendRow(
                            array($site->getName(), $page->getId(), $page->getUrl(), "<info> OK </info>")
                        );
                    }
                }
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Render List of Configuration Detected Files
     */
    protected function renderFilesList(OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(array('Path', 'Configurations'));
        foreach ($this->loader->loadConfigurations() as $file => $configs) {
            $table->addRow(array($file, count($configs)." Item"));
        }
        $table->render();
    }

    /**
     * Apply Configuration to Page
     */
    protected function configurePage(PageInterface $page, array $config): ?string
    {
        try {
            //==============================================================================
            // Resolve Page Configuration
            $resolved = $this->resolver->resolve($page, $config);
            //==============================================================================
            // Update Page Configuration
            if ($resolved) {
                $this->configurator->configure($page, $resolved);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return null;
    }
}
