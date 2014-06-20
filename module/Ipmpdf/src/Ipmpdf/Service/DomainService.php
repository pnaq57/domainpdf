<?php
namespace Ipmpdf\Service;
/**
 * Description of DomainService
 *
 * @author aqnguyen
 */
class DomainService
{
    protected $sugarDb;
    protected $dbaDb;
    
    protected $messages = array();


    /*
     * @var \Ipmpdf\Entity\DomainType $domainType
     */
    protected $domainType;
    
    public function __construct()
    {
        $this->getDomainType();
    }

    public function getDomainType()
    {
        if (!isset($this->domainType)) {
            $this->domainType = new \Ipmpdf\Entity\DomainType();
        }
        return $this->domainType;
    }

    public function setDomainType($domainType)
    {
        $this->domainType = $domainType;
    }

    
    public function getPriceByPCode($tld, $type)
    {
        return $this->domainType->getPCode($tld, $type, $this->sugarDb, $this);
    }
    
    public function getSugarDb()
    {
        return $this->sugarDb;
    }

    public function getDbaDb()
    {
        return $this->dbaDb;
    }

    public function setSugarDb($sugarDb)
    {
        $this->sugarDb = $sugarDb;
    }

    public function setDbaDb($dbaDb)
    {
        $this->dbaDb = $dbaDb;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function addMessage($msg)
    {
        if (!in_array($msg, $this->messages)) {
            $this->messages[] = $msg;
        }
    }
}
