<?php

namespace App\Entity;

use App\Repository\ViajerosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ViajerosRepository::class)
 */
class Viajeros
{
     /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(type="integer")
   */
private $id;
/**
 * @ORM\Column(type="string")
 */
private $idIPK;
/**
 * @ORM\Column(type="string")
 */
private $nombre;
/**
 * @ORM\Column(type="string",nullable=true)
 */
private $ci;
/**
 * @ORM\Column(type="string")
 */
private $fechaSalida;
/**
 * @ORM\Column(type="string")
 */
private $resultado;
/**
 * @ORM\Column(type="string")
 */
private $correo;
/**
 * @ORM\Column(type="string")
 */
private $notificado;
/**
 * @return mixed
 */
public function getIdIPK()
{
  return $this->idIPK;
}

/**
 * @param mixed $idIPK
 */
public function setIdIPK($idIPK)
{
  $this->idIPK = $idIPK;
}

/**
 * @return mixed
 */
public function getNombre()
{
  return $this->nombre;
}

/**
 * @param mixed $nombre
 */
public function setNombre($nombre)
{
  $this->nombre = $nombre;
}

/**
 * @return mixed
 */
public function getId()
{
  return $this->id;
}



/**
 * @return mixed
 */
public function getCi()
{
  return $this->ci;
}

/**
 * @param mixed $ci
 */
public function setCi($ci)
{
  $this->ci = $ci;
}

/**
 * @return mixed
 */
public function getResultado()
{
  return $this->resultado;
}

/**
 * @param mixed $resultado
 */
public function setResultado($resultado)
{
  $this->resultado = $resultado;
}

/**
 * @return mixed
 */
public function getFechaSalida()
{
  return $this->fechaSalida;
}

/**
 * @param mixed $fechaSalida
 */
public function setFechaSalida($fechaSalida)
{
  $this->fechaSalida = $fechaSalida;
}

/**
 * @return mixed
 */
public function getCorreo()
{
  return $this->correo;
}

/**
 * @param mixed $correo
 */
public function setCorreo($correo)
{
  $this->correo = $correo;
}

/**
 * @return mixed
 */
public function getNotificado()
{
  return $this->notificado;
}

/**
 * @param mixed $notificado
 */
public function setNotificado($notificado)
{
  $this->notificado = $notificado;
}

}
