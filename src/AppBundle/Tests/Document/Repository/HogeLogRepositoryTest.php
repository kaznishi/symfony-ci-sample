<?php

namespace AppBundle\Tests\Document\Repository;

use AppBundle\Test\MainTestCase;
use AppBundle\Document\HogeLog;

class HogeLogRepositoryTest extends MainTestCase
{
    /**
     * @test
     */
    public function findByName()
    {
        $this->loadDocumentFixtures('');
        $dm = $this->getDocumentManager();
        $hogeLog = new HogeLog();
        $hogeLog->setName('hogehoge');
        $dm->persist($hogeLog);
        $dm->flush();

        $repository = $dm->getRepository('AppBundle:HogeLog');
        $hogeLogs = $repository->findBy(['name'=>'hogehoge']);
        $this->assertEquals(1, count($hogeLogs));

    }

}
