<?php

namespace LinkORB\Userbase\RoleProviderBundle\RoleLoader;

use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlRoleLoader extends FileLoader
{
    protected $container;
    protected $yamlParser;

    public function __construct(Parser $yamlParser, ContainerBuilder $container, FileLocatorInterface $locator)
    {
        $this->container = $container;
        $this->yamlParser = $yamlParser;

        parent::__construct($locator);
    }

    /**
     * @return mixed
     */
    public function load($resource, $type = null)
    {
        try {
            $path = $this->locator->locate($resource, null, true);
        } catch (FileLocatorFileNotFoundException $e) {
            throw new \Exception("Unable to locate any role files named \"{$resource}\".", 0, $e);
        }

        try {
            $roles = $this->yamlParser->parseFile($path);
        } catch (ParseException $e) {
            throw new \Exception("Unable to load roles from Yaml file \"{$path}\".", 0, $e);
        }

        $this->container->setParameter('userbase.roles', $roles);
    }

    public function supports($resource, $type = null): bool
    {
        if (!\is_string($resource)) {
            return false;
        }

        if (null === $type) {
            $ext = \pathinfo($resource, PATHINFO_EXTENSION);

            return 'yaml' == $ext || 'yml' == $ext;
        }

        return 'yaml' === $type;
    }
}
