<?php

namespace ACFBentveld\Parser;

class Parser
{

    protected $text    = "";
    protected $values  = [];
    protected $tags    = ["[", "]"];
    protected $exclude = [];
    protected $aliases = [];
    protected $result  = "";


    /**
     * Create a new Skeleton Instance.
     */
    public function __construct()
    {
        // constructor body
    }


    /**
     * Sets the text for the parser. Also the starting point. `Parser::text()`
     *
     * @param string $text - text to set
     *
     * @return \ACFBentveld\Parser\Parser
     */
    public static function text(string $text)
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
        $aliases = $this->mapAliases();

        $values = array_merge($aliases, $this->values);

        foreach ($values as $key => $value) {

            if ($value instanceof \Closure) {
                $value = $value();
            }

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
        }
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
