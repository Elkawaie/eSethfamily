<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilleRepository")
 */
class Famille
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
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="fkFamille", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resident", mappedBy="famille")
     */
    private $resident;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Visio", mappedBy="participant")
     */
    private $visios;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ehpad", inversedBy="familles")
     */
    private $ehpads;

    /**
     * @ORM\Column(type="text")
     */
    private $commentaire;




    public function __construct()
    {
        $this->resident = new ArrayCollection();
        $this->visios = new ArrayCollection();
        $this->ehpads = new ArrayCollection();
    }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newFkFamille = null === $user ? null : $this;
        if ($user->getFkFamille() !== $newFkFamille) {
            $user->setFkFamille($newFkFamille);
        }

        return $this;
    }

    /**
     * @return Collection|Resident[]
     */
    public function getResident(): Collection
    {
        return $this->resident;
    }

    public function addResident(Resident $resident): self
    {
        if (!$this->resident->contains($resident)) {
            $this->resident[] = $resident;
            $resident->setFamille($this);
        }

        return $this;
    }

    public function removeResident(Resident $resident): self
    {
        if ($this->resident->contains($resident)) {
            $this->resident->removeElement($resident);
            // set the owning side to null (unless already changed)
            if ($resident->getFamille() === $this) {
                $resident->setFamille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Visio[]
     */
    public function getVisios(): Collection
    {
        return $this->visios;
    }

    public function addVisio(Visio $visio): self
    {
        if (!$this->visios->contains($visio)) {
            $this->visios[] = $visio;
            $visio->addParticipant($this);
        }

        return $this;
    }

    public function removeVisio(Visio $visio): self
    {
        if ($this->visios->contains($visio)) {
            $this->visios->removeElement($visio);
            $visio->removeParticipant($this);
        }

        return $this;
    }

    /**
     * @return Collection|ehpad[]
     */
    public function getEhpads(): Collection
    {
        return $this->ehpads;
    }

    public function addEhpad(ehpad $ehpad): self
    {
        if (!$this->ehpads->contains($ehpad)) {
            $this->ehpads[] = $ehpad;
        }

        return $this;
    }

    public function removeEhpad(ehpad $ehpad): self
    {
        if ($this->ehpads->contains($ehpad)) {
            $this->ehpads->removeElement($ehpad);
        }

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }


}
