<?php

namespace App\Entity;

use App\Repository\TypeRoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRoomRepository::class)]
class TypeRoom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $PriceDay = null;

    #[ORM\Column]
    private ?int $numberOfRooms = null;

    #[ORM\OneToMany(mappedBy: 'typeRoom', targetEntity: Room::class)]
    private Collection $rooms;

    #[ORM\Column]
    private ?int $MaxGuests = null;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
    }

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

    public function getPriceDay(): ?float
    {
        return $this->PriceDay;
    }

    public function setPriceDay(float $PriceDay): self
    {
        $this->PriceDay = $PriceDay;

        return $this;
    }

    public function getNumberOfRooms(): ?int
    {
        return $this->numberOfRooms;
    }

    public function setNumberOfRooms(int $numberOfRooms): self
    {
        $this->numberOfRooms = $numberOfRooms;

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms->add($room);
            $room->setTypeRoom($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->removeElement($room)) {
            // set the owning side to null (unless already changed)
            if ($room->getTypeRoom() === $this) {
                $room->setTypeRoom(null);
            }
        }

        return $this;
    }

    public function getMaxGuests(): ?int
    {
        return $this->MaxGuests;
    }

    public function setMaxGuests(int $MaxGuests): self
    {
        $this->MaxGuests = $MaxGuests;

        return $this;
    }
}
