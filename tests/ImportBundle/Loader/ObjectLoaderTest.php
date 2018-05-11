<?php

declare(strict_types=1);

namespace Tests\Darkilliant\ImportBundle\Loader;

use App\Entity\Product;
use App\Entity\ProductExtraData;
use Darkilliant\ImportBundle\Loader\ObjectLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ObjectLoaderTest extends TestCase
{
    /** @var ObjectLoader $objectLoader */
    private $objectLoader;

    protected function setUp()
    {
        parent::setUp();
        $this->objectLoader = new ObjectLoader(PropertyAccess::createPropertyAccessor());
    }

    public function testLoad()
    {
        $data = [
            'title' => 'nom produit',
            'extra' => [
                'picture' => 'photo',
            ],
        ];
        $mapping = [
            'title' => '@[title]',
            'price_ttc' => 5,
            'extra' => [
                'picture' => '@[picture]',
            ],
        ];
        $mappingRelation = [
            'extra' => ProductExtraData::class,
        ];

        /** @var Product $product */
        $product = $this->objectLoader->load(new Product(), $data, [
            'mapping' => $mapping,
            'mapping_relation' => $mappingRelation,
        ]);

        $this->assertEquals('nom produit', $product->getTitle());
        $this->assertEquals('photo', $product->getExtra()->getPicture());
        $this->assertEquals(5, $product->getPriceTtc());
    }
}