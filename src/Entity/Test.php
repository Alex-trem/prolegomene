<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TestRepository::class)
 */
class Test
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
    private $aze;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAze(): ?string
    {
        return $this->aze;
    }

    public function setAze(string $aze): self
    {
        $this->aze = $aze;

        return $this;
    }
}
