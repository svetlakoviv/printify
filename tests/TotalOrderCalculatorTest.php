<?php

namespace App\Tests;

use App\Entity\Product;
use App\Entity\ProductType;
use App\Service\TotalOrderCalculator;
use PHPUnit\Framework\TestCase;

class TotalOrderCalculatorTest extends TestCase
{
    const MUG = 'mug';
    const TSHIRT = 't-shirt';
    public function testExpressCalculation()
    {
        $product = new Product();
        $product->setCost(1);
        $productArray[] = $product;

        $totalCalculator = new TotalOrderCalculator();
        $totalSum = $totalCalculator->calculateTotalOrder('Domestic', $productArray, true);

        $this->assertEquals(1001, $totalSum);
    }

    public function testDomesticMugCalculation()
    {
        $productType = new ProductType();
        $productType->setName(self::MUG);
        $product = new Product();
        $product->setCost(1);
        $product->setProductType($productType);
        $productArray[] = $product;

        $totalCalculator = new TotalOrderCalculator();
        $totalSum = $totalCalculator->calculateTotalOrder('Domestic', $productArray, false);

        $this->assertEquals(201, $totalSum);
    }

    public function testSeveralDomesticCalculation()
    {
        $productType = new ProductType();
        $productType->setName(self::MUG);
        $product = new Product();
        $product->setCost(1);
        $product->setProductType($productType);
        $productArray[] = $product;
        $productArray[] = $product;
        $productArray[] = $product;

        $productType = new ProductType();
        $productType->setName(self::TSHIRT);
        $product = new Product();
        $product->setCost(1);
        $product->setProductType($productType);
        $productArray[] = $product;
        $productArray[] = $product;
        $productArray[] = $product;

        $totalCalculator = new TotalOrderCalculator();
        $totalSum = $totalCalculator->calculateTotalOrder('Domestic', $productArray, false);

        $this->assertEquals(606, $totalSum);
    }
}
