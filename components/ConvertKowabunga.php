<?php

class ConvertKowabunga implements ConvertServiceInterface
{
    const WSDL_URL = "http://currencyconverter.kowabunga.net/converter.asmx?WSDL";
    
    
    public function retrieveConversionRate($from, $to)
    {
        try {
            $soapClient = new SoapClient(self::WSDL_URL);
            $params = array(
                'CurrencyFrom' => $from,
                'CurrencyTo' => $to,
                'RateDate' => date('c')
            );
            $response = $soapClient->GetConversionRate($params);
            if (is_object($response)) {
                $response = $response->GetConversionRateResult;
            }
            if ($response <=0) {
                $response = false;
            }
        } catch(Exception $e) {
            $response = false;
        }
        return $response;
    }
    
}