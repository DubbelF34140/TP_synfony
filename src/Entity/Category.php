<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Wish::class, mappedBy: 'category')]
    private Collection $wishes;

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

    public function getWishes(): Collection
    {
        return $this->wishes;
    }

    public function setWishes(Collection $wishes): void
    {
        $this->wishes = $wishes;
    }

    public function __construct()
    {
        $this->wishes = new ArrayCollection();
    }

    /**
     * @return Collection<int, Wish>
     */

    public function addWish(Wish $wish): self
    {
        if (!$this->wishes->contains($wish)) {
            $this->wishes->add($wish);
            $wish->setCategory($this);
        }

        return $this;
    }

    public function removeWish(Wish $wish): self
    {
        if ($this->wishes->removeElement($wish)) {
            // set the owning side to null (unless already changed)
            if ($wish->getCategory() === $this) {
                $wish->setCategory(null);
            }
        }

        return $this;
    }


}
