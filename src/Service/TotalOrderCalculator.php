<?php

namespace App\Service;


class TotalOrderCalculator
{
    const INTERNATIONAL = 'International';
    const DOMESTIC = 'Domestic';
    const MUG = 'mug';
    const TSHIRT = 't-shirt';

    const EXPRESS_PER_ITEM = 1000;
    const DOMESTIC_MUG_PRICE = 100;
    const DOMESTIC_TSHIRT_PRICE = 50;
    const INTERNATIONAL_MUG_PRICE = 250;
    const INTERNATIONAL_TSHIRT_PRICE = 150;

    public function calculateTotalOrder($addressType, $products, bool $isExpress):int
    {
        $total = 0;
        $mugs = 0;
        $tShirts = 0;
        $totalProductsCost = 0;
        foreach ($products as $product){
            $total++;
            $totalProductsCost += $product->getCost();
            if($product->getProductType()->getName() === self::MUG){
                $mugs++;
            }
            if($product->getProductType()->getName() === self::TSHIRT){
                $tShirts++;
            }
        }
        if($isExpress){
            return self::EXPRESS_PER_ITEM*$total+$totalProductsCost;
        }

        $shippingCost = 0;
        if($addressType === self::DOMESTIC){
            if($mugs){
                $shippingCost += ($mugs+1)*self::DOMESTIC_MUG_PRICE;
            }
            if($tShirts){
                $shippingCost += ($tShirts+1)*self::DOMESTIC_TSHIRT_PRICE;
            }
            return $shippingCost+$totalProductsCost;
        }
        if($addressType === self::INTERNATIONAL){
            if($mugs){
                $shippingCost += ($mugs+1)*self::INTERNATIONAL_MUG_PRICE;
            }
            if($tShirts){
                $shippingCost += ($tShirts+1)*self::INTERNATIONAL_TSHIRT_PRICE;
            }

            return $shippingCost+$totalProductsCost;
        }

        return 0;
    }
}