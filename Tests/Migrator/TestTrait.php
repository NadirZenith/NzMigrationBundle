<?php

namespace Nz\MigrationBundle\Tests\Migrator;

/**
 * Description of TestTrait
 *
 * @author tino
 */
trait TestTrait
{

    protected function getConfig()
    {
        try {

            $parser = new \Symfony\Component\Yaml\Parser();
            // Use a Symfony ConfigurationInterface object to specify the *.yml format
            $yamlConfiguration = new \Nz\MigrationBundle\DependencyInjection\Configuration();
            $raw = $parser->parse(file_get_contents(__DIR__ . '/../fixtures/config.yml'));

            // Process the configuration files (merge one-or-more *.yml files)
            $processor = new \Symfony\Component\Config\Definition\Processor();
            $config = $processor->processConfiguration(
                $yamlConfiguration, array($raw['nz_migration'])
            );
        } catch (\Exception $ex) {
            return false;
        }

        return $config;
    }
    //put your code here
}
