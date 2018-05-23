<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
    * @ORM\Column(name="nom", type="string", length=256)
    * @Assert\NotBlank(message="Le nom ne doit pas rester vide !")
    * @Assert\Type("string", message="Il semble y avoir une erreur...")
    */
    private $nom;

    /**
     * @ORM\Column(name="adresse", type="string", length=256)
     * @Assert\NotBlank(message="L'adresse ne doit pas rester vide !")
     * @Assert\Type("string", message="Il semble y avoir une erreur...")
     */
    private $adresse;

    /**
     * @ORM\Column(name="precisions", type="string", length=256)
     * @Assert\Type("string", message="Il semble y avoir une erreur...")
     */
    private $precisions;

    /**
     * @ORM\Column(name="cp", type="string", length=256)
     * @Assert\NotNull(message="Code postal non indiqué !")
     */
    private $cp;

    /**
     * @ORM\Column(name="telephone", type="string", length=15)
     * @Assert\Regex(pattern="/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/", message="Numéro de téléphone invalide !")
     */
    private $telephone;


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
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @return mixed
     */
    public function getPrecisions()
    {
        return $this->precisions;
    }

    /**
     * @return mixed
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }



    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @param mixed $precisions
     */
    public function setPrecisions($precisions): void
    {
        $this->precisions = $precisions;
    }

    /**
     * @param mixed $cp
     */
    public function setCp($cp): void
    {
        $this->cp = $cp;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

}