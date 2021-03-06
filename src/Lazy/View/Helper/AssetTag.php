<?php

namespace Lazy\View\Helper;

abstract class AssetTag extends Tag
{
    const SOURCE_ATTRIBUTE = '';

    /**
     * @var string
     */
    protected static $defaultBasePath = '';

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $sources = [];

    /**
     * @var string
     */
    protected static $defaultDisableCachingParam = '__dc';

    /**
     * @var string
     */
    protected $disableCachingParam;

    /**
     * @param string $basePath
     */
    public static function setDefaultBasePath($basePath)
    {
        static::$defaultBasePath = $basePath;
    }

    /**
     * @return string
     */
    public static function getDefaultBasePath()
    {
        return static::$defaultBasePath;
    }

    /**
     * @param string|array|arguments $sources
     * @return $this
     */
    public function assertTag($sources = null)
    {
        if ($sources) {
            is_array($sources) || $sources = func_get_args();
            $this->sources = $sources;
        }

        return $this;
    }

    public function __invoke()
    {
        return call_user_func_array([$this, 'assertTag'], func_get_args());
    }

    /**
     * @param string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath === null? static::$defaultBasePath : $this->basePath;
    }

    /**
     * @param string $param
     */
    public static function setDefaultDisableCachingParam($param)
    {
        static::$defaultDisableCachingParam = $param;
    }

    /**
     * @return string
     */
    public static function getDefaultDisableCachingParam()
    {
        return static::$defaultDisableCachingParam;
    }

    /**
     * @param string $param
     * @return $this
     */
    public function setDisableCachingParam($param)
    {
        $this->disableCachingParam = $param;
        return $this;
    }

    /**
     * @return string
     */
    public function getDisableCachingParam()
    {
        return $this->disableCachingParam === null? static::$defaultDisableCachingParam : $this->disableCachingParam;
    }

    /**
     * @param string|array $sources
     * @return $this
     */
    public function append($sources)
    {
        $this->sources = array_merge($this->sources, (array) $sources);
        return $this;
    }

    /**
     * @param string|array $sources
     * @return $this
     */
    public function prepend($sources)
    {
        $this->sources = array_merge((array) $sources, $this->sources);
        return $this;
    }

    /**
     * @return string
     */
    public function build()
    {
        $attributes = $this->buildAttributes();
        $children = implode('', $this->children);
        $pattern = $this->getPattern();
        $tagName = $this->tagName;

        $sources = $this->sources;
        is_array($sources) || $sources = [$sources];
        if ($cachingParam = $this->getDisableCachingParam()) {
            array_walk($sources, function(&$source) use ($cachingParam) {
                if (false === strpos($source, '?')) {
                    $source .= '?';
                } else {
                    $source .= '&';
                }

                $source .= $cachingParam;
            });
        }

        $tags = [];
        foreach ($sources as $source) {
            if (!preg_match('/^(https?:|\/)/', $source)) {
                $source = $this->getBasePath() . '/' . $source;
            }
            $attrs = $attributes;
            array_unshift($attrs, sprintf('%s="%s"', static::SOURCE_ATTRIBUTE, $this->escape($source)));
            $tags[] = sprintf($pattern, $tagName, ' ' . implode(' ', $attrs), $children, $tagName);
        }

        return implode('', $tags);
    }
}