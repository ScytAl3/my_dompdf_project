<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FactureRepository")
 */
class Facture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0, message = "Un identifiant supérieur à {{ value }}.")
     * @Assert\Regex(pattern="/^[0-9]{1,4}$/", message="Entre 1 et 4 chiffres.")
     */
    private $client_id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Ip(message = "Ce n'est pas une adresse IP valide.")
     */
    private $client_adresse_ip;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_ht;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_tva;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual("01/01/2020", message = "La date ne peut pas être inférieure à {{ compared_value }}!")
     * @Assert\LessThanOrEqual("today", message = "La date ne peut pas être supérieure à aujourd'hui!")
     */
    private $facture_createAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): ?int
    {
        return $this->client_id;
    }

    public function setClientId(int $client_id): self
    {
        $this->client_id = $client_id;

        return $this;
    }

    public function getClientAdresseIp(): ?string
    {
        return $this->client_adresse_ip;
    }

    public function setClientAdresseIp(string $client_adresse_ip): self
    {
        $this->client_adresse_ip = $client_adresse_ip;

        return $this;
    }

    public function getMontantHt(): ?float
    {
        return $this->montant_ht;
    }

    public function setMontantHt(float $montant_ht): self
    {
        $this->montant_ht = $montant_ht;

        return $this;
    }

    public function getMontantTva(): ?float
    {
        return $this->montant_tva;
    }

    public function setMontantTva(float $montant_tva): self
    {
        $this->montant_tva = $montant_tva;

        return $this;
    }

    public function getFactureCreateAt(): ?\DateTimeInterface
    {
        return $this->facture_createAt;
    }

    public function setFactureCreateAt(\DateTimeInterface $facture_createAt): self
    {
        $this->facture_createAt = $facture_createAt;

        return $this;
    }
}
