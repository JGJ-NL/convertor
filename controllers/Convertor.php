<?php

class Convertor
{
    protected $error; // Possible error message to be displayed

    protected $msg;


    public function __construct()
    {
        $this->error = '';
        $this->msg = '';

        if( !ConvertUser::isLoggedIn()) {
            ConvertLogin::gotoLoginPage();
        }


    }
    

    public function start()
    {
        
        $currencies = new ConvertCurrency();

        $args = array(
            'title' => 'Currency Convertor',
            'name' => ConvertUser::username(),
            'alert' => (!empty($this->error) ? $this->error : ''),
            
            'currencies' => $currencies->getAll(),
        );

        $view = new ConvertorView();
        $view->renderPage($args);
    }
    
    
    // Ajax call to get a specific rate
    public function rate()
    {
        $from = empty($_GET['from']) ? '' : $_GET['from'];
        $to = empty($_GET['to']) ? '' : $_GET['to'];
        
        $obj = new stdClass();
        $obj->from = $from;
        $obj->to = $to;
        $obj->error = 0;
        if (!empty($from) && !empty($to)) {
            $convertRate = new ConvertRate($from, $to);
            $rates = $convertRate->currentRate();
            
            if (!empty($rates)) {
                $obj->rate = $rates[0];
                $obj->back = $rates[1];
                $obj->date = date("Y-m-d H:i");
            } else {
                $obj->error = 'Error obtaining rates. Please try it again later.';
            }
        } else {
            $obj->error = 'Please enter both a from and to currency';
        }
        
        $json = json_encode($obj);
        echo $json;
        exit();
    }
    
    
    public function graph()
    {
        $from = empty($_GET['from']) ? '' : $_GET['from'];
        $to = empty($_GET['to']) ? '' : $_GET['to'];
        if ($to=='EUR') {
           $to = $from;
           $from = 'EUR';
        }
        $now = time();
        $rates = $this->getWeekValues($from, $to);
        $days = $this->getGraphDays($now);
        $obj = new stdClass();
        $obj->from = $from;
        $obj->to = $to;
        $obj->rates = $rates;
        $obj->grid = $this->getVerticalGridValue($rates);
        $obj->days = $days;
        $obj->time = date('H:i',$now);
        $json = json_encode($obj);
        echo $json;
        exit();
    }
    
    protected function getGraphDays($now)
    {
        $days = array();
        $day = 4;
        while($day>=0)
        {
         $days[] = date("M j", $now-($day*24*60*60));
         $day--;
        }
        return $days;
    }
    
    protected function getVerticalGridValue($rates)
    {
        $min = 999999;
        $max = 0;
        foreach ($rates as $rate) {
            if ($rate>0) {
                $max = max($max, $rate);
                $min = min($min, $rate);
            }
        }
        if ($min==$max) {
            $average = round($max,3);
            $gridValue = 0.001;
        } else {
            $average = round((($max+$min) / 2), 3);
            $gridValue = (($max-$min) / 6) * 1.05;
            // Ensure to get at most 3 decimals that fit all values
            if($gridValue<0.001) {
                $gridValue = 0.001;
            } else {
                $gridValue *= 1000;
                $gridValue = ceil($gridValue);
                $gridValue /= 1000;
                $gridValue = round($gridValue,3);
            }
        }
        // Ensure that average also has at most
        $grid = array();
        $ix=-3;
        while ($ix<=3) {
            $grid[] = $average + ($ix * $gridValue);
            $ix++;
        }
        return $grid;
    }
    
    protected function getWeekValues($from, $to)
    {
        $convertRate = new ConvertRate($from, $to);
        $rates = $convertRate->weekRates();
        return $rates;
    }
}