<?php

namespace AppBundle\Repository;

/**
 * OperationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OperationRepository extends \Doctrine\ORM\EntityRepository
{
    public function getBalans()
    {
        $balans = $this->createQueryBuilder('operation')
            ->select("sum(operation.valueUAH)")
            ->getQuery()
            ->getResult();
        $balans = $balans[0][1];
        return $balans;
    }


    public function getSearch($date)
    {
        $start = $date['From'];
        $end = $date['To']->add(new \DateInterval('P1D'));;

        $operations = $this->createQueryBuilder('operation')
            ->where('operation.date >= (:startDate)')
            ->andWhere('operation.date <= (:endDate)')
            ->orderBy("operation.date", 'DESC')
            ->setParameters(['startDate' => $start,
                'endDate' => $end])
            ->getQuery()
            ->getResult();
        ;

        $revenues = $this->createQueryBuilder('operation')
            ->select('sum(operation.valueUAH)')
            ->where('operation.valueUAH > 0')
            ->andWhere('operation.date >= (:startDate)')
            ->andWhere('operation.date <= (:endDate)')
            ->setParameters(['startDate' => $start,
                'endDate' => $end])
            ->getQuery()
            ->getResult();
        ;
        $revenues = (float)$revenues[0][1];

        $costs = $this->createQueryBuilder('operation')
            ->select('sum(operation.valueUAH)')
            ->where('operation.valueUAH < 0')
            ->andWhere('operation.date >= (:startDate)')
            ->andWhere('operation.date <= (:endDate)')
            ->setParameters(['startDate' => $start,
                'endDate' => $end])
            ->getQuery()
            ->getResult();
        ;
        $costs = (float)$costs[0][1];

        $total = $revenues + $costs;
        $data = ['total'=>$total,
                'revenues'=>$revenues,
                'costs'=>$costs,
                'operations'=>$operations];

        return $data;
    }
}