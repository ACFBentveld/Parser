<?php

namespace ACFBentveld\Parser;

use ArrayAccess;
use Closure;

class Parser
{
    /**
     * @var string text that should be parsed
     */
    protected $text    = "";

    /**
     * @var array values that should be replaced
     */
    protected $values  = [];

    /**
     * @var array tags that indicate a value that should be parsed
     */
    protected $tags    = ["[", "]"];

    /**
     * @var array items to exclude
     */
    protected $exclude = [];

    /**
     * @var array aliases for values
     */
    protected $aliases = [];

    /**
     * @var string parsed result
     */
    protected $result  = "";


    /**
     * Sets the text for the parser. Also the starting point. `Parser::text()`
     *
     * @param string $text - text to set
     *
     * @return \ACFBentveld\Parser\Parser
     */
    public static function text(?string $text)
    {
        $parser = new self;
        $parser->text = $text;

        return $parser;
    }


    /**
     * Parses the text
     *
     * @return string
     */
    public function parse()
    {
        $this->validate();
        $this->result = $this->text;

        $keys = $this->getKeys();
        $aliases = $this->mapAliases();
        $values = array_merge($aliases, $this->values);

        foreach ($keys as $key) {
            $value = $this->getValue($values, $key);

            if (!$this->isValidValue($value) || in_array($key, $this->exclude)) {
                continue;
            }

            $this->result = str_replace($this->tags[0] . $key . $this->tags[1], $value, $this->result);
        }
        return $this->result;
    }


    /**
     * Validates the parser input
     * Currently only used to detect missing tags
     *
     * @throws \ACFBentveld\Parser\InvalidTagsException
     */
    private function validate()
    {
        if (count($this->tags) != 2) {
            throw InvalidTagsException::missingTags($this->tags);
        } elseif (empty($this->tags[0]) || empty($this->tags[1])) {
            $this->tags = ['[', ']'];
        }
    }


    /**
     * Get all the keys that need to be replaced
     *
     * @return mixed
     */
    private function getKeys()
    {
        $openTag = $this->tags[0];
        $closeTag = $this->tags[1];
        preg_match_all('/\\' . $openTag . '(.*?)\\' . $closeTag . '/', $this->text, $matches);
        return $matches[1];
    }


    /**
     * Maps the aliases with a value
     *
     * @return array
     */
    public function mapAliases()
    {
        $aliases = [];

        foreach ($this->aliases as $alias => $key) {
            if (!array_key_exists($key, $this->values) || in_array($key, $this->exclude)) {
                continue;
            }

            $aliases[$alias] = $this->values[$key];
        }
        return $aliases;
    }


    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string $key
     *
     * @return mixed
     */
    private function getValue($target, $key)
    {
        if (is_null($key)) return $target;
        foreach (explode('.', $key) as $segment) {
            if (is_array($target)) {
                if (!array_key_exists($segment, $target)) {
                    return null;
                }
                $target = $target[$segment];
            } elseif ($target instanceof ArrayAccess) {
                if (!isset($target[$segment])) {
                    return null;
                }
                $target = $target[$segment];
            } elseif (is_object($target)) {
                if (!isset($target->{$segment})) {
                    return null;
                }
                $target = $target->{$segment};
            } else {
                return null;
            }
        }
        return $target instanceof Closure ? $target() : $target;
    }


    /**
     * Checks if the given value is a valid type for the parser
     *
     * @param $value
     *
     * @return bool
     */
    private function isValidValue($value)
    {
        return (is_string($value) || is_numeric($value) || is_bool($value));
    }


    /**
     * Sets the values
     *
     * @param array $values - values to set
     *
     * @return $this
     */
    public function values(array $values)
    {
        $this->values = $values;
        return $this;
    }


    /**
     * Sets the tags
     *
     * @param array $tags - tags to set
     *
     * @return $this
     */
    public function tags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }


    /**
     * Sets the exclude
     *
     * @param array $exclude - values to exclude
     *
     * @return $this
     */
    public function exclude(array $exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }


    /**
     * Sets the aliases
     *
     * @param array $aliases
     *
     * @return $this
     */
    public function aliases(array $aliases)
    {
        $this->aliases = $aliases;
        return $this;
    }
}