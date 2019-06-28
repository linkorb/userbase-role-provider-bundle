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

That's it!  You can now restrict access to resources based on these roles.


Installation
============

Note: this feature requires version 1.6 or later of the userbase/client
package.  You may need to begin installation by issuing:-

```console
$ composer require "linkorb/userbase-client ^1.6"
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
