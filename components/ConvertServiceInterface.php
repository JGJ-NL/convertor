<?php

interface ConvertServiceInterface
{
    /**
     * createRequestParams()
     * Get structure to be used with soap for the conversion rate request
     * @from String Currency code (E.g. 'EUR', 'USD', etc)
     * @to String Currency code
     * @return Mixed Float with conversion rate value or else false to indicate access problem
     */
    public function retrieveConversionRate($from, $to);
}