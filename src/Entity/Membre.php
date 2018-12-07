<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembreRepository")
 * @UniqueEntity(fields={"email"},
 *     errorPath="email",
 *     message="Ce compte existe déjà.")
 */
class Membre implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Saisissez votre prénom")
     * @Assert\Length(max="50", maxMessage="Votre prénom est trop long. {{ limit }} caractères max.")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Saisissez votre nom")
     * @Assert\Length(max="50", maxMessage="Votre nom est trop long. {{ limit }} caractères max.")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Email(message="Veuillez entrer une adresse email valide.")
     * @Assert\NotBlank(message="Saisissez votre adresse email")
     * @Assert\Length(max="80", maxMessage="Votre email est trop long. {{ limit }} caractères max.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="N'oubliez pas votre mot de passe.")
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/",
     *     message="Votre mot de passe doit contenir au moins 8 caractères, 1 majuscule et 1 chiffre."
     * )
     * @Assert\Length(
     *     min="8",
     *     minMessage="Votre mot de passe est trop court. {{ limit }} caractères mini.",
     *     max="20",
     *     maxMessage="Votre mot de passe est trop long. {{ limit }} caractères max."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateInscription;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $derniereConnexion;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article",
     *     mappedBy="membre")
     */
    private $articles;

    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @Assert\IsTrue(message="Vous devez valider nos CGU.")
     */
    private $conditions;

    /**
     * Membre constructor.
     * @param string $role
     */
    public function __construct(string $role = 'ROLE_MEMBRE')
    {
        $this->addRole($role);
        $this->articles = new ArrayCollection();
        try {
            $this->dateInscription = new \DateTime("Europe/Paris");
        } catch (\Exception $e) {
            echo 'Erreur : ' . $e;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    public function getDerniereConnexion(): ?\DateTimeInterface
    {
        return $this->derniereConnexion;
    }

    public function setDerniereConnexion(string $timestamp = 'now'): self
    {
        $this->derniereConnexion = new \DateTime($timestamp);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getArticles()
    {
        return $this->articles;
    }

    /**
     * @param mixed $articles
     */
    public function setArticles($articles): void
    {
        $this->articles = $articles;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $role
     * @return bool
     */
    public function addRole(string $role) : bool
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param mixed $conditions
     */
    public function setConditions($conditions): void
    {
        $this->conditions = $conditions;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
