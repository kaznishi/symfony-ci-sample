<?php
namespace AppBundle\Entity\Repository;
use Doctrine\ORM\EntityRepository;

class BookRepository extends EntityRepository
{
    public function findByName($name)
    {
        return $this->findBy([
            'name' => $name,
        ]);
    }
}
