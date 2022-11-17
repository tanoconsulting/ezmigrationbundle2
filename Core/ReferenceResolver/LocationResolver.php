<?php

namespace Kaliop\eZMigrationBundle\Core\ReferenceResolver;

use Kaliop\eZMigrationBundle\Core\Matcher\LocationMatcher;

/**
 * Handles references to locations. At the moment: supports remote Ids.
 */
class LocationResolver extends AbstractResolver
{
    /**
     * Defines the prefix for all reference identifier strings in definitions
     */
    protected $referencePrefixes = array('location_by_remote_id:');

    protected $locationMatcher;

    /**
     * @param LocationMatcher $locationMatcher
     */
    public function __construct(LocationMatcher $locationMatcher)
    {
        parent::__construct();

        $this->locationMatcher = $locationMatcher;
    }

    /**
     * @param string $stringIdentifier location_by_remote_id:<remote_id>
     * @return string Location id
     * @throws \Exception
     */
    public function getReferenceValue($stringIdentifier)
    {
        $ref = $this->getReferenceIdentifierByPrefix($stringIdentifier);
        switch ($ref['prefix']) {
            case 'location_by_remote_id:':
                return $this->locationMatcher->MatchOne(array(LocationMatcher::MATCH_LOCATION_REMOTE_ID => $ref['identifier']))->id;
        }
    }
}
