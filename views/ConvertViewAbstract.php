<?php

abstract class ConvertViewAbstract
{
    protected $cssPath = array();
    protected $jsPath = array();
    protected $alertText = '';
    protected $args = array();
    const JQUERY_PATH = '/views/js/jquery-1.11.1.min.js';
    const MAIN_CSS_PATH = '/views/css/main.css';


    protected abstract function pageContents();
    protected abstract function pageJsCss();


    public function setCssFilePath($path)
    {
        $this->cssPaths[] = $path;
    }


    public function setJsFilePath($path)
    {
        $this->jsPaths[] = $path;
    }


    public function setAlertMessage($text)
    {
        $this->alertText = $text;
    }


    public function pageStart($pageTitle='',$user='')
    {
        echo "<!DOCTYPE html>\n";
        echo '<html><head><meta charset="UTF-8" /><title>'.$pageTitle."</title>\n";
        if (!empty($this->cssPaths)) {
            foreach ($this->cssPaths as $path) {
                echo '<link rel="stylesheet" type="text/css" href="'.$path.'" />'."\n";
            }
        }
        if (!empty($this->jsPaths)) {
            foreach ($this->jsPaths as $path) {
                echo '<script type="text/javascript" src="'.$path.'"></script>'."\n";
            }
        }
        if (!empty($this->alertText)) {
            echo '<script type="text/javascript">'.
                '$(document).ready(function(){'.
                    'alert("'.$this->alertText.'");'.
                '});'.
                "</script>\n";
        }
        echo "</head>\n<body>\n";
    }


    public function pageBody($headerText)
    {
        echo '<div id="pagefull">'."\n";
        echo '<div id="pageheader"><h1>'.$headerText."</h1></div>\n";
        $this->pageMenu();
        
        $this->pageContents();
        
        echo '<div id="pagefooter">Made by John-Gerard Janssen</div>'."\n";
        echo "</div>\n";
    }


    public function pageEnd()
    {
        echo "</body>\n";
    }


    public function renderPage($args)
    {
        $this->args = $args;
        
        $this->setJsFilePath(self::JQUERY_PATH);
        $this->setCssFilePath(self::MAIN_CSS_PATH);
        if (!empty($this->args['alert'])) {
            $this->setAlertMessage($this->args['alert']);
        }
        
        $this->pageJsCss();
        
        $title = empty($this->args['title']) ? '' : $this->args['title'];
        $name = empty($this->args['name']) ? '' : $this->args['name'];
        $this->pageStart($title, $name);
        $this->pageBody($title);
        $this->pageEnd();
    }


    protected function pageMenu()
    {
        echo '<div id="pagesubhead">' .
            (!ConvertUser::isLoggedIn() ? '' :
                '<div id="pageuser">' .
                    (ConvertUser::isLoggedIn() ? 'User: '.ConvertUser::username() : '') .
                '</div>' .
                '<div id="pagemenu">' .
                    '<p id="pagemenuline">' .
                        '<a class="menulink" href="'.PAGE_CONVERTOR.'">Convertor</a>' .
                        '&nbsp; &nbsp;' .
                        '<a class="menulink" href="'.PAGE_LOGOUT.'">Logout</a>' .
                    '</p>' .
                '</div>'.
                '<div id="pagemenuclear"></div>'
            ) .
            "</div>\n";
    }

}