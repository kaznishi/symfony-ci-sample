<?php

namespace UsePostgresqlBundle\Tests\Entity\Repository;

use AppBundle\Test\MainTestCase;
use UsePostgresqlBundle\Entity\Shelf;

class ShelfRepositoryTest extends MainTestCase
{
    /**
     * @test
     */
    public function findOneBy()
    {
        $this->loadPostgresFixtures('');
        $em = $this->getPostgresEntityManager();
        $shelf = new Shelf();
        $shelf->setName('asdf');
        $shelf->setPrice('30000');
        $shelf->setDescription('hogehogehogehoge');
        $em->persist($shelf);
        $em->flush();

        $repository = $em->getRepository('UsePostgresqlBundle:Shelf');
        $shelves = $repository->findOneBy(['name' => 'asdf']);
        $this->assertEquals(1, count($shelves));

    }

}
