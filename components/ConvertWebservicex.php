<?php

class ConvertWebservicex implements ConvertServiceInterface
{
    const WSDL_URL = "http://www.webservicex.com/CurrencyConvertor.asmx?WSDL";
    
    
    public function retrieveConversionRate($from, $to)
    {
        try {
            $soapClient = new SoapClient(self::WSDL_URL);
            $params = array(
                'FromCurrency' => $from,
                'ToCurrency' => $to
            );
            $response = $soapClient->ConversionRate($params);
            if (is_object($response)) {
                $response = $response->ConversionRateResult;
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