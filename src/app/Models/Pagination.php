<?php
namespace App\Models;

/**
 * 分页
 * Class Pagination
 * @package App\Models
 */
class Pagination
{
    private $counts;      //总条数
    private $currentPage; //当前页
    private $totalPages;  //总页数
    private $pageSize;    //每页显示的条数

	/**
	 * 构造函数
	 * Pagination constructor.
	 * @param $counts
	 * @param $currentPage
	 * @param $pageSize
	 */
    public function __construct($counts, $currentPage, $pageSize)
    {
        $this->counts = $counts;
        $this->pageSize = $pageSize;
        $this->totalPages = intval(ceil($counts / $pageSize));
        //处理异常页数,保证当前页>=1
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if ($this->totalPages > 0 && $currentPage > $this->totalPages) {
            $currentPage = $this->totalPages;
        }
        $this->currentPage = $currentPage;
    }

	/**
	 * 分页
	 * @return float|int
	 */
    public function getSkips()
    {
        return ($this->currentPage - 1) * $this->pageSize;
    }

	/**
	 * 每页数字
	 * @return mixed
	 */
    public function getPageSize()
    {
        return $this->pageSize;
    }

	/**
	 * 获取当前几页
	 * @return int
	 */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

	/**
	 * 总页数
	 * @return int
	 */
    public function getTotalPages()
    {
        return $this->totalPages;
    }
}