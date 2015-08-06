<?php

namespace Weiheng\Foundation;

use RecursiveTreeIterator, RecursiveIteratorIterator, CachingIterator;

class Tree extends RecursiveTreeIterator
{

    /**
     * 数据源
     *
     * @var array
     */
    protected $data = [];

    /**
     * 储存名称列表
     *
     * @var array
     */
    protected $names = [];

    /**
     * name键值
     *
     * @var string
     */
    protected $nameKey = 'name';

    /**
     * 构造函数
     *
     * @param array $data
     * @param string $idKey
     * @param string $pidKey
     * @param string $nameKey
     * @param int $flags
     * @param int $cit_flags
     * @param int $mode
     */
    public function __construct(array $data, $idKey = 'id', $pidKey = 'pid', $nameKey = 'name', $flags = RecursiveTreeIterator::BYPASS_KEY, $cit_flags = CachingIterator::CATCH_GET_CHILD, $mode = RecursiveIteratorIterator::SELF_FIRST)
    {
        $this->nameKey = $nameKey;

        // 生成树菜单
        $tempTree = [];
        foreach ($data as $each) {
            $id = $each[$idKey];

            // 生成引用
            $tempTree[$id] = null;
            $tempTree[$each[$pidKey]][$id] = &$tempTree[$id];

            // 储存名称
            $this->names[$id] = $each[$nameKey];
            // 储存数据
            $this->data[$id] = $each;
        }

        // 初始化
        parent::__construct(new \RecursiveArrayIterator($tempTree[0]), $flags, $cit_flags, $mode);

        // 设置前缀
        $prefixParts = [
            RecursiveTreeIterator::PREFIX_LEFT => ' ',
            RecursiveTreeIterator::PREFIX_MID_HAS_NEXT => '│ ',
            RecursiveTreeIterator::PREFIX_END_HAS_NEXT => '├ ',
            RecursiveTreeIterator::PREFIX_END_LAST => '└ '
        ];
        foreach ($prefixParts as $part => $string) {
            $this->setPrefixPart($part, $string);
        }
    }

    /**
     * 获取当前元素
     *
     * @return string
     */
    public function current()
    {
        return $this->getPrefix() . $this->names[$this->key()];
    }

    /**
     * 获取数据
     *
     * @param string|null $field
     * @return mixed
     */
    public function data($field = null)
    {
        $key = $this->key();
        if ($field) {
            $key .= '.' . $field;
        }

        return array_get($this->data, $key);
    }

    /**
     * 格式化为html的前缀
     *
     * @return mixed
     */
    public function getPrefix()
    {
        return str_replace(' ', '&nbsp;', parent::getPrefix());
    }

}