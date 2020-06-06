<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PersonneRepository::class)
 */
class Personne
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Your name cannot contain a number"
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=1)
     * @Assert\Choice({"M", "F"}, message="La sexe doit être M ou F")
     */
    private $sexe;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=0)
     * @Assert\LessThanOrEqual(
     *     value = 120
     * )
     */
    private $age;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie")
     * @ORM\JoinColumn(nullable=true)
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Infox", mappedBy="like")
     */
    private $like;

    public function __construct()
    {
        $this->like = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|Infox[]
     */
    public function getLike(): Collection
    {
        return $this->like;
    }

    public function addLike(Infox $like): self
    {
        if (!$this->like->contains($like)) {
            $this->like[] = $like;
            $like->addLike($this);
        }

        return $this;
    }

    public function removeLike(Infox $like): self
    {
        if ($this->like->contains($like)) {
            $this->like->removeElement($like);
            $like->removeLike($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string)$this->nom;
    }

}
