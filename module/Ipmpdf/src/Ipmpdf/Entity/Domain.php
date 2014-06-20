<?php
namespace Ipmpdf\Entity;

class Domain
{
    protected $name;
    protected $orderDate;
    protected $regDate;
    protected $invoiceStart;
    protected $invoiceEnd;
    protected $type = 'Y';
    protected $totalPrice;


    /*
     * @var float $yearlyFee from csv
     */
    protected $yearlyFee = 0.00;
    
    /*
     * @var float $regFee from csv
     */
    protected $regFee = 0.00;
    
    /*
     * @var float $yearlyPrice from sugar
     */
    protected $yearlyPrice = 0.00;
    
    /*
     * @var float $regPrice from sugar
     */
    protected $regPrice = 0.00;
    
    public function getType()
    {
        return $this->type;
    }

    public function getYearlyPrice()
    {
        return $this->yearlyPrice;
    }

    public function getRegPrice()
    {
        return $this->regPrice;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setYearlyPrice($yearlyPrice)
    {
        $this->yearlyPrice = floatval($yearlyPrice);
    }

    public function setRegPrice($regPrice)
    {
        $this->regPrice = floatval($regPrice);
    }

    
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = trim($name);
    }

    public function getOrderDate()
    {
        return $this->orderDate;
    }

    public function getRegDate()
    {
        return $this->regDate;
    }

    public function getInvoiceStart()
    {
        return $this->invoiceStart;
    }

    public function getInvoiceEnd()
    {
        
        return $this->invoiceEnd;
    }

    public function getTld()
    {
        $tld = explode('.', $this->name);
        $tld = end($tld);
        return $tld;
    }

    public function getYearlyFee()
    {
        return $this->yearlyFee;
    }

    public function getRegFee()
    {
        return $this->regFee;
    }

    public function setOrderDate($orderDate)
    {
        $this->orderDate = strtotime($orderDate);
    }

    public function setRegDate($regDate)
    {
        $this->regDate = strtotime($regDate);
    }

    public function setInvoiceStart($invoiceStart)
    {
        $this->invoiceStart = strtotime($invoiceStart);
    }

    public function setInvoiceEnd($invoiceEnd)
    {
        $this->invoiceEnd = strtotime($invoiceEnd);
    }

    public function setYearlyFee($yearlyFee)
    {
        $this->yearlyFee = floatval(str_replace(',', '.', $yearlyFee));
    }

    public function setRegFee($regFee)
    {
        $this->regFee = floatval(str_replace(',', '.', $regFee));
    }

    public function getTotalPrice()
    {
        $this->totalPrice = floatval($this->regPrice) + floatval($this->yearlyPrice);
        return $this->totalPrice;
    }

   

}