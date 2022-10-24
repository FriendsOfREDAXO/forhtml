<?php
use HtmlGenerator\HtmlTag;

class FORHtml extends HtmlTag
{
   public function mmfile($type = 'default', $file =''):string
    {
     return $this->set('src', rex_media_manager::getUrl($type, $file));
    } 
}
