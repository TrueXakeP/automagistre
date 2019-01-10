<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use LogicException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class Registry
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param object|string $entity
     */
    public function manager($entity): EntityManagerInterface
    {
        $em = $this->managerOrNull($entity);

        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManager expected');
        }

        return $em;
    }

    /**
     * @param object|string $entity
     */
    public function managerOrNull($entity): ?EntityManagerInterface
    {
        return $this->registry->getEntityManagerForClass($this->entityToString($entity));
    }

    /**
     * @param object|string $entity
     */
    public function repository($entity): EntityRepository
    {
        $class = $this->entityToString($entity);

        $repository = $this->manager($class)->getRepository($class);
        if (!$repository instanceof EntityRepository) {
            throw new LogicException('EntityRepository expected');
        }

        return $repository;
    }

    /**
     * @param object|string $entity
     */
    public function class($entity): string
    {
        $class = $this->entityToString($entity);

        return $this->manager($entity)->getClassMetadata($class)->getName();
    }

    /**
     * @param object|string $entity
     */
    public function isEntity($entity): bool
    {
        $em = $this->managerOrNull($entity);
        if (null === $em) {
            return false;
        }

        return $this->manager($entity)->getMetadataFactory()->isTransient($this->class($entity));
    }

    /**
     * @param object|string $entity
     */
    public function isTenantEntity($entity): bool
    {
        return $this->isEntity($entity) && 'tenant' === $this->manager($entity)->getConnection()->getDatabase();
    }

    /**
     * @param object|string $entity
     */
    private function entityToString($entity): string
    {
        return \is_object($entity) ? \get_class($entity) : $entity;
    }
}