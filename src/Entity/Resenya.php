<?php

namespace App\Entity;

use App\Repository\ResenyaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResenyaRepository::class)]
#[ORM\Table(name: 'resenya', schema: 'onetcg')]
#[ORM\UniqueConstraint(name: 'resenya_usuario_carta_unique', columns: ['id_usuario', 'id_carta'])]
class Resenya
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 1)]
    private ?string $puntuacion = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comentario = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'id_usuario', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(targetEntity: Carta::class)]
    #[ORM\JoinColumn(name: 'id_carta', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Carta $carta = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuntuacion(): ?string
    {
        return $this->puntuacion;
    }

    public function setPuntuacion(string $puntuacion): static
    {
        $this->puntuacion = $puntuacion;
        return $this;
    }

    public function getComentario(): ?string
    {
        return $this->comentario;
    }

    public function setComentario(?string $comentario): static
    {
        $this->comentario = $comentario;
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
}
