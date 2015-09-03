<?php

namespace AppBundle\Doctrine\DBAL\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration as DBALAbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractMigration  extends DBALAbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerAwareInterface
     */
    protected $container;

    public function preUp(Schema $schema)
    {
        $this->skipInvalidDB();
    }

    public function preDown(Schema $schema)
    {
        $this->skipInvalidDB();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    abstract protected function skipInvalidDB();

}
