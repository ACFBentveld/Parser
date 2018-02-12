<?php

namespace ACFBentveld\Parser;


use Exception;

class InvalidTagsException extends Exception
{
    public static function missingTags(array $tags)
    {
        $count = count($tags);
        return new static("Expected 2 tags. Got: `{$count}` tags");
    }
}