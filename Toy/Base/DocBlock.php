<?php

namespace Toy\Base;

class DocBlock
{

    public static $vectors = array(
        'param' => array('type', 'var', 'desc'),
        'return' => array('type', 'desc'),
    );

    public $descriptions;

    public $tags;

    public $comment;

    public function __construct($comment = null)
    {
        if ($comment)
            $this->setComment($comment);
    }

    public function setComment($comment)
    {
        $this->descriptions = '';
        $this->tags = array();
        $this->comment = $comment;
        $this->parseComment($comment);
    }

    protected function parseComment($comment)
    {
        // Strip the opening and closing tags of the docblock
        $comment = substr($comment, 3, -2);
        // Split into arrays of lines
        $comment = preg_split('/\r?\n\r?/', $comment);
        // Trim asterisks and whitespace from the beginning and whitespace from the end of lines
        $comment = array_map(function ($line) {
            return ltrim(rtrim($line), "* \t\n\r\0\x0B");
        }, $comment);
        // Group the lines together by @tags
        $blocks = array();
        $b = -1;
        foreach ($comment as $line) {
            if (self::isTagged($line)) {
                $b++;
                $blocks[] = array();
            } else if ($b == -1) {
                $b = 0;
                $blocks[] = array();
            }
            $blocks[$b][] = $line;
        }
        // Parse the blocks
        foreach ($blocks as $block => $body) {
            $body = trim(implode("\n", $body));
            if ($block == 0 && !self::isTagged($body)) {
                // This is the description block
                $this->descriptions = $body;
                continue;
            } else {
                // This block is tagged
                $tag = substr(self::stringTag($body), 1);
                $body = ltrim(substr($body, strlen($tag) + 2));

                if (isset(self::$vectors[$tag])) {
                    // The tagged block is a vector
                    $count = count(self::$vectors[$tag]);
                    if ($body) {
                        $parts = preg_split('/\s+/', $body, $count);
                    } else {
                        $parts = array();
                    }
                    // Default the trailing values
                    $parts = array_pad($parts, $count, null);
                    // Store as a mapped array
                    $this->tags[$tag][] = array_combine(
                        self::$vectors[$tag],
                        $parts
                    );
                } else {
                    // The tagged block is only text
                    $this->tags[$tag][] = $body;
                }
            }
        }
    }

    public function hasTag($tag)
    {
        return is_array($this->tags) && array_key_exists($tag, $this->tags);
    }

    public function tag($tag)
    {
        return $this->hasTag($tag) ? $this->tags[$tag] : null;
    }

    public function tagImplode($tag, $sep = ' ')
    {
        return $this->hasTag($tag) ? implode($sep, $this->tags[$tag]) : null;
    }

    public function tagMerge($tag)
    {
        return $this->hasTag($tag) ? array_merge_recursive($this->tags[$tag]) : null;
    }

    public static function isTagged($str)
    {
        return isset($str[1]) && $str[0] == '@' && ctype_alpha($str[1]);
    }

    public static function stringTag($str)
    {
        if (preg_match('/^@[a-z0-9_]+/', $str, $matches))
            return $matches[0];
        return null;
    }

}