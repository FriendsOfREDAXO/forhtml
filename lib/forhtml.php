<?php
use HtmlGenerator\HtmlTag;

class FORHtml extends HtmlTag
{
   /** @api */
   public function mmfile(string $type = 'default', string $file =''):string
    {
     return $this->set('src', rex_media_manager::getUrl($type, $file));
    } 
   
      public function body(string $body =''):string
    {
     return $this->text($body);
    } 
}
