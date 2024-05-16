<?php
use ForHtmlGenerator\HtmlTag;

class FORHtml extends HtmlTag
{
   /** @api */
   public function mmfile(string $type = 'default', string $file =''):string
   {
     return $this->set('src', rex_media_manager::getUrl($type, $file));
   } 
   /** @api */
   public function content(string $content =''):string
   {
     return $this->text($content);
   }
   
   
}
