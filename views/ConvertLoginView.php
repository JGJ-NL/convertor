<?php

class ConvertLoginView extends ConvertViewAbstract
{

    protected function pageContents()
    {
        echo '<div id="contents">' .
            '<form method="post" action="'. PAGE_LOGIN .'">' .
            '<input type="hidden" name="back" id="back" value="'.(!empty($this->args['back'])?$this->args['back']:'').'"/>' .
            '<table id="loginfields">' .
            '<tr>' .
                '<td class="formlabel">User</td>' .
                '<td class="formdata" align="right">' .
                    '<input type="text" name="user" id="user" value="'.(!empty($this->args['user'])?$this->args['user']:'').'" />' .
                '</td>' .
            '</tr>' .
            '<tr>' .
                '<td class="formlabel">Password</td>' .
                '<td class="formdata" align="right">' .
                    '<input type="password" name="password" id="password" />' .
                '</td>' .
            '</tr>' .
            '<tr>' .
                '<td colspan="2" class="formlast" align="right">' .
                    '<input type="submit" name="posted" id="posted" class="formbuttom" value="Submit"/>' .
                '</td>' .
            '</tr>' .
            '</table>' .
            '</form>' .
            "</div>\n";
    }


    protected function pageJsCss()
    {
        // No special files required for Login page
    }

}