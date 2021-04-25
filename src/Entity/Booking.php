<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $customers;

    /**
     * @ORM\Column(type="datetime")
     */
    public $arrivalAt;

    /**
     * @ORM\Column(type="datetime")
     */
    public $departureAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $hotelId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $bedroomType;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->arrivalAt = new \DateTime();
        $this->departureAt = new \DateTime();
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

    public function getHotelId(): ?int
    {
        return $this->hotelId;
    }

    public function setHotelId(int $hotelId): self
    {
        $this->hotelId = $hotelId;

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
}
