<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReviewRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=ReviewRepository::class)
 */
#[ApiResource(
    attributes: [
        "order" => ["createdAt" => "DESC"]
    ],
    paginationItemsPerPage: 6,
    normalizationContext: ['groups' => ['read:review']],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:review']]
        ]
    ]
)]
#[ApiFilter(SearchFilter::class, properties:["hotel" => "exact"])]
class Review
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:review'])]
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(min=0, max=10)
     */
    #[Groups(['write:review', 'read:review'])]
    private $rating;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:review'])]
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:review'])]
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['read:review'])]
    private $hotel;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reviews")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['read:review'])]
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Booking::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['read:review'])]
    private $booking;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking): self
    {
        $this->booking = $booking;

        return $this;
    }
}
