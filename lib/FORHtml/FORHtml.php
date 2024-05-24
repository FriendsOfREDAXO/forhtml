<?php

namespace FriendsOfRedaxo\FORHtml;

use rex_media_manager;

if (!defined('ENT_HTML5')) {
    define('ENT_HTML5', 48);
}

class FORHtml implements \ArrayAccess
{
    public static bool $avoidXSS = false;
    public static int $outputLanguage = ENT_HTML5; // Changed to ENT_HTML5
    protected static ?FORHtml $instance = null;
    protected ?FORHtml $top = null;
    protected ?FORHtml $parent = null;
    protected ?string $tag = null;
    public array $attributeList = [];
    protected array $classList = [];
    protected array $content = [];
    protected string $text = '';
    protected bool $autoclosed = false;
    protected array $autocloseTagsList = [
        'img', 'br', 'hr', 'input', 'area', 'link', 'meta', 'param', 'base', 'col', 'command', 'keygen', 'source', 'track', 'wbr'
    ];

    protected function __construct(string $tag, ?FORHtml $top = null)
    {
        $this->tag = $tag;
        $this->top = $top;
        $this->autoclosed = in_array($this->tag, $this->autocloseTagsList);
    }

    public static function __callStatic(string $tag, array $content): FORHtml
    {
        return self::createElement($tag)
            ->attr(count($content) && is_array($content[0]) ? array_pop($content) : [])
            ->text(implode('', $content));
    }

    public function __call(string $tag, array $content): FORHtml
    {
        return $this
            ->addElement($tag)
            ->attr(count($content) && is_array($content[0]) ? array_pop($content) : [])
            ->text(implode('', $content));
    }

    public function __invoke(): FORHtml
    {
        return $this->getParent();
    }

    public static function createElement(string $tag = ''): FORHtml
    {
        self::$instance = new static($tag);
        return self::$instance;
    }

    public function addElement(string $tag = ''): FORHtml
    {
        $htmlTag = (is_object($tag) && $tag instanceof self) ? clone $tag : new static($tag);
        $htmlTag->top = $this->getTop();
        $htmlTag->parent = $this;
        $this->content[] = $htmlTag;
        return $htmlTag;
    }

    public function set(array|string $attribute, ?string $value = null): FORHtml
    {
        if (is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $this[$key] = $value;
            }
        } else {
            $this[$attribute] = $value;
        }
        return $this;
    }

    public function attr(array|string $attribute, ?string $value = null): FORHtml
    {
        return $this->set(...func_get_args());
    }

    public function offsetExists(mixed $attribute): bool
    {
        return isset($this->attributeList[$attribute]);
    }

    public function offsetGet(mixed $attribute): mixed
    {
        return $this->attributeList[$attribute] ?? null;
    }

    public function offsetSet(mixed $attribute, mixed $value): void
    {
        $this->attributeList[$attribute] = $value;
    }

    public function offsetUnset(mixed $attribute): void
    {
        unset($this->attributeList[$attribute]);
    }

    public function text(string $value): FORHtml
    {
        $this->addElement('')->text = static::$avoidXSS ? static::unXSS($value) : $value;
        return $this;
    }

    public function getTop(): FORHtml
    {
        return $this->top ?? $this;
    }

    public function getParent(): ?FORHtml
    {
        return $this->parent;
    }

    public function getFirst(): ?FORHtml
    {
        return $this->parent->content[0] ?? null;
    }

    public function getPrevious(): FORHtml
    {
        $prev = $this;
        if ($this->parent !== null) {
            foreach ($this->parent->content as $c) {
                if ($c === $this) {
                    break;
                }
                $prev = $c;
            }
        }
        return $prev;
    }

    public function getNext(): ?FORHtml
    {
        $next = null;
        if ($this->parent !== null) {
            $found = false;
            foreach ($this->parent->content as $c) {
                if ($found) {
                    $next = $c;
                    break;
                }
                if ($c === $this) {
                    $found = true;
                }
            }
        }
        return $next;
    }

    public function getLast(): ?FORHtml
    {
        return $this->parent->content[array_key_last($this->parent->content)] ?? null;
    }

    public function remove(): ?FORHtml
    {
        if ($this->parent !== null) {
            foreach ($this->parent->content as $key => $value) {
                if ($value === $this) {
                    unset($this->parent->content[$key]);
                }
            }
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->getTop()->toString();
    }

    public function toString(): string
    {
        $string = '';
        if (!empty($this->tag)) {
            $string .= "<{$this->tag}" . $this->attributesToString();
            $string .= $this->autoclosed ? '/>' : ">{$this->contentToString()}</{$this->tag}>";
        } else {
            $string .= $this->text . $this->contentToString();
        }
        return $string;
    }

    protected function attributesToString(): string
    {
        $string = '';
        $XMLConvention = in_array(static::$outputLanguage, [ENT_XML1, ENT_XHTML]);
        foreach ($this->attributeList as $key => $value) {
            if ($value !== null && ($value !== false || $XMLConvention)) {
                $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
                $string .= " {$key}=\"{$escapedValue}\"";
            }
        }
        return $string;
    }

    protected function contentToString(): string
    {
        return array_reduce($this->content, fn($carry, $c) => $carry . $c->toString(), '');
    }

    public static function unXSS(string $input): string
    {
        return htmlentities($input, ENT_QUOTES | ENT_DISALLOWED | static::$outputLanguage);
    }

    // Methods from HtmlTag
    /**
     * Shortcut to set('id', $value)
     * @param string $value
     * @return Markup
     */
    public function id(string $value): FORHtml
    {
        return $this->set('id', $value);
    }

    /**
     * Add a class to classList
     * @param string $value
     * @return Markup
     */
    public function addClass(string $value): FORHtml
    {
        if (!isset($this->attributeList['class']) || is_null($this->attributeList['class'])) {
            $this->attributeList['class'] = [];
        }
        $this->attributeList['class'][] = $value;
        return $this;
    }

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

    /**
     * Remove a class from classList
     * @param string $value
     * @return Markup
     */
    public function removeClass(string $value): FORHtml
    {
        if (!is_null($this->attributeList['class'])) {
            unset($this->attributeList['class'][array_search($value, $this->attributeList['class'])]);
        }
        return $this;
    }
}
