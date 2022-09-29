<?php

namespace LinkORB\Userbase\RoleProviderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('userbase_role_provider');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('fixed_roles')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('from_files')
                            ->canBeDisabled()
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('path')
                                    ->defaultValue("{$this->projectDir}/config")
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('file_name')
                                    ->defaultValue('roles.yaml')
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('type')
                                    ->defaultValue('yaml')
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
