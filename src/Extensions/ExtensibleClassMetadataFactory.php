<?php

namespace LaravelDoctrine\Fluent\Extensions;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

class ExtensibleClassMetadataFactory extends ClassMetadataFactory
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Override to hold a reference to the EntityManager here as well (parent property is private).
     *
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        parent::setEntityManager($em);

        $this->entityManager = $em;
    }

    /**
     * Override to implement our custom ClassMetadata object.
     *
     * {@inheritdoc}
     */
    protected function newClassMetadataInstance($className)
    {
        return new ExtensibleClassMetadata($className, $this->entityManager->getConfiguration()->getNamingStrategy());
    }
}
