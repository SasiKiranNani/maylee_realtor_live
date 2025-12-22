<?php

namespace App\Traits;

trait AddressFormatter
{
    /**
     * Format the property address based on property subtype.
     *
     * @param array $property The property data array
     * @return string The formatted full address
     */
    protected function formatPropertyAddress(array $property): string
    {
        $subtypeMapping = [
            'Att/Row/Townhouse' => 'Freehold Townhouse',
        ];

        // Determine PropertySubType
        $propertySubType = trim($property['PropertySubType'] ?? 'N/A');
        $propertySubType = $subtypeMapping[$propertySubType] ?? $propertySubType;

        // Custom address formatting for specific property subtypes
        $subtypesWithUnitNumber = ['Freehold Townhouse', 'Condo Townhouse', 'Condo Apartment'];

        if (in_array($propertySubType, $subtypesWithUnitNumber)) {
            // Format: UnitNumber-StreetNumber StreetName StreetSuffix, City, ON PostalCode
            $unitNumber = trim($property['UnitNumber'] ?? '');
            $streetNumber = trim($property['StreetNumber'] ?? '');
            $streetName = trim($property['StreetName'] ?? '');
            $streetSuffix = trim($property['StreetSuffix'] ?? '');
            $city = trim($property['City'] ?? '');
            $postalCode = trim($property['PostalCode'] ?? '');

            // Build street address with unit number
            $streetPart = '';
            if ($unitNumber && $streetNumber) {
                $streetPart = $unitNumber . '-' . $streetNumber;
            } elseif ($streetNumber) {
                $streetPart = $streetNumber;
            }

            if ($streetName) {
                $streetPart .= ($streetPart ? ' ' : '') . $streetName;
            }

            if ($streetSuffix) {
                $streetPart .= ($streetPart ? ' ' : '') . $streetSuffix;
            }

            // Build full address
            $fullAddress = $streetPart;
            if ($city) {
                $fullAddress .= ($fullAddress ? ', ' : '') . $city . ', ON';
            }
            if ($postalCode) {
                $fullAddress .= ($fullAddress ? ' ' : '') . $postalCode;
            }
        } else {
            // Use existing UnparsedAddress or fallback for other subtypes
            $street = trim(($property['StreetNumber'] ?? '') . ' ' .
                ($property['StreetName'] ?? '') . ' ' .
                ($property['StreetSuffix'] ?? ''));
            $fullAddress = $property['UnparsedAddress'] ??
                $street . ', ' . $property['City'] . ', ' .
                $property['StateOrProvince'] . ' ' . ($property['PostalCode'] ?? '');
        }

        return $fullAddress;
    }
}
