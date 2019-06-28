<?php

namespace LinkORB\Userbase\RoleProviderBundle\RoleProvider;

use LinkORB\Contracts\UserbaseRole\RoleProviderInterface;
use LinkORB\Contracts\UserbaseRole\RoleInterface;

class FixedRolesRoleProvider implements RoleProviderInterface
{
    private $roleMap;

    public function __construct(array $roleMap)
    {
        $this->roleMap = $roleMap;
    }

    public function getRoles(RoleInterface $user): array
    {
        if (!\array_key_exists($user->getUsername(), $this->roleMap)) {
            return [];
        }

        return $this->roleMap[$user->getUsername()];
    }
}
