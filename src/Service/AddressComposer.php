<?php


namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;

class AddressComposer
{
    private $initialAddressArray;

    /**
     * @Assert\NotBlank
     */
    private $type;

    /**
     * @Assert\NotBlank
     */
    private $fullName;

    /**
     * @Assert\NotBlank
     */
    private $address;

    /**
     * @Assert\NotBlank
     */
    private $country;

    /**
     * @Assert\NotBlank
     */
    private $state;

    /**
     * @Assert\NotBlank
     */
    private $city;

    /**
     * @Assert\NotBlank
     */
    private $zip;

    /**
     * @Assert\NotBlank
     */
    private $phone;

    /**
     * @Assert\NotNull
     */
    private $region;

    public function loadAddressArray($addressArray)
    {
        $this->type = $addressArray['type']??null;
        $this->fullName = $addressArray['full_name']??null;
        $this->address = $addressArray['address']??null;
        $this->country = $addressArray['country']??null;
        $this->state = $addressArray['state']??null;
        $this->city = $addressArray['city']??null;
        $this->zip = $addressArray['zip']??null;
        $this->phone = $addressArray['phone']??null;
        $this->region = $addressArray['region']??null;

        $this->initialAddressArray = $addressArray;
    }

    /**
     * compose adress and prepare it for DB format
     * @return string
     */
    public function composeAdress():string
    {
        return json_encode($this->initialAddressArray);
    }

    public function getAddressType():string
    {
        return $this->type;
    }
}