<?php

namespace Nz\MigrationBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LogRepository extends EntityRepository
{

    public function findOneBySource($source)
    {
        /* $class = sprintf('%s:%d', get_class($source), $source->getId()); */
        return $this->findOneBy(['source' => get_class($source), 'sourceId' => @$source->getId()]);
    }

    public function findOneMigratedBySource($source)
    {
        
        $source_class= str_replace('Proxies\__CG__\\', '', get_class($source));
        /*d(get_parent_class($source));*/
        
        $qb = $this->createQueryBuilder('l');
        $query = $qb
            ->where('l.source = :source')
            ->andWhere('l.sourceId = :sourceId')
            ->andWhere('l.target IS NOT NULL')
            ->andWhere('l.targetId IS NOT NULL')
            ->setParameter('source', $source_class)
            /*->setParameter('source', get_class($source))*/
            ->setParameter('sourceId', $source->getId())
            ->setMaxResults(1)
            ->getQuery()
        ;

        $result = $query->execute();
        return empty($result) ? null : $result[0];
    }

    public function findOneByTarget($target)
    {
        return $this->findOneBy(['target' => get_class($target), 'targetId' => $target->getId()]);
    }

    public function findMigratedSourceIdsBySource($source)
    {
        //scape namespace backslash
        $source = str_replace('\\', '\\\\', is_object($source) ? get_class($source) : (string) $source);

        $query = $this->getEntityManager()->createQuery('SELECT l.sourceId FROM NzMigrationBundle:Log l WHERE l.source LIKE :source');
        $query->setParameter('source', '%' . $source . '%');
        $result = $query->getResult();

        if (!empty($result)) {
            $result = array_column($result, 'sourceId');
        }

        return $result;


        $qb = $this->createQueryBuilder('l');
        $result = $qb
            /* ->select('l') */
            /* ->from('NzMigrationBundle:Log', 'l') */
            /* ->where('l.source LIKE :class') */
            /* ->where('l.source = :class') */
            ->where($qb->expr()->like('l.source', ':class'))
            /* ->setParameter('class', $class . ':33') */
            ->setParameter('class', '%' . $class . ':%')
            ->getQuery()
            /* ->getSQL() */
            ->getResult()
        ;


        d($result);
        dd($qb);
    }
}
