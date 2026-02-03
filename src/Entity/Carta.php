<?php

namespace App\Entity;

use App\Repository\CartaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CartaRepository::class)]
#[ORM\Table(name: 'carta', schema: 'onetcg')]
class Carta
{
    #[ORM\Id]
    //  Hay que eliminar esta linea para usar la id de la api #[ORM\GeneratedValue]
    #[ORM\Column(type: 'string', length: 50)]
    private ?string $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $rarity = null;

    #[ORM\Column(length: 50)]
    private ?string $tipo = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(nullable: true)]
    private ?int $coste = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $atributoNombre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $atributoIconoUrl = null;

    #[ORM\Column(nullable: true)]
    private ?int $poder = null;

    #[ORM\Column(nullable: true)]
    private ?int $counter = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $familia = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $habilidad = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $efectoTrigger = null;

    #[ORM\ManyToOne(targetEntity: Expansiones::class)]
    #[ORM\JoinColumn(name: 'id_expansion', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Expansiones $expansion = null;

    #[ORM\OneToMany(mappedBy: 'carta', targetEntity: Imagenes::class, cascade: ['persist', 'remove'])]
    private Collection $imagenes;

    public function __construct()
    {
        $this->imagenes = new ArrayCollection();
    }

    /**
     * @return Collection<int, Imagenes>
     */
    public function getImagenes(): Collection
    {
        return $this->imagenes;
    }
    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getRarity(): ?string
    {
        return $this->rarity;
    }

    public function setRarity(?string $rarity): static
    {
        $this->rarity = $rarity;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): static
    {
        $this->tipo = $tipo;

        return $this;
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

    public function getCoste(): ?int
    {
        return $this->coste;
    }

    public function setCoste(?int $coste): static
    {
        $this->coste = $coste;

        return $this;
    }

    public function getAtributoNombre(): ?string
    {
        return $this->atributoNombre;
    }

    public function setAtributoNombre(?string $atributoNombre): static
    {
        $this->atributoNombre = $atributoNombre;

        return $this;
    }

    public function getAtributoIconoUrl(): ?string
    {
        return $this->atributoIconoUrl;
    }

    public function setAtributoIconoUrl(?string $atributoIconoUrl): static
    {
        $this->atributoIconoUrl = $atributoIconoUrl;

        return $this;
    }

    public function getPoder(): ?int
    {
        return $this->poder;
    }

    public function setPoder(?int $poder): static
    {
        $this->poder = $poder;

        return $this;
    }

    public function getCounter(): ?int
    {
        return $this->counter;
    }

    public function setCounter(?int $counter): static
    {
        $this->counter = $counter;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getFamilia(): ?string
    {
        return $this->familia;
    }

    public function setFamilia(?string $familia): static
    {
        $this->familia = $familia;

        return $this;
    }

    public function getHabilidad(): ?string
    {
        return $this->habilidad;
    }

    public function setHabilidad(?string $habilidad): static
    {
        $this->habilidad = $habilidad;

        return $this;
    }

    public function getEfectoTrigger(): ?string
    {
        return $this->efectoTrigger;
    }

    public function setEfectoTrigger(?string $efectoTrigger): static
    {
        $this->efectoTrigger = $efectoTrigger;

        return $this;
    }

    public function getExpansion(): ?Expansiones
    {
        return $this->expansion;
    }

    public function setExpansion(?Expansiones $expansion): static
    {
        $this->expansion = $expansion;
        return $this;
    }
}
