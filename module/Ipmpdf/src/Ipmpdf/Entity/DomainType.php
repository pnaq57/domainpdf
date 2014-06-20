<?php
namespace Ipmpdf\Entity;

class DomainType
{
    const DOMAIN_CODE_PREFIX = 'PCODE:DOMAIN';
    const DOMAIN_CODE_POSTFIX = 'STAND';
    const DOMAIN_CODE_TYPE_YEAR_FEE = 'JAHRGEB';
    const DOMAIN_CODE_TYPE_TRANSFER = 'TRANSFR';
    const DOMAIN_CODE_TYPE_RESTORE = 'RESTORE';
    const DOMAIN_CODE_TYPE_RESGISTER = 'REGISTR';
    
    const DOMAIN_ASCIO_CODE_TYPE_AUTORENEW = 'AutoRenewal';
    const DOMAIN_ASCIO_CODE_TYPE_RESGISTER = 'Registrant Details Update';
    
    /*
     * @var array PCODE with index like PCODE:DOMAIN:AG:JAHRGEB:STAND
     */
    protected $PCODE = array();




    public static $mapDomainTypes = array(
        self::DOMAIN_CODE_TYPE_RESGISTER => 'REG',
        self::DOMAIN_CODE_TYPE_RESTORE => 'UNKNOW',
        self::DOMAIN_CODE_TYPE_TRANSFER => 'KK',
        self::DOMAIN_CODE_TYPE_YEAR_FEE => 'REG',
        
    );
    
    /*
     * @param string $tld like DE|COM
     * @param string $type like KK|REG
     */
    public function getPCode($tld, $type = 'KK', $db, $domainService)
    {
        $tld = strtoupper($tld);
        $type = strtoupper($type);
        $code = self::DOMAIN_CODE_PREFIX . ':' . $tld . ':' . self::DOMAIN_CODE_TYPE_YEAR_FEE . ':' . self::DOMAIN_CODE_POSTFIX;
        if ($type == self::$mapDomainTypes[self::DOMAIN_CODE_TYPE_TRANSFER]) {
            $code = self::DOMAIN_CODE_PREFIX . ':' . $tld . ':' .  self::DOMAIN_CODE_TYPE_TRANSFER . ':' . self::DOMAIN_CODE_POSTFIX;
        } elseif ($type == self::$mapDomainTypes[self::DOMAIN_CODE_TYPE_RESGISTER]) {
            $code = self::DOMAIN_CODE_PREFIX . ':' . $tld . ':' .  self::DOMAIN_CODE_TYPE_RESGISTER . ':' . self::DOMAIN_CODE_POSTFIX;
        }
       
        if (isset($this->PCODE[$code])) {
            return $this->PCODE[$code];
        }
        $selectSql = '
            SELECT vendor_part_num, discount_price
            FROM product_templates
            WHERE vendor_part_num = "' . $code . '"';                
        $conn = $db->getConnection();
        $result= $conn->query($selectSql)->fetchAll();
        if (is_array($result) && count($result) > 0) {
            $result = reset($result);
            $this->PCODE[$result['vendor_part_num']] = $result['discount_price'];
            return $this->PCODE[$result['vendor_part_num']];
        }
        $domainService->addMessage('PCODE: "<span class="colorText">' . $code . '</span>" nicht gefunden!');
        return false;
    }
    
}
