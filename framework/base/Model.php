<?php
namespace framework\base;
class Model{
	protected $config =array();
	protected $options = array(
							'table' => '',
							'field' => '*',
							'where' => array(),
							'order' => '',
							'limit' => '',
							'data' => array(),
							'pager' => array(),
				);
	protected $database = 'default';
	protected $table = '';	
	protected $trueTable = null;
	protected static $objArr = array();
	public $pager = null;
	
	public function __construct( $database = 'default' ) {
		if( $database ){
			$this->database = $database;
		}
		$this->config = Config::get('DB.' . $this->database);
		if( empty($this->config) || !isset($this->config['DB_TYPE']) ) {
			throw new \Exception($this->database.' database config error', 500);
		}
		$this->table = (null==$this->trueTable) ? $this->config['DB_PREFIX'].$this->table : $this->trueTable;
		$this->trueTable = $this->table;
		$this->table($this->trueTable, true);
	}
			
	public function query($sql, $params = array()) {
		$sql = trim($sql);
		if ( empty($sql) ) return array();
		$sql = str_replace('{pre}', $this->config['DB_PREFIX'], $sql);
		return $this->getDb()->query($sql, $params);	
	}

	public function execute($sql, $params = array()) {
		$sql = trim($sql);
		if ( empty($sql) ) return 0;
		$sql = str_replace('{pre}', $this->config['DB_PREFIX'], $sql);
		return $this->getDb()->execute($sql, $params); 
	}
	
	public function find() {
		$this->limit(1);
		$data = $this->select();
		return isset($data[0]) ? $data[0] : array();
 	}	 

	public function select() {		
		$field = $this->options['field'];
		if( empty($field) ) $field  = '*'; 
		$this->options['field'] = '*';
		
		$order = $this->options['order'];
		$this->options['order'] = '';

		$limit = $this->options['limit'];
		$this->options['limit'] = '';
		
		$table = $this->_getTable();
		$where = $this->_getWhere();
		
		//Pagination
		if( !empty($this->options['pager']) ){
			$count = $this->getDb()->Count($table, $where);
			$this->_pager($this->options['pager']['page'], $this->options['pager']['pageSize'], 
						$this->options['pager']['scope'] = 10, $count);
			$this->options['pager'] = array();
			$limit = $this->pager['offset'] . ',' . $this->pager['limit'];
		}
		
		return $this->getDb()->select($table, $where, $field, $order, $limit);		
 	}
	
	public function insert() {
		if( empty($this->options['data']) || !is_array($this->options['data']) ) return false;
		
		return $this->getDb()->insert($this->_getTable(), $this->_getData());
	}
	
	public function update() {
		if( empty($this->options['where']) || !is_array($this->options['where'])  ) return false;
		if( empty($this->options['data']) || !is_array($this->options['data']) ) return false;
				
		return $this->getDb()->update($this->_getTable(), $this->_getWhere(), $this->_getData());
	}
	
	public function delete() {
		if( empty($this->options['where']) || !is_array($this->options['where'])  ) return false;

		return $this->getDb()->delete($this->_getTable(), $this->_getWhere());
	}

	public function count() {
		return $this->getDb()->count($this->_getTable(), $this->_getWhere());
	}
	
	public function getFields() {
		return $this->getDb()->getFields( $this->_getTable() );
	}
	
	public function getSql() {
	return $this->getDb()->getSql();
	}

	public function beginTransaction() {
		return $this->getDb()->beginTransaction();
	}
	
	public function commit() {
		return $this->getDb()->commit();
	}
	
	public function rollBack() {
		return $this->getDb()->rollBack();
	}

	public function table($table, $ignorePre = false) {
		$this->options['table'] = $ignorePre ? $table : $this->config['DB_PREFIX'] . $table;
		return $this;
	}

	public function join($join, $way='inner'){
		$join = str_replace('{pre}', $this->config['DB_PREFIX'], $join);
		$this->options['table'] = " {$this->options['table']} {$way} join {$join} ";
		return $this;
	}
	
	public function field($field) {
		$this->options['field'] = $field;
		return $this;
	}

	public function data(array $data = array()) {
		$this->options['data'] = $data;
		return $this;
	}

	public function where(array $where = array()) {
		$this->options['where'] = $where;
		return $this;
	}		

	public function order($order) {
		$this->options['order'] = $order;
		return $this;
	}

	public function limit($limit) {
		$this->options['limit'] = $limit;
		return $this;
	}	

	public function pager($page, $pageSize = 10, $scope = 10){
		$page = max(intval($page), 1);
		$this->options['pager'] = compact(array('page', 'pageSize', 'scope'));
		return $this;
	}
	
	public function cache($expire = 1800){
		$cache = new Cache($this->config['DB_CACHE']);
		$cache->proxyObj = $this;
		$cache->proxyExpire = $expire;
		return $cache;
	}
	
	public function clear() {
		$cache = new Cache($this->config['DB_CACHE']);
		return $cache->clear();
	}
	
	protected function getDb() {
		if( empty(self::$objArr[$this->database]) ){
			$dbDriver = __NAMESPACE__.'\db\\' . ucfirst( $this->config['DB_TYPE'] ).'Driver';
			self::$objArr[$this->database] = new $dbDriver( $this->config );
		}
		return self::$objArr[$this->database];
	}

	protected function _getTable(){
		$table = $this->options['table'];
		$this->options['table'] = $this->table;
		return $table;
	}

	protected function _getWhere(){
		$where = $this->options['where'];
		$this->options['where']= array();	
		return $where;
	}

	protected function _getData(){
		$data = $this->options['data'];
		$this->options['data']= array();
		return $data;
	}

	protected function _pager($page, $pageSize = 10, $scope = 10, $total){		
		$page = max(intval($page), 1);
		$totalPage = ceil( $total / $pageSize );
		
		$this->pager = array(		
			'page'=> $page,			
			'pageSize'   => $pageSize,
			'scope'   => $scope,
			'totalPage'  => $totalPage,
			'totalCount' => $total,
			'firstPage'  => 1,
			'prevPage'   => ( ( 1 == $page ) ? 1 : ($page - 1) ),
			'nextPage'   => ( ( $page == $totalPage ) ? $totalPage : ($page + 1)),
			'lastPage'   => $totalPage,			
			'allPages'   => array(),
			'offset'      => ($page - 1) * $pageSize,
			'limit'       => $pageSize,
		);
		
		if($totalPage <= $scope ){
			$this->pager['allPages'] = range(1, $totalPage);
		}elseif( $page <= $scope/2) {
			$this->pager['allPages'] = range(1, $scope);
		}elseif( $page <= $totalPage - $scope/2 ){
			$right = $page + (int)($scope/2);
			$this->pager['allPages'] = range($right-$scope+1, $right);
		}else{
			$this->pager['allPages'] = range($totalPage-$scope+1, $totalPage);
		}
	}
}