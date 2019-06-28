<?php

use LinkORB\Contracts\UserbaseRole\RoleInterface;
use LinkORB\Userbase\RoleProviderBundle\RoleProvider\FixedRolesRoleProvider;
use PHPUnit\Framework\TestCase;

class FixedRolesRoleProviderTest extends TestCase
{
    /**
     * @dataProvider roleMaps
     */
    public function testGetRolesWillReturnAListOfRolesMappedToTheUserName($user, $roleMap, $expectedRoles)
    {
        $roleProvider = new FixedRolesRoleProvider($roleMap);
        $this->assertSame($expectedRoles, $roleProvider->getRoles($user));
    }

    public function roleMaps()
    {
        $data = [
            'zero roles are expected when roleMap is empty' => [
                'a-name',
                [],
                [],
            ],
            'zero roles are expected when the user is not in the role map' => [
                'an-unmapped-name',
                ['a-name' => ['a-role']],
                [],
            ],
            'a list of roles is expected when the user is in the role map' => [
                'a-name',
                ['a-name' => ['a-role', 'a-nuther-role']],
                ['a-role', 'a-nuther-role'],
            ],
        ];

        foreach ($data as $description => $args) {
            $user = $this->getMockBuilder(RoleInterface::class)
                ->setMethods(['getUsername'])
                ->getMockForAbstractClass()
            ;
            $user->method('getUsername')->willReturn($args[0]);
            yield $description => [$user, $args[1], $args[2]];
        }
    }
}
