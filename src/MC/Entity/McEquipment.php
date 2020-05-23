<?php

declare(strict_types=1);

namespace App\MC\Entity;

use App\Shared\Doctrine\ORM\Mapping\Traits\Identity;
use App\Vehicle\Domain\Embeddable\Equipment as CarEquipment;
use App\Vehicle\Domain\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class McEquipment
{
    use Identity;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class)
     */
    public ?Model $model = null;

    /**
     * @Assert\Valid
     *
     * @ORM\Embedded(class=CarEquipment::class)
     */
    public ?CarEquipment $equipment = null;

    /**
     * @ORM\Column(type="integer", length=4)
     */
    public int $period = 0;

    /**
     * @var Collection<int, McLine>
     *
     * @ORM\OneToMany(targetEntity=McLine::class, mappedBy="equipment")
     */
    public ?Collection $lines = null;

    public function __construct()
    {
        $this->equipment = new CarEquipment();
        $this->lines = new ArrayCollection();
    }
}