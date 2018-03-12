<?php
namespace Chapagain\AutoCurrency\Block;

class Currency extends \Magento\Directory\Block\Currency
{
    public function getCurrentCurrencyCode()
    {
//        if ($this->_getData('current_currency_code') === null) {
//            // do not use $this->_storeManager->getStore()->getCurrentCurrencyCode() because of probability
//            // to get an invalid (without base rate) currency from code saved in session
//            $this->setData('current_currency_code', $this->_storeManager->getStore()->getCurrentCurrency()->getCode());
//        }
//        return $this->_getData('current_currency_code');
        $this->setData('current_currency_code', $this->_storeManager->getStore()->getCurrentCurrency()->getCode());
        return $this->_getData('current_currency_code');
    }
}

