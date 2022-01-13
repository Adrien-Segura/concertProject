<?php

namespace App\Entity;

use App\Repository\PictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $alternativeName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    private $attachment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAlternativeName(): ?string
    {
        return $this->alternativeName;
    }

    public function setAlternativeName(string $alternativeName): self
    {
        $this->alternativeName = $alternativeName;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAttachment(): ?File
    {
        return $this->attachment;
    }

    public function setAttachment(File $attachment): self
    {
        $this->attachment = $attachment;

        return $this;
    }
}
