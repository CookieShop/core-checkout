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
use Adteam\Core\Checkout\Entity\OauthUsers;

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
        }elseif(isset($params['data']->userAddress)){
            $userAddress =$params['data']->userAddress;
            $this->insert($userAddress, $params);
        }
    }
    
    private function insert($userAddress,$params)
    {
        $user= $this->_em->getReference(
                OauthUsers::class, $params['identity']['id']); 
        $CoreUserAddresses = new CoreUserAddresses();
        foreach ($userAddress as $key=>$value){
            $CoreUserAddresses->setUser($user);
            $CoreUserAddresses->setMain(1);
            if (method_exists($CoreUserAddresses, 'set'.ucfirst($key))) {                
                $CoreUserAddresses->{'set'.ucfirst($key)}($value);
            }else{
                throw new \InvalidArgumentException(
                    'Missing_Fields_In_Survey_Json'); 
            }            
        }
        $this->_em->persist($CoreUserAddresses);
        $this->_em->flush();          
    }
}
