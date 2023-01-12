<?php

namespace App\Entity;

use App\Repository\MovesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovesRepository::class)]
class Moves
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Color = null;

    #[ORM\Column(length: 255)]
    private ?string $X = null;

    #[ORM\Column(length: 255)]
    private ?string $Y = null;

    #[ORM\Column]
    private ?int $User_id = null;

    #[ORM\Column]
    private ?int $Game_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColor(): ?string
    {
        return $this->Color;
    }

    public function setColor(string $Color): self
    {
        $this->Color = $Color;

        return $this;
    }

    public function getX(): ?string
    {
        return $this->X;
    }

    public function setX(string $X): self
    {
        $this->X = $X;

        return $this;
    }

    public function getY(): ?string
    {
        return $this->Y;
    }

    public function setY(string $Y): self
    {
        $this->Y = $Y;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->User_id;
    }

    public function setUserId(int $User_id): self
    {
        $this->User_id = $User_id;

        return $this;
    }

    public function getGameId(): ?int
    {
        return $this->Game_id;
    }

    public function setGameId(int $Game_id): self
    {
        $this->Game_id = $Game_id;

        return $this;
    }
}
