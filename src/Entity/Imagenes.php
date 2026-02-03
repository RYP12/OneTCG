<?php

namespace App\Entity;

use App\Repository\ImagenesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagenesRepository::class)]
#[ORM\Table(name: 'imagenes', schema: 'onetcg')]
class Imagenes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Carta::class, inversedBy: 'imagenes')]
    #[ORM\JoinColumn(name: 'id_carta', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Carta $carta = null;

    #[ORM\Column(length: 10)]
    private ?string $tamanyo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $url = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTamanyo(): ?string
    {
        return $this->tamanyo;
    }

    public function setTamanyo(string $tamanyo): static
    {
        $this->tamanyo = $tamanyo;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
