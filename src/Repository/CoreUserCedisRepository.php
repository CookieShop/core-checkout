<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Checkout\Repository;

/**
 * Description of CoreUserCedisRepository
 *
 * @author dev
 */
use Doctrine\ORM\EntityRepository;
use Adteam\Core\Checkout\Entity\CoreUserCedis;

class CoreUserCedisRepository extends EntityRepository{
    
    /**
     * 
     * @param type $params
     * @return type
     */
    private function exist($params)
    {
        $resultSet = $this->createQueryBuilder('T')
            ->select('COUNT(T.id) as contador')
            ->where('T.user = :user_id')
            ->setParameter('user_id', $params['identity']['id'])
            ->getQuery()->getSingleResult();  
        return (boolean) $resultSet['contador'];
    }
    
    /**
     * 
     * @param type $params
     */
    public function updateUserCedis($params)
    {
        if($this->exist($params)){
            $cedis =$params['data']->cedis;
            $this->createQueryBuilder('o')
            ->update(CoreUserCedis::class,'o')
            ->set('o.cedis',':cedis')  
            ->setParameter('cedis', $cedis)
            ->where('o.user = :user_id')
            ->setParameter('user_id', $params['identity']['id'])
            ->getQuery()->execute();  
        }
    }
}
