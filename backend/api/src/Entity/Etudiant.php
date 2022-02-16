<?php

namespace App\Entity;

use App\Entity\MediaObject;
use App\Repository\EtudiantRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Controller\CreateMediaObjectAction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf\Literal\Integer;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ApiResource(
    //attributes: ["security" => "is_granted('ROLE_USER')"],
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put', 'delete', 'patch'],
)]
/**
 * @ORM\Entity(repositoryClass=EtudiantRepository::class)
 * @Vich\Uploadable()
 */
class Etudiant implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\ManyToMany(targetEntity=Evenement::class, inversedBy="etudiants")
     */
    private $evenements;

    /**
     * @ORM\ManyToMany(targetEntity=Association::class, inversedBy="etudiants")
     */
    private $associations;

    /**
     * @ORM\ManyToMany(targetEntity=Cours::class, inversedBy="etudiants")
     */
    private $cours;

    /**
     * @ORM\ManyToOne(targetEntity=Promotion::class, inversedBy="etudiants")
     */
    private $promotion;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $administre;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $photo;



    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->associations = new ArrayCollection();
        $this->cours = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        $this->evenements->removeElement($evenement);

        return $this;
    }

    /**
     * @return Collection|Association[]
     */
    public function getAssociations(): Collection
    {
        return $this->associations;
    }

    public function addAssociation(Association $association): self
    {
        if (!$this->associations->contains($association)) {
            $this->associations[] = $association;
        }

        return $this;
    }

    public function removeAssociation(Association $association): self
    {
        $this->associations->removeElement($association);

        return $this;
    }

    /**
     * @return Collection|Cours[]
     */
    public function getCours(): Collection
    {
        return $this->cours;
    }

    public function addCour(Cours $cour): self
    {
        if (!$this->cours->contains($cour)) {
            $this->cours[] = $cour;
        }

        return $this;
    }

    public function removeCour(Cours $cour): self
    {
        $this->cours->removeElement($cour);

        return $this;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getAdministre(): ?int
    {
        return $this->administre;
    }

    public function setAdministre(?int $administre): self
    {
        $this->administre = $administre;

        return $this;
    }

    public function getPhoto(): ?int
    {
        return $this->photo;
    }

    public function setPhoto(?int $photo): self
    {
        $this->photo = $photo;

        return $this;
    }


}
