<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// SECCIÓN: Entidad Ranking
#[ORM\Entity]
#[ORM\Table(name: 'ranking', schema: 'onetcg')]
class Ranking
{
    // SECCIÓN: Propiedades
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToMany(targetEntity: Carta::class)]
    #[ORM\JoinTable(name: 'ranking_cartas_disponibles', schema: 'onetcg')]
    private Collection $cartasDisponibles;

    // SECCIÓN: Constructor
    public function __construct()
    {
        $this->cartasDisponibles = new ArrayCollection();
    }

    // SECCIÓN: Getters y Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }
    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * @return Collection<int, Carta>
     */
    public function getCartasDisponibles(): Collection
    {
        return $this->cartasDisponibles;
    }

    public function addCartasDisponible(Carta $carta): static
    {
        if (!$this->cartasDisponibles->contains($carta)) {
            $this->cartasDisponibles->add($carta);
        }
        return $this;
    }

    public function removeCartasDisponible(Carta $carta): static
    {
        $this->cartasDisponibles->removeElement($carta);
        return $this;
    }
}