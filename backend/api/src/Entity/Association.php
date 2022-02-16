<?php

namespace App\Entity;

use App\Entity\MediaObject;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\AssociationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    attributes: ["security" => "is_granted('ROLE_USER')"],
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete', 'patch'],
)]
/**
 * @ORM\Entity(repositoryClass=AssociationRepository::class)
 * @Vich\Uploadable
 */
class Association
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
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity=Etudiant::class, mappedBy="associations")
     */
    private $etudiants;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="association", orphanRemoval=true)
     */
    private $evenements;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $administrateurs = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $logo;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->etudiants = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Etudiant[]
     */
    public function getEtudiants(): Collection
    {
        return $this->etudiants;
    }

    public function addEtudiant(Etudiant $etudiant): self
    {
        if (!$this->etudiants->contains($etudiant)) {
            $this->etudiants[] = $etudiant;
            $etudiant->addAssociation($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            $etudiant->removeAssociation($this);
        }

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->setAssociation($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getAssociation() === $this) {
                $evenement->setAssociation(null);
            }
        }

        return $this;
    }

    public function getAdministrateurs(): ?array
    {
        return $this->administrateurs;
    }

    public function setAdministrateurs(?array $administrateurs): self
    {
        $this->administrateurs = $administrateurs;

        return $this;
    }

    public function getLogo(): ?int
    {
        return $this->logo;
    }

    public function setLogo(?int $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
}
