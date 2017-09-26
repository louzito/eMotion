<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Vehicule
 *
 * @ORM\Table(name="vehicule")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VehiculeRepository")
 */
class Vehicule
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="marque", type="string", length=255)
     */
    private $marque;

    /**
     * @var string
     *
     * @ORM\Column(name="modele", type="string", length=255)
     */
    private $modele;

    /**
     * @var string
     *
     * @ORM\Column(name="numSerie", type="string", length=255)
     */
    private $numSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=255)
     */
    private $couleur;

    /**
     * @var string
     *
     * @ORM\Column(name="plaqueImmatriculation", type="string", length=255)
     */
    private $plaqueImmatriculation;

    /**
     * @var int
     *
     * @ORM\Column(name="nbKilometres", type="integer")
     */
    private $nbKilometres;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAchat", type="datetime")
     */
    private $dateAchat;

    /**
     * @var int
     *
     * @ORM\Column(name="prixAchat", type="integer")
     */
    private $prixAchat;


    public function __toString()
    {
        return $this->getInfos();
    }

    public function getInfos()
    {
        $nom = $this->getMarque() .' '. $this->getModele() .' ' . $this->getPlaqueImmatriculation();
        return $nom;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set marque
     *
     * @param string $marque
     *
     * @return Vehicule
     */
    public function setMarque($marque)
    {
        $this->marque = $marque;

        return $this;
    }

    /**
     * Get marque
     *
     * @return string
     */
    public function getMarque()
    {
        return $this->marque;
    }

    /**
     * Set modele
     *
     * @param string $modele
     *
     * @return Vehicule
     */
    public function setModele($modele)
    {
        $this->modele = $modele;

        return $this;
    }

    /**
     * Get modele
     *
     * @return string
     */
    public function getModele()
    {
        return $this->modele;
    }

    /**
     * Set numSerie
     *
     * @param string $numSerie
     *
     * @return Vehicule
     */
    public function setNumSerie($numSerie)
    {
        $this->numSerie = $numSerie;

        return $this;
    }

    /**
     * Get numSerie
     *
     * @return string
     */
    public function getNumSerie()
    {
        return $this->numSerie;
    }

    /**
     * Set couleur
     *
     * @param string $couleur
     *
     * @return Vehicule
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set plaqueImmatriculation
     *
     * @param string $plaqueImmatriculation
     *
     * @return Vehicule
     */
    public function setPlaqueImmatriculation($plaqueImmatriculation)
    {
        $this->plaqueImmatriculation = $plaqueImmatriculation;

        return $this;
    }

    /**
     * Get plaqueImmatriculation
     *
     * @return string
     */
    public function getPlaqueImmatriculation()
    {
        return $this->plaqueImmatriculation;
    }

    /**
     * Set nbKilometres
     *
     * @param integer $nbKilometres
     *
     * @return Vehicule
     */
    public function setNbKilometres($nbKilometres)
    {
        $this->nbKilometres = $nbKilometres;

        return $this;
    }

    /**
     * Get nbKilometres
     *
     * @return int
     */
    public function getNbKilometres()
    {
        return $this->nbKilometres;
    }

    /**
     * Set dateAchat
     *
     * @param \DateTime $dateAchat
     *
     * @return Vehicule
     */
    public function setDateAchat($dateAchat)
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    /**
     * Get dateAchat
     *
     * @return \DateTime
     */
    public function getDateAchat()
    {
        return $this->dateAchat;
    }

    /**
     * Set prixAchat
     *
     * @param integer $prixAchat
     *
     * @return Vehicule
     */
    public function setPrixAchat($prixAchat)
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    /**
     * Get prixAchat
     *
     * @return int
     */
    public function getPrixAchat()
    {
        return $this->prixAchat;
    }
}

