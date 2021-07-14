<?php

namespace App\Entity;

use App\Repository\SearchRepository;
use Doctrine\ORM\Mapping as ORM;
use function PHPUnit\Framework\stringContains;

/**
 * @ORM\Entity(repositoryClass=SearchRepository::class)
 */
class Search
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $country;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $country = str_replace(' ', '-', $country);
        $this->country = $country;

        return $this;
    }
}
