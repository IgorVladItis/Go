<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $user_id = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $GameDate = null;

    #[ORM\Column(length: 255)]
    private ?string $PW = null;

    #[ORM\Column(length: 255)]
    private ?string $PB = null;

    #[ORM\OneToMany(targetEntity: Moves::class, mappedBy: 'Game_id')]
    private $moves;

    protected $fileName;

    public function __construct()
    {
        $this->moves = new ArrayCollection();
    }

    /**
     * @return Collection|Moves[]
     */
    public function getMoves(): Collection
    {
        return $this->moves;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getGameDate(): ?\DateTimeInterface
    {
        return $this->GameDate;
    }

    public function setGameDate(\DateTimeInterface $GameDate): self
    {
        $this->GameDate = $GameDate;

        return $this;
    }

    public function getPW(): ?string
    {
        return $this->PW;
    }

    public function setPW(string $PW): self
    {
        $this->PW = $PW;

        return $this;
    }

    public function getPB(): ?string
    {
        return $this->PB;
    }

    public function setPB(string $PB): self
    {
        $this->PB = $PB;

        return $this;
    }

    public function setFileName(File $file = null)
    {
        $this->fileName = $file;
    }

    public function getFileName()
    {
        return $this->fileName;
    }


}
