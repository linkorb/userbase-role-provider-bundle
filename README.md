Userbase Role Provider Bundle

Use this bundle to add Roles to Users loaded by the UserProvider.

Userbase does not provide Role information about its users so apps need to
supply their own.  This bundle allows you to define fixed roles for your users
and these will be incorporated into the User objects loaded by the Userbase
UserProvider.

Define the fixed roles in a Yaml file at `config/roles.yaml`:-

```yaml
my-username: [ROLE_ADMIN]
some-other-username: [ROLE_USER]
```

Next, clear the app cache so that the roles are baked into the service
container configuration.  And that's it!  You can now restrict access to
resources based on these roles.

The bundle will load roles from the roles.yaml file without any further
configuration.  It can instead be made to load roles from the environment.
Populating a service parameter named `userbase.roles` is the key to this
alternative configuration:

```
# add to a .env file
USERBASE_ROLES='{"adele":["ROLE_ADMIN"]}'

# add to config/services.yaml
parameters:
  userbase.roles: '%env(json:USERBASE_ROLES)%'

# add to config/packages/userbase.yaml
userbase_role_provider:
  fixed_roles:
    from_files: false
```

The change to userbase.yaml will require that the app cache is cleared.  Then
the roles will be loaded from the `USERBASE_ROLES` environment variable at the
start of every request.


Installation
============

Note: this feature requires version 1.7 or later of the userbase/client
package.  You may need to begin installation by issuing:-

```console
$ composer require "linkorb/userbase-client ^1.7"
```

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require linkorb/userbase-role-provider-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require linkorb/userbase-role-provider-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    LinkORB\Userbase\RoleProvider\UserbaseRoleProviderBundle::class => ['all' => true],
];
```
