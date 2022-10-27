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
  
   
    /**
     * The auto closed tags list (or void elements.)
     *
     * @var array
     */
    protected $autocloseTagsList = array(
        'area',
        'base',
        'basefont',
        'bgsound',
        'br',
        'col',
        'command',
        'embed',
        'frame',
        'hr',
        'image',
        'img',
        'input',
        'isindex',
        'keygen',
        'link',
        'menuitem',
        'meta',
        'nextid',
        'param',
        'source',
        'track',
        'wbr'
    );   
   
   
}
