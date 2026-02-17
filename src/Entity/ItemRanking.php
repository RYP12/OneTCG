<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

// SECCIÓN: Entidad Item de Ranking
#[ORM\Entity]
#[ORM\Table(name: 'item_ranking', schema: 'onetcg')]
class ItemRanking
{
    // SECCIÓN: Propiedades
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Ranking::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Ranking $ranking = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: Carta::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Carta $carta = null;

    #[ORM\Column(type: 'integer')]
    private ?int $orden = 0;

    // SECCIÓN: Getters y Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRanking(): ?Ranking
    {
        return $this->ranking;
    }
    public function setRanking(?Ranking $ranking): static
    {
        $this->ranking = $ranking;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }
    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getCarta(): ?Carta
    {
        return $this->carta;
    }
    public function setCarta(?Carta $carta): static
    {
        $this->carta = $carta;
        return $this;
    }

    public function getOrden(): ?int
    {
        return $this->orden;
    }
    public function setOrden(int $orden): static
    {
        $this->orden = $orden;
        return $this;
    }
}