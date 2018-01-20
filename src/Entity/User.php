<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User extends Entity implements UserInterface, \Serializable
{
    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $username;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    protected $email;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @var Role
     * @ORM\ManyToOne(targetEntity="App\Entity\Role")
     */
    protected $role;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enables
     */
    public function setEnabled(bool $enable): void
    {
        $this->enabled = $enable;
    }

    /**
     * @return array
     */
    public function getRoles(): Role
    {
        return $this->role;
    }

    /**
     * @param Role $roles
     */
    public function setRoles(Role $role): void
    {
        $this->role = $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        return serialize([$this->id, $this->username, $this->password]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        // add $this->salt too if you don't use Bcrypt or Argon2i
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([$this->id, $this->username, $this->password]);
    }
}