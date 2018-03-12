<?php
namespace Chapagain\AutoCurrency\Block;

class Currency extends Magento\Directory\Block\Currency
{
    /**
     * Retrieve Current Currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        echo "getCurrentCurrencyCode() in app code<br>";
        $this->setData('current_currency_code', $this->_storeManager->getStore()->getCurrentCurrency()->getCode());
        return $this->_getData('current_currency_code');
    }
}
