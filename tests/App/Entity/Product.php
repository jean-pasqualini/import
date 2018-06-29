<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation;
use App\Entity\BoutiqueInterface;
use Symfony\Component\Validator\Constraints as Asserts;

/**
 * @ORM\Entity()
 * @ORM\Table(name="Product", indexes={@ORM\Index(name="search_ean", columns={"ean"})})
 */
class Product
{
    const IMPORT_FIELD_IDENTIFIERS = ['ean'];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $ean;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Asserts\GreaterThan(5, groups={"demo"})
     */
    private $priceTtc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $priceHt;

    /**
     * @var Boutique
     * @ORM\ManyToOne(targetEntity="BoutiqueInterface")
     */
    private $boutique;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", cascade={"persist"}, orphanRemoval=true)
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="ProductExtraData", cascade={"persist"})
     */
    private $extra;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPriceTtc()
    {
        return $this->priceTtc;
    }

    /**
     * @param mixed $priceTtc
     */
    public function setPriceTtc($priceTtc)
    {
        $this->priceTtc = $priceTtc;
    }

    /**
     * @return mixed
     */
    public function getPriceHt()
    {
        return $this->priceHt;
    }

    /**
     * @param mixed $priceHt
     */
    public function setPriceHt($priceHt)
    {
        $this->priceHt = $priceHt;
    }

    /**
     * @return mixed
     */
    public function getBoutique()
    {
        return $this->boutique;
    }

    /**
     * @param mixed $boutique
     */
    public function setBoutique(BoutiqueInterface $boutique)
    {
        $this->boutique = $boutique;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param mixed $ean
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
    }

    public function setTags(array $tags) {
        $this->tags = $tags;
    }

    /**
     * @return ProductExtraData
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra(ProductExtraData $extra)
    {
        $this->extra = $extra;
    }
}