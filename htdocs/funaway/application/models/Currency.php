<?php

class Currency extends MyAppModel
{

    const DB_TBL = 'tbl_currency';
    const DB_TBL_PREFIX = 'currency_';
	
    const DEFAULT_CURRENCY_CODE = 'USD';
    const DEFAULT_CURRENCY_SYMBOL = '$';

    static $defaultCurrencyId = 2;

    public function __construct($recordId = 0)
    {
        $recordId = FatUtility::int($recordId);
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $recordId);
        $this->objMainTableRecord->setSensitiveFields(array());
    }

    public static function getSearchObject()
    {
        $srch = new SearchBase(static::DB_TBL);
        return $srch;
    }

    public static function displayDefaultPrice($price, $isDisplaySymbol = true)
    {
        $srch = Self::getSearchObject();
        $srch->addCondition('currency_id', '=', self::getDefaultId());
		
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);

        $currency = $record['currency_symbol'].' ';
		
        if (!$isDisplaySymbol) {
            $currency = $record['currency_code'].' ';
        }
        $pricestring = '{leftsymbol}{price}{rightsymbol}';
        if ($record['currency_symbol_location'] == 1) {
            $pricestring = str_replace('{leftsymbol}', $currency, $pricestring);
            $pricestring = str_replace('{rightsymbol}', '', $pricestring);
        } else {
            $pricestring = str_replace('{rightsymbol}', $currency, $pricestring);
            $pricestring = str_replace('{leftsymbol}', '', $pricestring);
        }
        //$pricestring = str_replace('{price}',money_format("%i",$price*$record['currency_rate']),$pricestring);
        $formatedprice = str_replace(".00", '', number_format($price * $record['currency_rate'], 2));
        $pricestring = str_replace('{price}', $formatedprice, $pricestring);
        return $pricestring;
    }

    public static function displayPrice($price, $isDisplaySymbol = true)
    {
        $srch = Self::getSearchObject();
        $srch->addCondition('currency_id', '=', Info::getCurrentCurrency());
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);

        $currency = $record['currency_symbol'].' ';
		
        if (!$isDisplaySymbol) {
            $currency = $record['currency_code'].' ';
        }
		
        $pricestring = '{leftsymbol}{price}{rightsymbol}';
        if ($record['currency_symbol_location'] == 1) {
            $pricestring = str_replace('{leftsymbol}', $currency, $pricestring);
            $pricestring = str_replace('{rightsymbol}', '', $pricestring);
        } else {
            $pricestring = str_replace('{rightsymbol}', $currency, $pricestring);
            $pricestring = str_replace('{leftsymbol}', '', $pricestring);
        }
        //$pricestring = str_replace('{price}',money_format("%i",$price*$record['currency_rate']),$pricestring);
        $formatedprice = str_replace(".00", '', number_format($price * $record['currency_rate'], 2));
        $pricestring = str_replace('{price}', $formatedprice, $pricestring);
        return $pricestring;
    }

    public static function getDefaultCurrency()
    {
		
        $srch = Self::getSearchObject();
        $srch->addCondition('currency_id', '=', Info::getCurrentCurrency());
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetch($rs);
    }

    public static function price($price)
    {
        $srch = Self::getSearchObject();
        $srch->addCondition('currency_id', '=', Info::getCurrentCurrency());
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);
        $pricestring = $price * $record['currency_rate'];
        return $pricestring;
    }

    public static function reversePrice($price)
    {
        $srch = Self::getSearchObject();
        $srch->addCondition('currency_id', '=', Info::getCurrentCurrency());
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);
        return money_format("%i", $price / $record['currency_rate']);
    }

    public static function getCurrentCurrencyForForm()
    {
        $srch = Self::getSearchObject();
        $srch->addFld('currency_id');
        $srch->addFld('currency_name');
        $rs = $srch->getResultSet();

        return FatApp::getDb()->fetchAllAssoc($rs);
    }
	
	public static function getDefaultId()
	{
		return FatApp::getConfig('conf_default_currency', FatUtility::VAR_INT, self::$defaultCurrencyId);
	}
	
	public static function getSystemCurrency()
	{
		$defaultId = FatApp::getConfig('conf_default_currency', FatUtility::VAR_INT, self::$defaultCurrencyId);
		
		$srch = Self::getSearchObject();
        $srch->addCondition('currency_id', '=', $defaultId);
        
		$rs = $srch->getResultSet();
        
		return FatApp::getDb()->fetch($rs);
		
	}

}
