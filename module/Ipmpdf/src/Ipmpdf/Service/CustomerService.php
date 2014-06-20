<?php
namespace Ipmpdf\Service;
use Ipmpdf\Collection\Customers as CustomerCollection;
use Ipmpdf\Entity\Customer;
use Ipmpdf\Entity\Domain;
use Ipmpdf\Entity\DomainType;
/**
 * Description of CustomerService
 *
 * @author aqnguyen
 */
class CustomerService
{
    /*
     * db from sugar
     */
    protected $db;
    
    protected $dba;
    
    protected $currentTime;
    
    protected $currentYear;


    protected $messages = array();


    public function __construct($db, $dba)
    {
        $this->db = $db;
        $this->dba = $dba;
        $this->currentTime = time();
        $this->currentYear = date('Y', $this->currentTime);
    }

    
    public function generateCustomers(array $data, DomainService $domainService)
    {
        if (empty($data)) {
            return false;
        }
        $firstRow = array_keys(reset($data));
        if ($firstRow[0] == 'Domain') {
            return $this->mapDataFromAscio($data, $domainService);
        } else {
            return $this->mapDataFromPartnerGate($data, $domainService);
        }
        
    }
    
    /*
     * @param \Ipmpdf\Entity\Domain $domain
     * @param \Ipmpdf\Service\DomainService $domainService
     */
    protected function setPrice($domain, $domainService)
    {
        $firstFee = false;
        $yearlyFee = false;
        // handle KK type
        if ($domain->getType() == DomainType::$mapDomainTypes[DomainType::DOMAIN_CODE_TYPE_TRANSFER]) {
            // ist nicht wirklich KK, weil die Abrechnungsjahr > RegJahr
            if (date('Y', $domain->getInvoiceStart()) > date('Y', $domain->getRegDate())) {
                $yearlyFee = true;
            } else {
                $firstFee = true;
                $yearlyFee = true;
            }
        } else {
            // handle REG type
            if (date('Y', $domain->getInvoiceStart()) > date('Y', $domain->getRegDate())) {
                $yearlyFee = true;
            } else {
                $firstFee = true;
                $yearlyFee = true;
            }
        }
        
        if ($yearlyFee) {
            $price = $domainService->getPriceByPCode(
                $domain->getTld(),
                DomainType::DOMAIN_CODE_TYPE_YEAR_FEE
            );
            $domain->setYearlyPrice($price);
        }
        
        if ($firstFee) {
            $price = $domainService->getPriceByPCode(
                $domain->getTld(),
                $domain->getType()
            );
            $domain->setRegPrice($price);
        }
    }

    public function updateCustomer($customers)
    {
        $customers = $customers->getCustomers();
        $ids = array_keys($customers);
        $ids = implode("', '", $ids);
        $selectSql = "
            SELECT name, icc_customer_id
            FROM accounts
            WHERE icc_customer_id IN ('" . $ids . "')";
        $conn = $this->db->getConnection();
        $result = $conn->query($selectSql)->fetchAll();
        if (is_array($result) && count($result) > 0) {
            foreach ($result as $val) {
                $customers[$val['icc_customer_id']]->setName($val['name']);
            }
        }
    }
    
    protected function getCustomerByDomains(array $domains)
    {
        $domains = implode("', '", $domains);
        $selectSql = "
            SELECT DOMAIN, FIRMA, KDNR
            FROM DOMAINS
            WHERE DOMAIN IN ('" . $domains . "')
            ORDER BY REGDATUM DESC";

        $conn = $this->dba->getConnection();
        $result = $conn->query($selectSql)->fetchAll();
        $customers = array();
        if (is_array($result) && count($result) > 0) {
            foreach ($result as $val) {
                $customers[$val['DOMAIN']] = array(
                    'name' => $val['FIRMA'],
                    'customerId' => $val['KDNR']
                );
            }
            return $customers;
        }
        return false;
    }

