<?php

namespace FriendsOfRedaxo\HtmlGenerator;


if (!defined('ENT_XML1')) {
    define('ENT_XML1', 16);
}

if (!defined('ENT_XHTML')) {
    define('ENT_XHTML', 32);
}

class Markup implements ArrayAccess
{
    public static bool $avoidXSS = false;
    public static int $outputLanguage = ENT_XML1;
    protected static ?Markup $instance = null;
    protected ?Markup $top = null;
    protected ?Markup $parent = null;
    protected ?string $tag = null;
    public ?array $attributeList = null;
    protected ?array $classList = null;
    protected ?array $content = null;
    protected string $text = '';
    protected bool $autoclosed = false;
    protected array $autocloseTagsList = [];

    protected function __construct(string $tag, ?Markup $top = null): Markup
    {
        $this->tag = $tag;
        $this->top = $top;
        $this->attributeList = [];
        $this->classList = [];
        $this->content = [];
        $this->autoclosed = in_array($this->tag, $this->autocloseTagsList);
        $this->text = '';
        return $this;
    }

    public static function __callStatic(string $tag, array $content): Markup
    {
        return self::createElement($tag)
            ->attr(count($content) && is_array($content[0]) ? array_pop($content) : [])
            ->text(implode('', $content));
    }

    public function __call(string $tag, array $content): Markup
    {
        return $this
            ->addElement($tag)
            ->attr(count($content) && is_array($content[0]) ? array_pop($content) : [])
            ->text(implode('', $content));
    }

    public function __invoke(): Markup
    {
        return $this->getParent();
    }

    public static function createElement(string $tag = ''): Markup
    {
        self::$instance = new static($tag);
        return self::$instance;
    }

    public function addElement(string $tag = ''): Markup
    {
        $htmlTag = (is_object($tag) && $tag instanceof self) ? clone $tag : new static($tag);
        $htmlTag->top = $this->getTop();
        $htmlTag->parent = $this;
        $this->content[] = $htmlTag;
        return $htmlTag;
    }

    public function set(string|array $attribute, ?string $value = null): Markup
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

    public function attr(string|array $attribute, ?string $value = null): Markup
    {
        return $this->set(...func_get_args());
    }

    public function offsetExists($attribute): bool
    {
        return isset($this->attributeList[$attribute]);
    }

    public function offsetGet($attribute)
    {
        return $this->offsetExists($attribute) ? $this->attributeList[$attribute] : null;
    }

    public function offsetSet($attribute, $value): void
    {
        $this->attributeList[$attribute] = $value;
    }

    public function offsetUnset($attribute): void
    {
        if ($this->offsetExists($attribute)) {
            unset($this->attributeList[$attribute]);
        }
    }

    public function text(string $value): Markup
    {
        $this->addElement('')->text = static::$avoidXSS ? static::unXSS($value) : $value;
        return $this;
    }

    public function getTop(): Markup
    {
        return $this->top === null ? $this : $this->top;
    }

    public function getParent(): ?Markup
    {
        return $this->parent;
    }

    public function getFirst(): ?Markup
    {
        return is_null($this->parent) ? null : $this->parent->content[0];
    }

    public function getPrevious(): Markup
    {
        $prev = $this;
        $find = false;
        if (!is_null($this->parent)) {
            foreach ($this->parent->content as $c) {
                if ($c === $this) {
                    $find = true;
                    break;
                }
                if (!$find) {
                    $prev = $c;
                }
            }
        }
        return $prev;
    }

    public function getNext(): ?Markup
    {
        $next = null;
        $find = false;
        if (!is_null($this->parent)) {
            foreach ($this->parent->content as $c) {
                if ($find) {
                    $next = $c;
                    break;
                }
                if ($c == $this) {
                    $find = true;
                }
            }
        }
        return $next;
    }

    public function getLast(): ?Markup
    {
        return is_null($this->parent) ? null : $this->parent->content[count($this->parent->content) - 1];
    }

    public function remove(): ?Markup
    {
        $parent = $this->parent;
        if (!is_null($parent)) {
            foreach ($parent->content as $key => $value) {
                if ($parent->content[$key] == $this) {
                    unset($parent->content[$key]);
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
            $string .=  '<' . $this->tag;
            $string .= $this->attributesToString();
            if ($this->autoclosed) {
                $string .= '/>';
            } else {
                $string .= '>' . $this->contentToString() . '</' . $this->tag . '>';
            }
        } else {
            $string .= $this->text;
            $string .= $this->contentToString();
        }
        return $string;
    }

    protected function attributesToString(): string
    {
        $string = '';
        $XMLConvention = in_array(static::$outputLanguage, [ENT_XML1, ENT_XHTML]);
        if (!empty($this->attributeList)) {
            foreach ($this->attributeList as $key => $value) {
                if ($value !== null && ($value !== false || $XMLConvention)) {
                    // ...
                }
            }
        }
        return $string;
    }

    protected function contentToString(): string
    {
        $string = '';
        if (!is_null($this->content)) {
            foreach ($this->content as $c) {
                $string .= $c->toString();
            }
        }
        return $string;
    }

    public static function unXSS(string $input): string
    {
        $return = '';
        if (version_compare(phpversion(), '5.4', '<')) {
            $return = htmlspecialchars($input);
        } else {
            $return = htmlentities($input, ENT_QUOTES | ENT_DISALLOWED | static::$outputLanguage);
        }
        return $return;
    }
}
