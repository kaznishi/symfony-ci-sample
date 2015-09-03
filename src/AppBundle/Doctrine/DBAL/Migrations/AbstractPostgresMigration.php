<?php

namespace AppBundle\Doctrine\DBAL\Migrations;

abstract class AbstractPostgresMigration extends AbstractMigration
{
    protected function skipInvalidDB()
    {
        $dbName = $this->container->get('doctrine')->getConnection('postgres')->getDatabase();
        $this->skipIf($this->connection->getDatabase() != $dbName, "Migration can only be executed on '{$dbName}' database (use --em=postgres).'" );
    }
}
