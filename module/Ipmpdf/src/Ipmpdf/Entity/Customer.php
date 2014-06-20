<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Ipmpdf\Entity;

/**
 * Description of Customer
 *
 * @author aqnguyen
 */
class Customer
{
    protected $id;
    protected $name;
    protected $invoiceNr;
    protected $domains = array();
    protected $allDomainPrice;


    public function addDomain(Domain $domain)
    {
        $this->domains[$domain->getName()] = $domain;
    }
    
    public function removeDomain(Domain $domain)
    {
        if (isset($this->domains[$domain->getName()])) {
            unset($this->domains[$domain->getName()]);
        }
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        if ($id == 'ipandmoreintern') {
            $id = 'intern';
        }
        $this->id = $id;
    }
    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getAllDomainPrice()
    {
        $this->allDomainPrice = 0.00;
        foreach ($this->domains as $domain) {
            $this->allDomainPrice += $domain->getTotalPrice();
        }
        return $this->allDomainPrice;
    }

    public function getInvoiceNr()
    {
        if (!isset($this->invoiceNr)) {
            $this->invoiceNr = date('mYHi', time());
        }
        return $this->invoiceNr;
    }

    public function getDomains()
    {
        return $this->domains;
    }



}
