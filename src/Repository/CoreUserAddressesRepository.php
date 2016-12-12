<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Checkout\Repository;

/**
 * Description of CoreUserAddressesRepository
 *
 * @author dev
 */
use Doctrine\ORM\EntityRepository;
use Adteam\Core\Checkout\Entity\CoreUserAddresses;

class CoreUserAddressesRepository extends EntityRepository{
    
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
    public function updateUserAddresses($params)
    {
        if($this->exist($params)&&isset($params['data']->userAddress)){
            $userAddress =$params['data']->userAddress;
            $update = $this->createQueryBuilder('o')
            ->update(CoreUserAddresses::class,'o');
            foreach ($userAddress as $key=>$value){
                $update->set('o.'.$key,':'.$key)  
                ->setParameter($key, $value);
            }     
            $update->where('o.user = :user_id')
            ->setParameter('user_id', $params['identity']['id'])
            ->getQuery()->execute();  
        }
    }
}
