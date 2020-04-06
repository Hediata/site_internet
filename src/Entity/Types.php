<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypesRepository")
 * @ORM\Table(name="types")
 */
class Types
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $nom;

    public function getNom(): ?string
    {
        return $this->nom;
    }
}
