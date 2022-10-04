<?php

namespace LinkORB\Userbase\RoleProviderBundle\DependencyInjection;

use LinkORB\Userbase\RoleProviderBundle\RoleLoader\YamlRoleLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Yaml\Parser;

class UserbaseRoleProviderExtension extends Extension implements CompilerPassInterface
{
    /**
     * @var string Service id of the Userbase Client UserProvider
     */
    const SVC_USER_PROVIDER = 'user_base_client.user_provider';
    /**
     * @var string Service tag applied to implementations of RoleProviderInterface
     */
    const TAG_ROLE_PROVIDER = 'userbase_role_provider.role_provider';

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.xml');

        $config = $this->processConfiguration(
            new Configuration($container->getParameter('kernel.project_dir')),
            $configs
        );

        if (true === $config['fixed_roles']['from_files']['enabled']) {
            // the loader will make the roles available in the userbase.roles param
            $this->loadFixedRoles($container, $config);
        }
    }

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SVC_USER_PROVIDER)) {
            return;
        }

        $userProviderDefn = $container->findDefinition(self::SVC_USER_PROVIDER);

        $roleProviders = $container->findTaggedServiceIds(self::TAG_ROLE_PROVIDER);
        if (empty($roleProviders)) {
            throw new \RuntimeException('Cannot supply a RoleProvider to Userbase UserProvider because there are no services tagged with "userbase_role_provider.role_provider".');
        }

        $userProviderDefn->addMethodCall(
            'setRoleProvider',
            [
                new Reference(\array_key_first($roleProviders)),
            ]
        );
    }

    private function loadFixedRoles(ContainerBuilder $container, $config)
    {
        if (isset($config['fixed_roles']['from_files'])) {
            $this->loadFixedRolesFromFiles($container, $config);
        }
    }

    private function loadFixedRolesFromFiles(ContainerBuilder $container, $config)
    {
        if ('yaml' === $config['fixed_roles']['from_files']['type']) {
            $this->loadFixedRolesFromYamlFiles(
                $container,
                $config['fixed_roles']['from_files']['path'],
                $config['fixed_roles']['from_files']['file_name']
            );
        } else {
            throw new \UnexpectedValueException("Invalid config value: userbase_role_provider.fixed_roles.from_files.type = \"{$config['fixed_roles']['from_files']['type']}\". The role provider can load fixed roles only from yaml files.");
        }
    }

    private function loadFixedRolesFromYamlFiles(
        ContainerBuilder $container,
        string $rolesDirectory,
        string $rolesFilename
    ) {
        $loader = new YamlRoleLoader(new Parser(), $container, new FileLocator($rolesDirectory));

        try {
            $loader->load($rolesFilename);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to load roles from the directory \"{$rolesDirectory}\"", 0, $e);
        }
    }
}
