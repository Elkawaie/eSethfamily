<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Famille", inversedBy="user", cascade={"persist", "remove"})
     */
    private $fkFamille;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Resident", inversedBy="user", cascade={"persist", "remove"})
     */
    private $fkResident;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ehpad", inversedBy="users")
     */
    private $ehpad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if(empty($roles)){
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getActif(): ?bool
    {
        $actif = $this->actif;
        if(empty($actif)){
            $actif = false;
        }
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getFkFamille(): ?Famille
    {
        return $this->fkFamille;
    }

    public function setFkFamille(?Famille $fkFamille): self
    {
        $this->fkFamille = $fkFamille;

        return $this;
    }

    public function getFkResident(): ?Resident
    {
        return $this->fkResident;
    }

    public function setFkResident(?Resident $fkResident): self
    {
        $this->fkResident = $fkResident;

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
