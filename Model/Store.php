<?php
namespace Chapagain\AutoCurrency\Model;

use Magento\Framework\App\ObjectManager;

class Store extends \Magento\Store\Model\Store
{
    public function getDefaultCurrency()
    {
        $currency = $this->getData('default_currency');
        $currency = $this->getDefaultCurrencyCode();
        $this->setData('default_currency', $currency);
        return $currency;
    }
    /**
     * Update default store currency code
     *
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        $helper = ObjectManager::getInstance()->get('Chapagain\AutoCurrency\Helper\Data');
        if ($helper->isEnabled()) {
            //$result = parent::getDefaultCurrencyCode();
            //return $this->getCurrencyCodeByIp('');
            if(empty($this->getCurrencyCodeByIp(''))){
                return "CAD";
            }else{
                return $this->getCurrencyCodeByIp('');
            }
        } else {
            return parent::getDefaultCurrencyCode();
        }
    }

    /**
     * Retrieve store current currency
     *
     * @return Currency
     */
    public function getCurrentCurrency()
    {
        $currency = $this->getData('current_currency');
        if ($currency === null) {
            $currency = $this->currencyFactory->create()->load($this->getCurrentCurrencyCode());
            $baseCurrency = $this->getBaseCurrency();
            if (!$baseCurrency->getRate($currency)) {
                $currency = $baseCurrency;
                $this->setCurrentCurrencyCode($baseCurrency->getCode());
            }
            $this->setData('current_currency', $currency);
        }
        return $currency;
    }

    /**
     * Get Currency code by IP Address
     *
     * @return string
     */
    public function getCurrencyCodeByIp($result = '')
    {
        $currencyCode = $this->getCurrencyCodeIp2Country();

        // if currencyCode is not present in allowedCurrencies
        // then return the default currency code
        $currencyModel = ObjectManager::getInstance()->get('Magento\Directory\Model\Currency');
        $allowedCurrencies = $currencyModel->getConfigAllowCurrencies();
        //$allowedCurrencies = $this->currencyFactory->getConfigAllowCurrencies();
        if(!in_array($currencyCode, $allowedCurrencies)) {
            return $result;
        }

        return $currencyCode;
    }

    /**
     * Get Currency code by IP Address
     * Using Ip2Country Database
     *
     * @return string
     */
    public function getCurrencyCodeIp2Country()
    {
        $helper = ObjectManager::getInstance()->get('Chapagain\AutoCurrency\Helper\Data');

        // load Ip2Country database
        //$ipc = $helper->loadIp2Country();

        // get IP Address
        $ipAddress = $helper->getIpAddress();

        // additional valid ip check
        // because Ip2Country generates error for invalid IP address
        if (!$helper->checkValidIp($ipAddress)) {
            return null;
        }

        //$countryCode = $ipc->lookup($ipAddress);
        //------------------------lookup countryCode with api----------------------------------------
        $abmIPAddress = array("184.67.239.26",
            "184.67.248.98",
            "184.67.242.51",
            "184.67.242.52",
            "184.67.242.53",
            "184.67.242.50",
            "184.67.242.42",
            "184.67.242.43",
            "184.67.242.44",
            "184.67.242.45",
            "174.7.0.36",
            "174.7.4.110"
        );
        if(in_array($ipAddress,$abmIPAddress)){
            $countryCode = "US";
            $countryName = "United States";
        }else{
            $countryHttpUrl = "https://freegeoip.net/json/$ipAddress";
            $countryHttpJson = file_get_contents($countryHttpUrl);
            if($countryHttpJson){
                $countryHttpArray = json_decode($countryHttpJson,TRUE);
                $countryCode = $countryHttpArray["country_code"];
                $countryName = $countryHttpArray['country_name'];
            }else{
                $countryCode = "CA";
                $countryName = "Canada";
            }
        }
        //------------------------lookup countryCode with api--------------------------------------------
        if($countryCode == 'US' || $countryCode == 'CN'){
            $currencyCode = 'USD'; // 1
        }else if($countryCode == 'CA'){
            $currencyCode = 'CAD'; //1.15
        }else{//other countries besides US, CN, CA
            $currencyCode = 'CNY';  //1.5
        }

//		// return default currency code when country code is ZZ
//		// i.e. if browsed in localhost / personal computer
//		if ($countryCode == 'ZZ') {
//			$currencyCode = parent::getDefaultCurrencyCode();
//		} else {
//			$currencyCode = $helper->getCurrencyByCountry($countryCode);
//		}
//
        return $currencyCode;
    }
}

?>
