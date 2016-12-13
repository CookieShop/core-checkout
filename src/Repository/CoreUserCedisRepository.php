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
use Adteam\Core\Checkout\Entity\OauthUsers;
use Adteam\Core\Checkout\Entity\CoreCedis;

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
        if($this->exist($params)&&isset($params['data']->cedis)){
            $cedis =$params['data']->cedis;
            $this->createQueryBuilder('o')
            ->update(CoreUserCedis::class,'o')
            ->set('o.cedis',':cedis')  
            ->setParameter('cedis', $cedis)
            ->where('o.user = :user_id')
            ->setParameter('user_id', $params['identity']['id'])
            ->getQuery()->execute();  
        }elseif(isset($params['data']->cedis)){
            $cedis =$params['data']->cedis;
            $this->insert($cedis, $params);
        }
    }
    
    private function insert($cedis,$params)
    {
        $user= $this->_em->getReference(
                OauthUsers::class, $params['identity']['id']); 
        $cedi= $this->_em->getReference(
                CoreCedis::class, $cedis);         
        $CoreUserCedis = new CoreUserCedis();
        $CoreUserCedis->setUser($user);
        $CoreUserCedis->setCedis($cedi);
        $this->_em->persist($CoreUserCedis);
        $this->_em->flush();          
    }    
}
