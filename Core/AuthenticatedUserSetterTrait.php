<?php

declare(strict_types=1);

namespace Kaliop\eZMigrationBundle\Core;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Kaliop\eZMigrationBundle\API\Exception\InvalidUserAccountException;

trait AuthenticatedUserSetterTrait
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /**
     * Helper method to authenticate a user that can make changes to the system.
     *
     * @throws \Kaliop\eZMigrationBundle\API\Exception\InvalidUserAccountException
     */
    protected function authenticateUserByLogin(string $userLogin): void
    {
        try {
            $user = $this->userService->loadUserByLogin($userLogin);
            $this->permissionResolver->setCurrentUserReference($user);
        } catch (NotFoundException $e) {
            throw new InvalidUserAccountException(
                sprintf(
                    'Could not find the required user account to be used for logging in: "%s". UserService says: %s',
                    $userLogin,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }
    }

    protected function authenticateUserByReference(UserReference $userReference): void
    {
        $this->permissionResolver->setCurrentUserReference($userReference);
    }

    protected function getCurrentUser(): UserReference
    {
        return $this->permissionResolver->getCurrentUserReference();
    }

    public function setPermissionResolver(PermissionResolver $permissionResolver): void
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function setUserService(UserService $userService): void
    {
        $this->userService = $userService;
    }
}
