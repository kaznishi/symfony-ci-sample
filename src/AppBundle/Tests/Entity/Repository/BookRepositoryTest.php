<?php

namespace AppBundle\Tests\Entity\Repository;

use AppBundle\Test\MainTestCase;
use AppBundle\Entity\Book;

class BookRepositoryTest extends MainTestCase
{
    /**
     * @test
     */
    public function findByName()
    {
        $this->loadFixtures('');
        $em = $this->getEntityManager();
        $book = new Book();
        $book->setName('hogehoge');
        $em->persist($book);
        $em->flush();

        $repository = $em->getRepository('AppBundle:Book');
        $books = $repository->findByName('hogehoge');
        $this->assertEquals(1, count($books));

    }

}
