<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EhpadRepository")
 */
class Ehpad
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
    private $adresse;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="ehpad")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Resident", mappedBy="ehpad")
     */
    private $residents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Famille", mappedBy="ehpads")
     */
    private $familles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HoraireVisio", mappedBy="ehpad")
     */
    private $horaireVisios;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->residents = new ArrayCollection();
        $this->familles = new ArrayCollection();
        $this->horaireVisios = new ArrayCollection();
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setEhpad($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getEhpad() === $this) {
                $user->setEhpad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Resident[]
     */
    public function getResidents(): Collection
    {
        return $this->residents;
    }

    public function addResident(Resident $resident): self
    {
        if (!$this->residents->contains($resident)) {
            $this->residents[] = $resident;
            $resident->setEhpad($this);
        }

        return $this;
    }

    public function removeResident(Resident $resident): self
    {
        if ($this->residents->contains($resident)) {
            $this->residents->removeElement($resident);
            // set the owning side to null (unless already changed)
            if ($resident->getEhpad() === $this) {
                $resident->setEhpad(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * @return Collection|Famille[]
     */
    public function getFamilles(): Collection
    {
        return $this->familles;
    }

    public function addFamille(Famille $famille): self
    {
        if (!$this->familles->contains($famille)) {
            $this->familles[] = $famille;
            $famille->addEhpad($this);
        }

        return $this;
    }

    public function removeFamille(Famille $famille): self
    {
        if ($this->familles->contains($famille)) {
            $this->familles->removeElement($famille);
            $famille->removeEhpad($this);
        }

        return $this;
    }

    /**
     * @return Collection|HoraireVisio[]
     */
    public function getHoraireVisios(): Collection
    {
        return $this->horaireVisios;
    }

    public function addHoraireVisio(HoraireVisio $horaireVisio): self
    {
        if (!$this->horaireVisios->contains($horaireVisio)) {
            $this->horaireVisios[] = $horaireVisio;
            $horaireVisio->setEhpad($this);
        }

        return $this;
    }

    public function removeHoraireVisio(HoraireVisio $horaireVisio): self
    {
        if ($this->horaireVisios->contains($horaireVisio)) {
            $this->horaireVisios->removeElement($horaireVisio);
            // set the owning side to null (unless already changed)
            if ($horaireVisio->getEhpad() === $this) {
                $horaireVisio->setEhpad(null);
            }
        }

        return $this;
    }
}
