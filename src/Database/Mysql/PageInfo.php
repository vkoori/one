<?php


namespace One\Database\Mysql;


class PageInfo
{
    /**
     * @var int
     */
    private $perPage = 10;

    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var ListModel
     */
    private $list;

    /**
     * @var boolean
     */
    private $setMaxPage=true;

    /**
     * @var int
     */
    public $skip;

    /**
     * @var int
     */
    public $limit;

    public function setPaginate(?int $perPage = Null, string $query = 'page'): void
    {
        $this->setMaxPage = true;
        
        $page = $_GET[$query] ?? 1;

        if ($perPage) {
            $this->perPage = $perPage;
        }
        
        $this->skip = ($page-1) * $this->perPage;
        $this->limit = $this->perPage;
    }

    public function setData(int $total, ListModel $list): self
    {
        $this->total = $total;
        $this->list = $list;

        return $this;
    }
    
    public function toArray()
    {
        $res = [
            'total' => $this->total,
            'maxPage' => (int) ceil($this->total / $this->perPage),
            'list'  => $this->list->toArray()
        ];
        if ( !$this->setMaxPage ) {
            unset($res['maxPage']);
        }

        return $res;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->list->valid();
    }
}