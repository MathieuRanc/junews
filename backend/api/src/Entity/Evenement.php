<?php

namespace App\Entity;

use App\Entity\MediaObject;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    attributes: ["security" => "is_granted('ROLE_USER')"],
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete', 'patch'],
)]
/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDebut;


    /**
     * @ORM\Column(type="datetime")
     */
    private $dateFin;

    /**
     * @ORM\ManyToMany(targetEntity=Materiel::class, inversedBy="evenements")
     */
    private $materiels;

    /**
     * @ORM\ManyToMany(targetEntity=Promotion::class, inversedBy="evenements")
     */
    private $promos;

    /**
     * @ORM\ManyToOne(targetEntity=Lieu::class, inversedBy="evenements")
     */
    private $lieu;

    /**
     * @ORM\ManyToMany(targetEntity=Etudiant::class, mappedBy="evenements")
     */
    private $etudiants;

    /**
     * @ORM\ManyToOne(targetEntity=Association::class, inversedBy="evenements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $association;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $image;

    public function __construct()
    {
        $this->materiels = new ArrayCollection();
        $this->promos = new ArrayCollection();
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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }



    /**
     * @return Collection|Materiel[]
     */
    public function getMateriels(): Collection
    {
        return $this->materiels;
    }

    public function addMateriel(Materiel $materiel): self
    {
        if (!$this->materiels->contains($materiel)) {
            $this->materiels[] = $materiel;
        }

        return $this;
    }

    public function removeMateriel(Materiel $materiel): self
    {
        $this->materiels->removeElement($materiel);

        return $this;
    }

    /**
     * @return Collection|Promotion[]
     */
    public function getPromos(): Collection
    {
        return $this->promos;
    }

    public function addPromo(Promotion $promo): self
    {
        if (!$this->promos->contains($promo)) {
            $this->promos[] = $promo;
        }

        return $this;
    }

    public function removePromo(Promotion $promo): self
    {
        if ($this->promos->removeElement($promo)) {
            $promo->removeEvenement($this);
        }

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

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
            $etudiant->addEvenement($this);
        }

        return $this;
    }

    public function removeEtudiant(Etudiant $etudiant): self
    {
        if ($this->etudiants->removeElement($etudiant)) {
            $etudiant->removeEvenement($this);
        }

        return $this;
    }

    public function getAssociation(): ?Association
    {
        return $this->association;
    }

    public function setAssociation(?Association $association): self
    {
        $this->association = $association;

        return $this;
    }

    public function getImage(): ?int
    {
        return $this->image;
    }

    public function setImage(?int $image): self
    {
        $this->image = $image;

        return $this;
    }
}
