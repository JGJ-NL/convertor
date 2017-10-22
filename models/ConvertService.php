<?php

class ConvertService
{
    protected $currentService;
    
    public function getFirstService()
    {
        $this->currentService = new ConvertWebservicex();
        return $this->currentService;
    }
    
    
    public function getNextService()
    {
        if (empty($this->currentService)) {
            return $this->getFirstService();
        } else {
            switch (get_class($this->currentService)) {
                case 'ConvertWebservicex':
                    $nextService = new ConvertKowabunga();
                    break;
                case 'ConvertKowabunga':
                    $nextService = false;
                    break;
            }
            $this->currentService = $nextService;
            return $this->currentService;
        }
    }
 
}