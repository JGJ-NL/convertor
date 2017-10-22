<?php

class ConvertRate
{
    protected $from; // Currency code to convert from - as string (E.g. 'EUR')
    protected $to;   // Currency code to convert to
    
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }
    
    /**
     * currencyRate()
     * @return Mixed Array with [0]=rate and [1]= backward rate or false if not available.
     */
    public function currentRate()
    {
        $rates = $this->getFromCache();
        if(empty($rates)) {
            $rates = $this->requestRate();
        }
        return $rates;
    }
    
    
    /**
     * requestRate()
     * Obtain actual rate by one of the external services.
     * @return Mixed false if no rate was retrieved else array with forward/backward rate.
     */
    protected function requestRate()
    {
        $rates = array();
        $convertService = new ConvertService();
        $useService = $convertService->getFirstService();
        do {
            $rates[0] = $useService->retrieveConversionRate($this->from, $this->to);
            $useService = empty($rates[0]) ? $convertService->getNextService() : false;
        } while(empty($rates[0]) && $useService);
        if (empty($rates[0])) {
            return false;
        }
        $rates[0] = $this->adjustDecimals($rates[0]);
        
        // Now try to obtain the backward rate as well
        $useService = $convertService->getFirstService();
        do {
            $rates[1] = $useService->retrieveConversionRate($this->to, $this->from);
            $useService = empty($rates[1]) ? $convertService->getNextService() : false;
        } while(empty($rates[1]) && $useService);
        if (empty($rates[1])) {
            $rates[1] = 0;
        } else {
            $rates[1] = $this->adjustDecimals($rates[1]);
        }
        return $rates;
    }
    
    
    protected function adjustDecimals($rate)
    {
        $rate = round($rate, 4);
        return $rate;
    }
 
 
    protected function getFromCache()
    {
        // Memcache not yet implemented
        return false;
    }
    
    protected function addToCache($from, $to, $rate, $back)
    {
        // Memcache not yet implemented
    }
     
     
    public function weekRates()
    {
        // As this DEMO version doesn't have any history, nor memcache,
        // we simply use a fixed set of data and limit the last day to current time of day.
        $rates = array();
        // Determine last hour to be included of day 5
        $lastHour = 24*4 + date('H');
        // Ensure to fill array with rate values for testing
        foreach (self::$testRates as $key=>$rate) {
            if ($this->from=='EUR' && 
                ($this->to=='GBP' || $this->to=='USD') &&
                $key<=$lastHour
            ){
                // Use stored values for GBP and '1/values' for USD
                // Of course this is only fo this demo application
                $useRate  = $this->to=='USD' ? $rate : 1/$rate;
            } else {
                $useRate = 0;
            }
            $rates[$key] = $useRate;
        }
        return $rates;
    }
    
    protected static $testRates = array(
       // Day 1:
       1.2345, 1.2345, 1.2333, 1.2333, 1.2311, 1.2298,
       1.2398, 1.2399, 1.2399, 1.2399, 1.2455, 1.2455,
       1.2366, 1.2366, 1.2366, 1.2655, 1.2650, 1.2586,
       1.2432, 1.2411, 1.2354, 1.2354, 1.2354, 1.2354,
       // Day 2:
       1.2354, 1.2354, 1.2333, 1.2333, 1.2311, 1.2375,
       1.2398, 1.2399, 1.2399, 1.2399, 1.2455, 1.2455,
       1.2366, 1.2366, 1.2366, 1.2655, 1.2650, 1.2521,
       1.2432, 1.2411, 1.2354, 1.2354, 1.2354, 1.2354,
       // Day 3:
       1.2345, 1.2345, 1.2333, 1.2333, 1.2298, 1.2375,
       1.2398, 1.2399, 1.2399, 1.2399, 1.2455, 1.2455,
       1.2366, 1.2366, 1.2366, 1.2655, 1.2586, 1.2521,
       1.2432, 1.2411, 1.2354, 1.2354, 1.2354, 1.2354,
       // Day 4:
       1.2345, 1.2345, 1.2333, 1.2311, 1.2298, 1.2375,
       1.2398, 1.2399, 1.2399, 1.2455, 1.2455, 1.2455,
       1.2366, 1.2366, 1.2366, 1.2650, 1.2586, 1.2521,
       1.2432, 1.2411, 1.2354, 1.2354, 1.2354, 1.2354,
       // Day 5:
       1.2345, 1.2345, 1.2333, 1.2311, 1.2298, 1.2375,
       1.2398, 1.2399, 1.2399, 1.2455, 1.2455, 1.2455,
       1.2366, 1.2366, 1.2655, 1.2650, 1.2586, 1.2521,
       1.2432, 1.2411, 1.2354, 1.2354, 1.2354, 1.2354,
   );

}