<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Roles;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAsset;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GrupoUsuario
 *
 * @ORM\Table(name="grupo_usuario")
 * @ORM\Entity(repositoryClass="App\Repository\GrupoUsuarioRepository")
 */
class GrupoUsuario
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
   * @ORM\Column(type="string",length=2147483647, name="nombre")
   * @Assert\NotBlank( message = "El campo no puede ser vacio")
   * @Assert\Type(type="string")
   */
  private $nombre;

  /**
   * @var string
   *
   * @ORM\Column(type="string",length=2147483647, name="descripcion", nullable=true)
   * @Assert\Type(type="string")
   */
  private $descripcion;

  /**
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="grupos")
   * @ORM\JoinTable(name="grupo_has_roles",
   *     joinColumns={@ORM\JoinColumn(name="grupo_id", referencedColumnName="id")},
   *     inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
   * )
   *
   * @var ArrayCollection $roles
   */
  private $roles;

  /**
   * @var \App\Entity\User
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="grupos")
   */
  private $usuario;


  /**
   * Constructor
   */
  public function __construct()
  {
    $this->roles = new ArrayCollection();
    $this->usuario = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set nombre
   *
   * @param string $nombre
   *
   * @return GrupoUsuario
   */
  public function setNombre($nombre)
  {
    $this->nombre = $nombre;

    return $this;
  }

  /**
   * Get nombre
   *
   * @return string
   */
  public function getNombre()
  {
    return $this->nombre;
  }

  /**
   * Set descripcion
   *
   * @param string $descripcion
   *
   * @return GrupoUsuario
   */
  public function setDescripcion($descripcion)
  {
    $this->descripcion = $descripcion;

    return $this;
  }

  /**
   * Get descripcion
   *
   * @return string
   */
  public function getDescripcion()
  {
    return $this->descripcion;
  }

  /**
   * Add role
   *
   * @param \App\Entity\Roles $role
   *
   * @return GrupoUsuario
   */
  public function addRole(\App\Entity\Role $role)
  {
    $this->roles[] = $role;

    return $this;
  }

  /**
   * Remove role
   *
   * @param \App\Entity\Role $role
   */
  public function removeRole(\App\Entity\Role $role)
  {
    $this->roles->removeElement($role);
  }

  /**
   * Get roles
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getRoles()
  {
    return $this->roles;
  }

  /**
   * Add usuario
   *
   * @param \App\Entity\Usuario $usuario
   *
   * @return GrupoUsuario
   */
  public function addUsuario(\App\Entity\User $usuario)
  {
    $this->usuario[] = $usuario;

    return $this;
  }

  /**
   * Remove usuario
   *
   * @param \App\Entity\User $usuario
   */
  public function removeUsuario(\App\Entity\User $usuario)
  {
    $this->usuario->removeElement($usuario);
  }

  /**
   * Get usuario
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getUsuario()
  {
    return $this->usuario;
  }

    public function getRolesString()
    {
        $diag = '';
        for($i = 0; $i < count($this->roles); $i++) {
            if ($i < count($this->roles) - 1) {
                $diag .= $this->roles[$i]->getRol()."\n";
            }
            else
                $diag .= $this->roles[$i]->getRol();
        }

        return $diag;
    }
}
