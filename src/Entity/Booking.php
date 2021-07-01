<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 * @UniqueEntity(fields={"arrivalAt", "hotel", "bedroomType"})
 */
#[ApiResource(
    itemOperations:["get"],
    collectionOperations: []
)]
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=0, max=6)
     */
    public $customers;

    /**
     * @ORM\Column(type="datetime")
     */
    public $arrivalAt;

    /**
     * @ORM\Column(type="datetime")
     */
    public $departureAt;

   /**
     * @ORM\ManyToOne(targetEntity=Hotel::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $hotel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $bedroomType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_review;

    public function __construct()
    {
        $this->arrivalAt = new \DateTime();
        $this->departureAt = new \DateTime();
        $this->has_review = false;
    }

    public function __toString()
    {
        return $this->arrivalAt->format('Y-m-d') . ' / ' . $this->departureAt->format('Y-m-d') . ' - ' . $this->bedroomType;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomers(): ?int
    {
        return $this->customers;
    }

    public function setCustomers(int $customers): self
    {
        $this->customers = $customers;

        return $this;
    }

    public function getArrivalAt(): ?\DateTimeInterface
    {
        return $this->arrivalAt;
    }

    public function setArrivalAt(\DateTimeInterface $arrivalAt): self
    {
        $this->arrivalAt = $arrivalAt;

        return $this;
    }

    public function getDepartureAt(): ?\DateTimeInterface
    {
        return $this->departureAt;
    }

    public function setDepartureAt(\DateTimeInterface $departureAt): self
    {
        $this->departureAt = $departureAt;

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    public function getBedroomType(): ?string
    {
        return $this->bedroomType;
    }

    public function setBedroomType(string $bedroomType): self
    {
        $this->bedroomType = $bedroomType;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getHasReview(): ?bool
    {
        return $this->has_review;
    }

    public function setHasReview(bool $has_review): self
    {
        $this->has_review = $has_review;

        return $this;
    }
}
