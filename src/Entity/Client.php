<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity('cin')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 10000000,
        max: 99999999,
        notInRangeMessage: 'Le CIN doit contenir exactement 8 chiffres',
    )]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(
        min: 10000000,
        max: 99999999,
        notInRangeMessage: 'Le néméro de téléphone doit contenir exactement 8 chiffres',
    )]
    private ?int $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    private ?string $mail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adr = null;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'client')]
    private Collection $commandes;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(?int $tel): static
    {
        $this->tel = $tel;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdr(): ?string
    {
        return $this->adr;
    }

    public function setAdr(?string $adr): static
    {
        $this->adr = $adr;

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setClient($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getClient() === $this) {
                $commande->setClient(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
