<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Adteam\Core\Checkout\Checkout;

use Adteam\Core\Checkout\Entity\CoreConfigs;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceManager;
use Adteam\Core\Checkout\Entity\OauthUsers;
use Adteam\Core\Checkout\Entity\CoreSurveyQuestions;

/**
 * Description of Steps
 *
 * @author dev
 */
class Steps {
    
    /**
     *
     * @var type 
     */
    protected $service;
    
    /**
     *
     * @var type 
     */
    protected $identity;
    
    /**
     * 
     * @param ServiceManager $service
     */
    public function __construct(ServiceManager $service) {
        $this->service = $service;
        $this->identity = $this->service->get('authentication')
                ->getIdentity()->getAuthenticationIdentity(); 
    }

    /**
     * Obtiene usuario mediante username
     * 
     * @param type $username
     * @return type
     */
    public function getUserByUsername($username)
    {
        $em = $this->service->get(EntityManager::class);
        return $em->getRepository(OauthUsers::class)
                ->createQueryBuilder('U')
                ->select('U.id,U.displayName,U.username')
                ->where('U.username = :username')
                ->setParameter('username', $username)
                ->getQuery()->getSingleResult();
    }  
    
    /**
     * ['none'|'fulfill'] Debe o no llenar encuesta
     * ['user-address' | 'cedis'| 'none'] CEDIS o Direccion del usuario o nada
     * 
     * @return type
     */
    public function getSettings()
    {
        $result = $this->getConfigs();
        return [
            'settings'=>[
                'surveyMode'=>$this->hasAnswered($result['survey.isMandatory']),
                'deliveryMode'=> $this->hasEditable($result),
                ]
        ];
    }
    
    /**
     * 
     * @param type $configs
     * @return type
     */
    private function hasEditable($configs)
    {
        return $configs['checkout.delivery.userEditable']==='editable'?
                $configs['checkout.delivery.mode']:'none';
    }
    
    /**
     * 
     * @return type
     */
    public function getConfigs()
    {
        $em = $this->service->get(EntityManager::class);
         return $em->getRepository(CoreConfigs::class)->getConfig();         
    }
    
    /**
     * 
     * @param type $userId
     * @return type
     */
    private function getSurvey($userId)
    {
        $em = $this->service->get(EntityManager::class);
        return $em->getRepository(CoreSurveyQuestions::class)
                ->createQueryBuilder('U')
                ->select('U.id,R.id')
                ->innerJoin('U.user', 'R')
                ->where('R.id = :userId')
                ->setParameter('userId', $userId)
                ->getQuery()->getResult();        
    }

    /**
     * 
     * @param type $surveyMode
     * @return type
     */
    private function hasAnswered($surveyMode)
    {
        $identity = $this->getUserByUsername($this->identity['user_id']);       
        $result = $this->getSurvey($identity['id']);
        return $surveyMode==='1'&&count($result)===0?'fulfill':'none';
    }
}
