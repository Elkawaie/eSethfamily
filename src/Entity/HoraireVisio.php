<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HoraireVisioRepository")
 */
class HoraireVisio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $debut;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fin;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ehpad", inversedBy="horaireVisios")
     */
    private $ehpad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getEhpad(): ?Ehpad
    {
        return $this->ehpad;
    }

    public function setEhpad(?Ehpad $ehpad): self
    {
        $this->ehpad = $ehpad;

        return $this;
    }
}
