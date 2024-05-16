<?php

namespace FriendsOfRedaxo\HtmlGenerator;

if (!defined('ENT_HTML5')) {
    define('ENT_HTML5', 48);
}

class HtmlTag extends Markup
{
    /**
     * @var int The language convention used for XSS avoiding
     */
    public static int $outputLanguage = ENT_HTML5;

    protected array $autocloseTagsList = [
    'img', 'br', 'hr', 'input', 'area', 'link', 'meta', 'param', 'base', 'col', 'command', 'keygen', 'source', 'track', 'wbr'
    ];
    
    /**
     * Shortcut to set('id', $value)
     * @param string $value
     * @return HtmlTag
     */
    public function id(string $value): HtmlTag
    {
        return $this->set('id', $value);
    }

    /**
     * Add a class to classList
     * @param string $value
     * @return HtmlTag
     */
    public function addClass(string $value): HtmlTag
    {
        if (!isset($this->attributeList['class']) || is_null($this->attributeList['class'])) {
            $this->attributeList['class'] = [];
        }
        $this->attributeList['class'][] = $value;
        return $this;
    }

    /**
     * Remove a class from classList
     * @param string $value
     * @return HtmlTag
     */
    public function removeClass(string $value): HtmlTag
    {
        if (!is_null($this->attributeList['class'])) {
            unset($this->attributeList['class'][array_search($value, $this->attributeList['class'])]);
        }
        return $this;
    }
}
