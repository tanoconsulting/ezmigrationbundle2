<?php

namespace Kaliop\eZMigrationBundle\Core\Matcher;

use Ibexa\Contracts\Core\Repository\Repository;

abstract class RepositoryMatcher extends AbstractMatcher
{
    protected $repository;

    /**
     * @param Repository $repository
     * @todo inject the services needed, not the whole repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }
}
