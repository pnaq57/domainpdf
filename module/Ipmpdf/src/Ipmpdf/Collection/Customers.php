<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ipmpdf\Collection;

/**
 * Description of Customers
 *
 * @author aqnguyen
 */
class Customers
{
    protected $customers = array();
    protected $customersPrice = 0.00;
    
    public function addCustomer(\Ipmpdf\Entity\Customer $customer)
    {
        $this->customers[$customer->getId()] = $customer;
    }
    
    public function removeCustomer(\Ipmpdf\Entity\Customer $customer)
    {
        if (isset($this->customers[$customer->getId()])) {
            unset($this->customers[$customer->getId()]);
        }
    }
    
    public function existById($id)
    {
        if (isset($this->customers[$id])) {
            return true;
        }
        return false;
    }
    
    public function addDomainByCustomerId($id, \Ipmpdf\Entity\Domain $domain)
    {
        if (isset($this->customers[$id])) {
            $this->customers[$id]->addDomain($domain);
            return;
        }
        throw new \Exception('Customer not exist by id ' . $id);
    }
    
    public function getCustomers()
    {
        return $this->customers;
    }

    public function setCustomers($customers)
    {
        $this->customers = $customers;
    }
    
    public function getTotalPriceFromAll()
    {
        if ($this->customersPrice != 0.00) {
            return $this->customersPrice;
        }
        foreach ($this->customers as $key => $customer) {
            $this->customersPrice += $customer->getAllDomainPrice();
        }
        return $this->customersPrice;
    }


}
