<?php

class ConvertCurrency
{
 protected static $currencies = array(
  'AFA' => 'Afghanistan Afghani',
  'ALL' => 'Albanian Lek',
  'AWG' => 'Aruba Florin',
  'BDT' => 'Bangla Desh Taka',
  'CAD' => 'Canadian Dollar',
  'CHF' => 'Swiss Franc',
  'CNY' => 'Chinese Yuan',
  'EGP' => 'Egyptian Pound',
  'EUR' => 'Euro',
  'GBP' => 'British Pound',
  'ILS' => 'Israeli Shekel',
  'ISK' => 'Island Krona',
  'JPY' => 'Japanese Yen',
  'MXN' => 'Mexican Peso',
  'NOK' => 'Norwegian Krone',
  'RUB' => 'Russian Rouble',
  'TRL' => 'Turkish Lira',
  'USD' => 'United States Dollar'
 );
 
 public function getAll()
 {
  return self::$currencies;
 }
}