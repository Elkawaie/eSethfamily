<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ResidentRepository")
 */
class Resident
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Famille", inversedBy="resident")
     */
    private $famille;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="fkResident", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ehpad", inversedBy="residents")
     */
    private $ehpad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getFamille(): ?Famille
    {
        return $this->famille;
    }

    public function setFamille(?Famille $famille): self
    {
        $this->famille = $famille;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newFkResident = null === $user ? null : $this;
        if ($user->getFkResident() !== $newFkResident) {
            $user->setFkResident($newFkResident);
        }

        return $this;
    }

    public function getEhpad(): ?ehpad
    {
        return $this->ehpad;
    }

    public function setEhpad(?ehpad $ehpad): self
    {
        $this->ehpad = $ehpad;

        return $this;
    }
}
