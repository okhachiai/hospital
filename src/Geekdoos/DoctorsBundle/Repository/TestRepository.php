<?php

namespace Geekdoos\DoctorsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * TestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TestRepository extends EntityRepository
{
    /* advanced search */
    public function search($searchParam) {
        extract($searchParam);        
        $qb = $this->createQueryBuilder('t')
        		->leftJoin('t.consultation', 'c')
                ->addSelect('c')
                ->leftJoin('c.person', 'p')
                ->addSelect('p')
                ->leftJoin('c.user', 'u')
                ->addSelect('u');

        if(!empty($keyword))
            $qb->andWhere('concat(p.familyname, p.firstname) like :keyword or t.type like :keyword or c.name like :keyword')
                ->setParameter('keyword', '%'.$keyword.'%');
        if(!empty($ids))
            $qb->andWhere('t.id in (:ids)')->setParameter('ids', $ids);
        if(!empty($cin))
            $qb->andWhere('p.cin = :cin')->setParameter('cin', $cin);
        if(!empty($user))
            $qb->andWhere('u.id = :user')->setParameter('user', $user);
        if(!empty($gender))
            $qb->andWhere('p.gender = :gender')->setParameter('gender', $gender);
        if(!empty($date))
            $qb->andWhere('p.created = :date')->setParameter('date', $date);
        if(!empty($sortBy)){
            $sortBy = in_array($sortBy, array('firstname', 'familyname', 'birthday')) ? $sortBy : 'id';
            $sortDir = ($sortDir == 'DESC') ? 'DESC' : 'ASC';
            $qb->orderBy('p.' . $sortBy, $sortDir);
        }
        if(!empty($perPage)) $qb->setFirstResult(($page - 1) * $perPage)->setMaxResults($perPage);
       return new Paginator($qb->getQuery());
    }

    public function counter() {
        $qb = $this->createQueryBuilder('t')->select('COUNT(t)');
        return $qb->getQuery()->getSingleScalarResult();
    }
}
