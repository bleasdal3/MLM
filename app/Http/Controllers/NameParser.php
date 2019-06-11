<?php

namespace App\Http\Controllers;

class NameParser extends Controller
{
    const COMMON_SALUTATIONS = [
        'MR', 'MRS', 'DR', 'MISS', 'MASTER', 'MS'
    ];

    private $firstName;
    private $fullName;
    private $lastName;

    public function __construct(string $fullName)
    {
        $this->fullName = $fullName;
        $this->firstName = $this->getFirstName();
        $this->lastName = $this->getLastName();
    }

    public function firstName()
    {
        return $this->firstName;
    }

    private function getFirstName()
    {
        $nameSplit = explode(' ', $this->fullName);
        $hasSalutation = in_array(strtoupper($nameSplit[0]), self::COMMON_SALUTATIONS);
        if ($hasSalutation) {
            return $nameSplit[1];
        }
        if (count($nameSplit) === 1) {
            return '';
        }
        return $nameSplit[0];
    }

    public function lastName()
    {
        return $this->lastName;
    }

    private function getLastName()
    {
        $nameSplit = explode(' ', $this->fullName);
        return last($nameSplit);
    }
}