<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     */
    private $supplement;

    /**
     * @ORM\Column(type="integer", length=1)
     */
    private $type;

    /**
     * @ORM\Column(type="smallint", length=4)
     */
    private $placement;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $url;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Hashtag", cascade={"persist"})
     */
    private $hashtags;

    /**
     * @ORM\Column(type="boolean")
     */
    private $nouveau;

    /**
     * @ORM\Column(type="smallint")
     */
    private $stock;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;


    public function __construct()
    {
        $this->hashtags = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getSupplement()
    {
        return $this->supplement;
    }

    /**
     * @param mixed $supplement
     */
    public function setSupplement($supplement): void
    {
        $this->supplement = $supplement;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
            if($this->type == 1) {
                return "Sucré";
            }
            elseif($this->type == 2) {
                return "Salé";
            }
            elseif($this->type == 3) {
                return "Boisson";
            }
            else {
                return "Autre";
            }
        }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param mixed $placement
     */
    public function setPlacement($placement): void
    {
        $this->placement = $placement;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return Produit
     */
    public function addHashtag(Hashtag $hashtag)
    {
        $this->hashtags[] = $hashtag;
    }

    public function removeHashtag(Hashtag $hashtag)
    {
        $this->hashtags->removeElement($hashtag);
    }

    /**
     * @return mixed
     */
    public function getHashtags()
    {
        return $this->hashtags;
    }

    /**
     * @return mixed
     */
    public function getNouveau()
    {
        return $this->nouveau;
    }

    /**
     * @param mixed $nouveau
     */
    public function setNouveau($nouveau): void
    {
        $this->nouveau = $nouveau;
    }

    /**
     * @return mixed
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param mixed $stock
     */
    public function setStock($stock): void
    {
        $this->stock = $stock;
    }

    /**
     * @return mixed
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * @param mixed $valide
     */
    public function setValide($valide): void
    {
        $this->valide = $valide;
    }


}
