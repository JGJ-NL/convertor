<?php

class ConvertorView extends ConvertViewAbstract
{
 const INITIAL_FROM = 'EUR';
 const INITIAL_TO = 'EUR';
 
 protected static $jsFiles = array(
    '/views/js/convertor.js'
 );

    protected function pageContents()
    {
        echo "<div id=\"contents\">\n";
        echo "<h2 id=\"contheader\">CONVERTER</h2>\n";
        
        // Amount conversion
        // =================
        echo "<div id=\"selectionwrapper\">\n";
        
        // From selection box
        echo '<div id="fromdiv"><p>From:</p>';
        echo "<select id=\"from\" name=\"from\">";
        foreach ($this->args['currencies'] as $key=>$curr) {
            $state = $key==self::INITIAL_FROM ? ' selected="selected"' : '';
            echo '<option id="from'.$key.'"'.$state.' name="from'.$key.'" value="'.$key.'">' . 
                $key.' - '.$curr.'</option>';
        }
        echo "</select></div>\n";
        
        // To selection box
        echo '<div id="todiv"><p>To:</p>';
        echo "<select id=\"to\" name=\"to\">";
        foreach ($this->args['currencies'] as $key=>$curr) {
            $state = $key==self::INITIAL_TO ? ' selected="selected"' : '';
            echo '<option id="to'.$key.'"'.$state.' name="to'.$key.'" value="'.$key.'">' .
                $key.' - '.$curr.'</option>';
        }
        echo "</select></div>\n";
        
        echo '<div id="amountdiv"><p>Amount (<span id="amountcurrency">'.self::INITIAL_FROM.'</span>):</p>';
        echo '<input type="text" name="amount" id="amount" value="" />';
        echo "</div>\n";
        
        echo '<div id="converteddiv"><p>Converted amount:</p>';
        echo '<p id="converted"></p>';
        echo "</div>\n";
        
        echo "<div class=\"cleardiv\"></div>\n";
        echo "</div>\n";
        
        // Current rates
        // =============
        
        echo '<div id="lastrates">' .
            '<p id="rateforward">' .
                '<span class="ratecurrencies"></span>&nbsp; &nbsp;' .
                '<span class="ratevalue"></span>&nbsp; &nbsp;' .
                '<span class="ratedate"></span></p>' .
            '<p id="ratebackward">' .
                '<span class="ratecurrencies"></span>&nbsp; &nbsp;' .
                '<span class="ratevalue"></span>&nbsp; &nbsp;' .
                '<span class="ratedate"></span></p>' .
            "</div>\n";
            
        // Current graph
        // =============
        
        echo '<div id="grapharea">' .
                '<canvas id="graphcanvas" width="920" height="300">' .
                'Your browser does not support graph canvasses.</canvas>' .
            "</div>\n";
        
        echo "</div>\n";
    }

    
    protected function pageJsCss()
    {
        foreach (self::$jsFiles as $file) {
            $this->setJsFilePath($file);
        }
    }

}