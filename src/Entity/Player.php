<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_game = null;

    #[ORM\Column(length: 255)]
    private ?string $nickname = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdGame(): ?int
    {
        return $this->id_game;
    }

    public function setIdGame(int $id_game): self
    {
        $this->id_game = $id_game;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }
}
