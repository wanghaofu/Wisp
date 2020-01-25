<?php namespace Wisp\Orm\ListProvider;


abstract class AbstractListProvider implements IListProvider, \Countable
{
    /**
     * @return int
     */
    public function count()
    {
        $pager = $this->getPager();

        return $pager['count'];
    }

    public function startJoin($parentName)
    {
        return new JoinListProvider($this, $parentName);
    }
}
