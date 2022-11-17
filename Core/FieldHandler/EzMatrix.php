<?php

namespace Kaliop\eZMigrationBundle\Core\FieldHandler;

use Kaliop\eZMigrationBundle\API\FieldDefinitionConverterInterface;
use Kaliop\eZMigrationBundle\API\FieldValueConverterInterface;

/**
 * @todo make fieldSettings independent from version of ezmatrix in use, by converting them from a common format
 */
class EzMatrix extends AbstractFieldHandler implements FieldValueConverterInterface
{
    public function hashToFieldValue($fieldValue, array $context = array())
    {
        /// @todo resolve refs ?

        $rows = array();
        foreach ($fieldValue as $data) {
            $rows[] = new \EzSystems\EzPlatformMatrixFieldtype\FieldType\Value\Row($data);
        }
        return new \EzSystems\EzPlatformMatrixFieldtype\FieldType\Value($rows);
}

    public function fieldValueToHash($fieldValue, array $context = array())
    {
        $data = array();

        foreach ($fieldValue->getRows() as $row) {
            $data[] = $row->getCells();
        }

        return $data;
    }
}
