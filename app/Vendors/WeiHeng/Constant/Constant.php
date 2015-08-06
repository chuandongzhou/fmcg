<?php

namespace WeiHeng\Constant;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Translation\Translator as Lang;

class Constant
{

    /**
     * instance of Config
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * instance of Translator
     *
     * @var \Illuminate\Translation\Translator
     */
    protected $lang;

    /**
     * Create a new constant instance.
     *
     * @param Config $config
     * @param Lang $lang
     */
    public function __construct(Config $config, Lang $lang)
    {
        $this->config = $config;
        $this->lang = $lang;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->config->has('constant.' . $key);
    }

    /**
     * Get the constant for the given key.
     *
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->config->get('constant.' . $key, $default);
    }

    /**
     * Get key from flip constant.
     *
     * @param string $key
     * @param string $value
     * @param null|mixed $default
     * @return null|string
     */
    public function key($key, $value = null, $default = null)
    {
        $constant = $this->get($key);
        if (is_array($constant)) {
            return array_get(array_flip($constant), $value, $default);
        }

        return $default;
    }

    /**
     * Get the lang for the given key.
     *
     * @param string $key
     * @param array $replace
     * @param null|string $locale
     * @return string
     */
    public function lang($key, array $replace = [], $locale = null)
    {
        return $this->lang->get('constant.' . $key, $replace, $locale);
    }

    /**
     * Get lang from constant through value.
     *
     * @param string $key
     * @param string $value
     * @param array $replace
     * @param null|string $locale
     * @return string
     */
    public function valueLang($key, $value = null, array $replace = [], $locale = null)
    {
        $subKey = $this->key($key, $value);
        if (is_array($subKey)) {
            $lang = $this->lang($key, $replace, $locale);

            return array_map(function ($key) use ($lang) {
                return $lang[$key];
            }, $subKey);
        }

        if (is_string($subKey)) {
            return $this->lang($key . '.' . $subKey, $replace, $locale);
        }

        return $key;
    }
}
