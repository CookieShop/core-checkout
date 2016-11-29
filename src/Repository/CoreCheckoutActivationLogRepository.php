<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Checkout\Repository;

use Doctrine\ORM\EntityRepository;
use Adteam\Core\Checkout\Entity\OauthUsers;
use Adteam\Core\Checkout\Entity\CoreCheckoutActivationLog;

/**
 * 
 */
class CoreCheckoutActivationLogRepository extends EntityRepository
{
   
    /**
     * Fetch all config values
     * 
     * @return array
     */
    public function fetchLast()
    {
       $result = $this->createQueryBuilder('B')
               ->select("B.id, DATE_FORMAT(B.createdAt,'%d-%m-%Y %H:%i:%s'), R.displayName, B.status")
//               ->select("B.id, B.createdAt, R.id as userId, R.displayName, B.status")
               ->innerJoin('B.requestedBy', 'R')
               ->orderBy('B.createdAt','DESC')
               ->getQuery()->getResult();
        if(isset($result[0])){
            return $result[0];
        }
           throw new \InvalidArgumentException(
                'Inicializar_canje'); 
    }
    
    public function create($data,$identity)
    {
        $user = $this->getUserByUsername($identity['user_id']);
        $lastcreated = $this->fetchLast();

        $enabled = (boolean)$data->enabled;
        if($enabled===$lastcreated['status']){            
           $activo = $lastcreated['status']?'canje activo':'canje cerrado';
           throw new \InvalidArgumentException($activo);             
        }
        $this->insertLog($user, $enabled);
        return $lastcreated['status']?false:true;
    }
    
    public function getUserByUsername($username='testuser')
    {
        return $this->_em->getRepository(OauthUsers::class)
                ->createQueryBuilder('U')
                ->select('U.id,U.displayName')
                ->where('U.username = :username')
                ->setParameter('username', $username)
                ->getQuery()->getSingleResult();
    }
    
    public function insertLog($user,$status)
    {
        $this->_em->transactional(
            function ($em) use($status,$user) {
                $log = new CoreCheckoutActivationLog();
                $UserReference = $em->getReference(
                            OauthUsers::class, $user['id']);

                $log->setRequestedBy($UserReference);
                $log->setStatus($status);
                $em->persist($log);
            }
        );        
        
    }
}