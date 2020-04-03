<?php

use LinkORB\Userbase\RoleProviderBundle\RoleLoader\YamlRoleLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class YamlRoleLoaderTest extends TestCase
{
    private $container;
    private $locator;
    private $roleLoader;
    private $yamlParser;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerBuilder::class);
        $this->locator = $this->createMock(FileLocator::class);
        $this->yamlParser = $this->createMock(Parser::class);

        $this->roleLoader = new YamlRoleLoader($this->yamlParser, $this->container, $this->locator);
    }

    public function testLoadWillRejectAllButTheFirstLocatedFileInThePreconfiguredLocationOnly()
    {
        // args to locate
        $name = $this->equalTo('some-file.yaml');
        $currentPath = $this->isNull();
        $first = $this->isTrue(); // return the first occurrence

        // make sure that locate receives the correct args (and then end the test)
        $this->locator
            ->expects($this->once())
            ->method('locate')
            ->with($name, $currentPath, $first)
            ->will($this->throwException(new FileLocatorFileNotFoundException()));

        $this->expectException(\Exception::class);

        $this->roleLoader->load('some-file.yaml');
    }

    public function testLoadThrowsExceptionWhenFileCannotBeLocated()
    {
        $this->locator
            ->method('locate')
            ->will($this->throwException(new FileLocatorFileNotFoundException()));


        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to locate any role files named "some-file.yaml"');

        $this->roleLoader->load('some-file.yaml');
    }

    public function testLoadThrowsExceptionWhenFileCannotBeParsed()
    {
        $this->locator
            ->method('locate')
            ->willReturn('/a/path/to/some-file.yaml');

        $this->yamlParser
            ->method('parseFile')
            ->will($this->throwException(new ParseException('')));


        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unable to load roles from Yaml file "/a/path/to/some-file.yaml"');

        $this->roleLoader->load('some-file.yaml');
    }

    public function testLoadSetsServiceParameterWithLoadedRolesAsItsValue()
    {
        $this->locator
            ->method('locate')
            ->willReturn('/a/path/to/some-file.yaml');

        $this->yamlParser
            ->method('parseFile')
            ->willReturn(['a-name' => 'a-role', 'another-role']);

        $this->container
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->equalTo('userbase.roles'),
                $this->equalTo(['a-name' => 'a-role', 'another-role']));


        $this->roleLoader->load('some-file.yaml');
    }

    /**
     * @dataProvider supports
     */
    public function testTheUnusedButMandatedMethodNamedSupports($resource, $type, $isSupported)
    {
        if ($isSupported) {
            $this->assertTrue($this->roleLoader->supports($resource, $type));
        } else {
            $this->assertFalse($this->roleLoader->supports($resource, $type));
        }
    }

    public function supports()
    {
        return [
            'the loader supports names with a .yml extension' => ['a-file-name.yaml', null, true],
            'the loader supports names with a .yaml extension' => ['a-file-name.yml', null, true],
            'the loader does not support names with an .xml extension' => ['a-file-name.xml', null, false],
            'the loader supports explicit yaml type of any name' => ['any-name-at.all', 'yaml', true],
            'the loader does not support other types' => ['a-file-name.yml', 'xml', false],
        ];
    }
}
