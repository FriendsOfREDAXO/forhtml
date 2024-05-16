<?php

namespace FriendsOfRedaxo\HtmlGenerator;

class Markup implements \ArrayAccess
{
    public static bool $avoidXSS = false;
    public static int $outputLanguage = ENT_XML1;
    protected static ?Markup $instance = null;
    protected ?Markup $top = null;
    protected ?Markup $parent = null;
    protected ?string $tag = null;
    public array $attributeList = [];
    protected array $classList = [];
    protected array $content = [];
    protected string $text = '';
    protected bool $autoclosed = false;
    protected array $autocloseTagsList = [
        'img', 'br', 'hr', 'input', 'area', 'link', 'meta', 'param', 'base', 'col', 'command', 'keygen', 'source', 'track', 'wbr'
    ];

    protected function __construct(string $tag, ?Markup $top = null)
    {
        $this->tag = $tag;
        $this->top = $top;
        $this->autoclosed = in_array($this->tag, $this->autocloseTagsList);
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

    public function set(array|string $attribute, ?string $value = null): Markup
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

    public function attr(array|string $attribute, ?string $value = null): Markup
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

    public function text(string $value): Markup
    {
        $this->addElement('')->text = static::$avoidXSS ? static::unXSS($value) : $value;
        return $this;
    }

    public function getTop(): Markup
    {
        return $this->top ?? $this;
    }

    public function getParent(): ?Markup
    {
        return $this->parent;
    }

    public function getFirst(): ?Markup
    {
        return $this->parent->content[0] ?? null;
    }

    public function getPrevious(): Markup
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

    public function getNext(): ?Markup
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

    public function getLast(): ?Markup
    {
        return $this->parent->content[array_key_last($this->parent->content)] ?? null;
    }

    public function remove(): ?Markup
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
}
