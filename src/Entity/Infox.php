<?php

namespace App\Entity;

use App\Repository\InfoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\inverseJoinColumn;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InfoxRepository::class)
 */
class Infox
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $intitule;

    /**
     * @ORM\Column(type="decimal", precision=2, scale=1)
     * @Assert\Regex(
     *      pattern = "/[0-9]/",
     *      message = "La viralite est comprise entre 0.0 et 9.9"
     * )
     */
    private $viralite;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Personne", inversedBy="like")
     * @ORM\JoinTable(name="like_infox",
     *     joinColumns={@ORM\JoinColumn(name="infox_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="personne_id", referencedColumnName="id")}
     *     )
     */
    private $like;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Theme")
     */
    private $theme;

    public function __construct()
    {
        $this->like = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getViralite(): ?string
    {
        return $this->viralite;
    }

    public function setViralite(string $viralite): self
    {
        $this->viralite = $viralite;

        return $this;
    }

    /**
     * @return Collection|Personne[]
     */
    public function getLike(): Collection
    {
        return $this->like;
    }

    public function addLike(Personne $like): self
    {
        if (!$this->like->contains($like)) {
            $this->like[] = $like;
            $like->addLike($this);
        }

        return $this;
    }

    public function removeLike(Personne $like): self
    {
        if ($this->like->contains($like)) {
            $this->like->removeElement($like);
            $like->removeLike($this);
        }

        return $this;
    }

    public function getTheme(): ?Theme
    {
        return $this->theme;
    }

    public function setTheme(?Theme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function removeTheme(): self {
       $this->setTheme(null);
        return $this;
    }
}
