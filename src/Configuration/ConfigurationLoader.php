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

namespace BadPixxel\SonataPageExtra\Configuration;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

#[Autoconfigure(bind: array(
    '$files' => "%badpixxel_sonata_page_extra.pages.configurations%"
))]
class ConfigurationLoader
{
    /**
     * @param string[] $files
     */
    public function __construct(
        private readonly array $files
    ) {
    }

    /**
     * @return array<string, array>
     */
    public function loadConfigurations(): array
    {
        static $configurations;

        if (is_array($configurations)) {
            return $configurations;
        }

        $configurations = array();
        //==============================================================================
        // Walk on Registered Files
        foreach ($this->getFiles() as $file) {
            Assert::fileExists($file);
            //==============================================================================
            // Load Yml Contents
            $rawConfigurations = Yaml::parseFile($file) ?? array();
            Assert::isArray($rawConfigurations);
            Assert::allIsArray($rawConfigurations);

            $configurations[$file] = $rawConfigurations;
        }

        return $configurations;
    }

    /**
     * Get List of Available Files
     *
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
