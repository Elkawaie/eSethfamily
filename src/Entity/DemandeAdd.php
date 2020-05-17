<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeAddRepository")
 */
class DemandeAdd
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sujet;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Famille", inversedBy="demandeAdds")
     */
    private $demandeur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idSujet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $choixEhpadResident;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getDemandeur(): ?Famille
    {
        return $this->demandeur;
    }

    public function setDemandeur(?Famille $demandeur): self
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    public function getIdSujet(): ?string
    {
        return $this->idSujet;
    }

    public function setIdSujet(string $idSujet): self
    {
        $this->idSujet = $idSujet;

        return $this;
    }

    public function getChoixEhpadResident(): ?string
    {
        return $this->choixEhpadResident;
    }

    public function setChoixEhpadResident(?string $choixEhpadResident): self
    {
        $this->choixEhpadResident = $choixEhpadResident;

        return $this;
    }
}
