<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Entity;

use MateuszJagielskiRekrutacjaSmartiveapp\Repository\ThumbnailRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThumbnailRepository::class)]
class Thumbnail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 1000)]
    private ?string $imagePath = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $destination = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?string $sendStatus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dropboxToken = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getDestination(): ?int
    {
        return $this->destination;
    }

    public function setDestination(int $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getSendStatus(): ?string
    {
        return $this->sendStatus;
    }

    public function setSendStatus(string $sendStatus): static
    {
        $this->sendStatus = $sendStatus;

        return $this;
    }

    public function getDropboxToken(): ?string
    {
        return $this->dropboxToken;
    }

    public function setDropboxToken(?string $dropboxToken): static
    {
        $this->dropboxToken = $dropboxToken;

        return $this;
    }
}
