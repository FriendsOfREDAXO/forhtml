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
/*
 * @author Airmanbzh
 */
namespace HtmlGenerator;

use ArrayAccess;
use ArrayAccess;

if (!defined('ENT_XML1')) {
    define('ENT_XML1', 16);
}
if (!defined('ENT_XHTML')) {
    define('ENT_XHTML', 32);
}


class Markup implements ArrayAccess
{
    /** @var boolean Specifies if attribute values and text input sould be protected from XSS injection */
    public static $avoidXSS = false;

    /** @var int The language convention used for XSS avoiding */
    public static $outputLanguage = ENT_XML1;

    protected static $instance = null;

    protected $top = null;
    protected $parent = null;

    protected $tag = null;
    public $attributeList = null;
    protected $classList = null;

    protected $content = null;
    protected $text = '';

    protected $autoclosed = false;

    protected $autocloseTagsList = array();

    /**
     * Constructor
     * @param mixed $tag
     * @param Markup $top
     * @return static instance
     */
    protected function __construct($tag, $top = null)
    {
        $this->tag = $tag;
        $this->top =& $top;
        $this->attributeList = array();
        $this->classList = array();
        $this->content = array();
        $this->autoclosed = in_array($this->tag, $this->autocloseTagsList);
        $this->text = '';
        return $this;
    }

    /**
     * Builds markup from static context
     * @param string $tag The tag name
     * @param array  $content The content of the current tag, first argument can be an array containing the attributes
     * @return static
     */
    public static function __callStatic($tag, $content)
    {
        return self::createElement($tag)
            ->attr(count($content) && is_array($content[0]) ? array_pop($content) : array())
            ->text(implode('', $content));
    }

    /**
     * Add a children to the current element
     * @param string $tag The name of the tag
     * @param array  $content The content of the current tag, first argument can be an array containing the attributes
     * @return Markup instance
     */
    public function __call($tag, $content)
    {
        return $this
            ->addElement($tag)
            ->attr(count($content) && is_array($content[0]) ? array_pop($content) : array())
            ->text(implode('', $content));
    }

    /**
     * Alias for getParent()
     * @return Markup
     */
    public function __invoke()
    {
        return $this->getParent();
    }

    /**
     * Create a new Markup
     * @param string $tag
     * @return static instance
     */
    public static function createElement($tag = '')
    {
        self::$instance = new static($tag);
        return self::$instance;
    }

    /**
     *
     * Add element at an existing Markup
     * @param Markup|string $tag
     * @return static instance
     */
    public function addElement($tag = '')
    {
        $htmlTag = (is_object($tag) && $tag instanceof self) ? clone $tag : new static($tag);
        $htmlTag->top = $this->getTop();
        $htmlTag->parent = &$this;

        $this->content[] = $htmlTag;
        return $htmlTag;
    }

    /**
     * (Re)Define an attribute or many attributes
     * @param string|array $attribute
     * @param string $value
     * @return static instance
     */
    public function set($attribute, $value = null)
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

    /**
     * alias to method "set"
     * @param string|array $attribute
     * @param string $value
     * @return static instance
     */
    public function attr($attribute, $value = null)
    {
        return call_user_func_array(array($this, 'set'), func_get_args());
    }

    /**
     * Checks if an attribute is set for this tag and not null
     *
     * @param string $attribute The attribute to test
     * @return boolean The result of the test
     */
    public function offsetExists($attribute)
    {
        return isset($this->attributeList[$attribute]);
    }

    /**
     * Returns the value the attribute set for this tag
     *
     * @param string $attribute The attribute to get
     * @return mixed The stored result in this object
     */
    public function offsetGet($attribute)
    {
        return $this->offsetExists($attribute) ? $this->attributeList[$attribute] : null;
    }

    /**
     * Sets the value an attribute for this tag
     *
     * @param string $attribute The attribute to set
     * @param mixed $value The value to set
     * @return void
     */
    public function offsetSet($attribute, $value)
    {
        $this->attributeList[$attribute] = $value;
    }

    /**
     * Removes an attribute
     *
     * @param mixed $attribute The attribute to unset
     * @return void
     */
    public function offsetUnset($attribute)
    {
        if ($this->offsetExists($attribute)) {
            unset($this->attributeList[$attribute]);
        }
    }

    /**
     *
     * Define text content
     * @param string $value
     * @return static instance
     */
    public function text($value)
    {
        $this->addElement('')->text = static::$avoidXSS ? static::unXSS($value) : $value;
        return $this;
    }

    /**
     * Returns the top element
     * @return static
     */
    public function getTop()
    {
        return $this->top===null ? $this : $this->top;
    }

    /**
     *
     * Return parent of current element
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Return first child of parent of current object
     */
    public function getFirst()
    {
        return is_null($this->parent) ? null : $this->parent->content[0];
    }

    /**
     * Return previous element or itself
     *
     * @return static instance
     */
    public function getPrevious()
    {
        $prev = $this;
        $find = false;
        if (!is_null($this->parent)) {
            foreach ($this->parent->content as $c) {
                if ($c === $this) {
                    $find=true;
                    break;
                }
                if (!$find) {
                    $prev = $c;
                }
            }
        }
        return $prev;
    }

    /**
     * @return static last child of parent of current object
     */
    public function getNext()
    {
        $next = null;
        $find = false;
        if (!is_null($this->parent)) {
            foreach ($this->parent->content as $c) {
                if ($find) {
                    $next = &$c;
                    break;
                }
                if ($c == $this) {
                    $find = true;
                }
            }
        }
        return $next;
    }

    /**
     * @return static last child of parent of current object
     */
    public function getLast()
    {
        return is_null($this->parent) ? null : $this->parent->content[count($this->parent->content) - 1];
    }

    /**
     * @return static return parent or null
     */
    public function remove()
    {
        $parent = $this->parent;
        if (!is_null($parent)) {
            foreach ($parent->content as $key => $value) {
                if ($parent->content[$key] == $this) {
                    unset($parent->content[$key]);
                    return $parent;
                }
            }
        }
        return null;
    }

    /**
     * Generation method
     * @return string
     */
    public function __toString()
    {
        return $this->getTop()->toString();
    }

    /**
     * Generation method
     * @return string
     */
    public function toString()
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

    /**
     * return current list of attribute as a string $key="$val" $key2="$val2"
     * @return string
     */
    protected function attributesToString()
    {
        $string = '';
        $XMLConvention = in_array(static::$outputLanguage, array(ENT_XML1, ENT_XHTML));
        if (!empty($this->attributeList)) {
            foreach ($this->attributeList as $key => $value) {
                if ($value!==null && ($value!==false || $XMLConvention)) {
                    $string.= ' ' . $key;
                    if ($value===true) {
                        if ($XMLConvention) {
                            $value = $key;
                        } else {
                            continue;
                        }
                    }
                    $string.= '="' . implode(
                        ' ',
                        array_map(
                            static::$avoidXSS ? 'static::unXSS' : 'strval',
                            is_array($value) ? $value : array($value)
                        )
                    ) . '"';
                }
            }
        }
        return $string;
    }

    /**
     * return current list of content as a string
     * @return string
     */
    protected function contentToString()
    {
        $string = '';
        if (!is_null($this->content)) {
            foreach ($this->content as $c) {
                $string .= $c->toString();
            }
        }

        return $string;
    }

    /**
     * Protects value from XSS injection by replacing some characters by XML / HTML entities
     * @param string $input The unprotected value
     * @return string A safe string
     */
    public static function unXSS($input)
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
