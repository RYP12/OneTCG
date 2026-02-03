<?php

namespace App\Entity;

use App\Repository\ExpansionesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExpansionesRepository::class)]
#[ORM\Table(name: 'expansiones', schema: 'onetcg')]
class Expansiones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $nombreExpansion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreExpansion(): ?string
    {
        return $this->nombreExpansion;
    }

    public function setNombreExpansion(string $nombreExpansion): static
    {
        $this->nombreExpansion = $nombreExpansion;

        return $this;
    }
}
