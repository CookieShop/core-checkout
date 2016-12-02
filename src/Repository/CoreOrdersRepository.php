<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Adteam\Core\Checkout\Repository;

use Adteam\Core\Checkout\Entity\CoreOrders;
use Adteam\Core\Checkout\Entity\OauthUsers;
use Doctrine\ORM\EntityRepository;
use Adteam\Core\Checkout\Entity\CoreOrderProducts;
use Adteam\Core\Checkout\Entity\CoreProducts;
use Adteam\Core\Checkout\Entity\CoreOrderAddressses;
use Adteam\Core\Checkout\Entity\CoreUserCartItems;
use Adteam\Core\Checkout\Entity\CoreUserTransactions;

/**
 * Description of CoreOrdersRepository
 *
 * @author dev
 */
class CoreOrdersRepository extends EntityRepository 
{
    //put your code here
    public function create($params,$data)
    {
        $currentRepo = $this;        
        return $this->_em->transactional(
            function ($em) use($currentRepo,$params,$data) {

            $userId = $this->_em->getReference(
                    OauthUsers::class, $params['identity']['id']);
                $coreorders = new CoreOrders();
                $coreorders->setUser($userId);
                $coreorders->setTotal($params['totalcart']);
                $coreorders->setRevision(1);
                $em->persist($coreorders); 
                $em->flush();
                $id = $coreorders->getId();
                $currentRepo->insertCoreProducts($id, $params);
                $currentRepo->insertCoreAdresess($id, $params);
                $currentRepo->emptyCart($params); 
                $currentRepo->insertTransaction($params, $id);
                return $id;
            }
        );        
    }
    
    public function insertCoreProducts($idOrder,$params)
    {
        $order= $this->_em->getReference(CoreOrders::class, $idOrder);
        $coreorderproducts = new CoreOrderProducts();
        foreach ($params['cart'] as $items){
            foreach ($items as $key=>$value){
                $coreorderproducts->setOrder($order);
                $product = $this->_em->getReference(
                        CoreProducts::class, $items['product']);
                $coreorderproducts->setProduct($product);
                if (method_exists($coreorderproducts, 'set'.ucfirst($key))
                        &&$key!=='id'&&$key!=='product') {                
                    $coreorderproducts->{'set'.ucfirst($key)}($value);
                }                
            }
        }
        $this->_em->persist($coreorderproducts);
        $this->_em->flush();
    }
    
    public function insertCoreAdresess($idOrder,$params)
    {
        $user= $this->_em->getReference(
                OauthUsers::class, $params['identity']['id']);
        $order= $this->_em->getReference(CoreOrders::class, $idOrder);
        $corecrderaddressses = new CoreOrderAddressses();
        foreach ($params['data']->userAddress as $key=>$value){
            $corecrderaddressses->setOrder($order);
            $corecrderaddressses->setUser($user);

            if (method_exists($corecrderaddressses, 'set'.ucfirst($key))) {                
                $corecrderaddressses->{'set'.ucfirst($key)}($value);
            }
            else{
                throw new \InvalidArgumentException(
                'Faltan campos en Json de Direcciones'); 
            }                
        }
        $this->_em->persist($corecrderaddressses);
        $this->_em->flush();        
    }
    
    public function emptyCart($params)
    {
        $this->_em->getRepository(CoreUserCartItems::class)->remove($params);
    }
    
    public function insertTransaction($params,$orderId)
    {
        $user= $this->_em->getReference(
                OauthUsers::class, $params['identity']['id']);
        $snap = $this->getBalanceSnapshop($params);   
        $CoreUserTransactions =  new CoreUserTransactions();
        $CoreUserTransactions->setUser($user);
        $CoreUserTransactions->setAmount(0-$params['totalcart']);
        $CoreUserTransactions->setType(CoreUserTransactions::TYPE_ORDER);
        $CoreUserTransactions->setCorrelationId($orderId);
        $CoreUserTransactions->setBalanceSnapshot($snap);
        $this->_em->persist($CoreUserTransactions);
        $this->_em->flush();        
    }
    
    public function getBalanceSnapshop($params)
    {
        return $this->_em->getRepository(CoreUserTransactions::class)
                ->getBalanceSnapshot($params['identity']['id']);
    }
    
}