    public function mapDataFromPartnerGate($data, $domainService)
    {
        $cusCol = new CustomerCollection();
        foreach ($data as $key => $val) {
            // only handle KK or REG
            if (
                $val['Art'] != DomainType::$mapDomainTypes[DomainType::DOMAIN_CODE_TYPE_TRANSFER]
                && $val['Art'] != DomainType::$mapDomainTypes[DomainType::DOMAIN_CODE_TYPE_RESGISTER]
            ) {
                continue;
            }
            if ($val['Reseller-ID'] == 'ipandmoreintern') {
                $val['Reseller-ID'] = 'intern';
            }
            if (!$cusCol->existById($val['Reseller-ID'])) {
                $customer = new Customer();
                $customer->setId($val['Reseller-ID']);
                $cusCol->addCustomer($customer);
            }            
            $domain = new Domain();
            $domain->setName($val['Domain-Name']);
            $domain->setInvoiceEnd($val['Abrechn.zeitr. Ende']);
            $domain->setInvoiceStart($val['Abrechn.zeitr. Anfang']);
            $domain->setOrderDate($val['Auftrag vom']);
            $domain->setRegDate($val['Reg.Datum']);
            $domain->setType($val['Art']);            
            $domain->setRegFee($val['Einricht.gebuehr']);
            $domain->setYearlyFee($val['Jahresgebuehr']);
            $this->setPrice($domain, $domainService);
            $cusCol->addDomainByCustomerId($val['Reseller-ID'], $domain);
        }
        $this->updateCustomer($cusCol);
        return $cusCol;
    }

    public function mapDataFromAscio($data, $domainService)
    {
        $cusCol = new CustomerCollection();
        $dommains = array();
        foreach ($data as $key => $val) {
            $domainName = trim($val['Domain']);
            $dommains[] = $domainName;
            $data[$domainName] = $val;
            unset($data[$key]);
        }
        $dommainCustomer = $this->getCustomerByDomains($dommains);
        foreach ($dommainCustomer as $key => $value) {
            $data[$key]['Customer'] =  $value;
        }
        foreach ($data as $key => $val) {
            if (
                $val['Service'] != DomainType::DOMAIN_ASCIO_CODE_TYPE_AUTORENEW
                && $val['Service'] != DomainType::DOMAIN_ASCIO_CODE_TYPE_RESGISTER
            ) {
                $this->messages[] = 'Rechnungstyp "' . $val['Service'] . '" von ' . $key . ' konnte nicht behandelt werden!';
                continue;
            }
            if (!isset($val['Customer']['customerId'])) {
                $this->messages[] = 'Es wird kein Kunde von dem Domain "<span class="colorText">' . $key . '</span>" gefunden';
            }
            if (!$cusCol->existById($val['Customer']['customerId'])) {
                $customer = new Customer();
                $customer->setId($val['Customer']['customerId']);
                $cusCol->addCustomer($customer);
            }            
            $domain = new Domain();
            $domain->setName($key);

            $domain->setRegDate($val['RegistrationDate']);
            if ($val['Service'] == DomainType::DOMAIN_ASCIO_CODE_TYPE_RESGISTER) {
                $domain->setType(DomainType::$mapDomainTypes[DomainType::DOMAIN_CODE_TYPE_RESGISTER]);
                $domain->setRegFee($val['Cost']);
            } else {
                $domain->setYearlyFee($val['Cost']);
            }

            $this->setAscioPrice($domain, $domainService);
            $cusCol->addDomainByCustomerId($val['Customer']['customerId'], $domain);
        }
        $this->updateCustomer($cusCol);
        return $cusCol;
    }

    public function setAscioPrice($domain, $domainService)
    {
        $firstFee = false;
        $yearlyFee = false;
        // handle KK type
        if ($domain->getType() == DomainType::$mapDomainTypes[DomainType::DOMAIN_CODE_TYPE_RESGISTER]) {
            // ist nicht wirklich KK, weil die Abrechnungsjahr > RegJahr
            if (date('Y', $domain->getRegDate()) < $this->currentYear) {
                $yearlyFee = true;
            } else {
                $firstFee = true;
                $yearlyFee = true;
            }
        } else {
            $yearlyFee = true;
        }
        
        if ($yearlyFee) {
            $price = $domainService->getPriceByPCode(
                $domain->getTld(),
                DomainType::DOMAIN_CODE_TYPE_YEAR_FEE
            );
            $domain->setYearlyPrice($price);
        }
        
        if ($firstFee) {
            $price = $domainService->getPriceByPCode(
                $domain->getTld(),
                $domain->getType()
            );
            $domain->setRegPrice($price);
        }
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }


    


}
