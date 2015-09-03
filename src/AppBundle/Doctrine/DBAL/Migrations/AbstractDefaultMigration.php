<?php

namespace AppBundle\Doctrine\DBAL\Migrations;

/**
 * Class AbstractDefaultMigration
 * @package AppBundle\Doctrine\DBAL\Migrations
 *
 * @see https://gist.github.com/cyk/10167795
 */
abstract class AbstractDefaultMigration extends AbstractMigration
{
    public function skipInvalidDB()
    {
        $dbName = $this->container->get('doctrine')->getConnection('default')->getDatabase();
        $this->skipIf($this->connection->getDatabase() != $dbName, "Migration can only be executed on '{$dbName}' database (use --em=default).'" );
    }
}
