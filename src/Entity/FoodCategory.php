<?php

namespace App\Entity;

use App\Repository\FoodCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FoodCategoryRepository::class)]
class FoodCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Food $foodId = null;

    #[ORM\ManyToOne(inversedBy: 'foodCategories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $categoryId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFoodId(): ?Food
    {
        return $this->foodId;
    }

    public function setFoodId(?Food $foodId): static
    {
        $this->foodId = $foodId;

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->categoryId;
    }

    public function setCategoryId(?Category $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }
}
