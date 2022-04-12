<?php

namespace Core\Model;

use Core\{
    Utilities\Util,
    Database\Mysql as Db,
};

use App\Config\Mysql as MysqlConfig;

class Mysql {

    private $db;
    private $master;
    private $table = null;
    private $select = [];
    private $where = [];
    private $alias = null;
    private $values = [];
    private $order = [];
    private $limit = null;
    private $query = '';
    private $debug = false;
    private $single = false;
    
    public function __construct(){
       
        $this->db = new Db();
        $this->master = MysqlConfig::getMaster();

    }

    private function empty(){
        $this->table = null;
        $this->select = [];
        $this->where = [];
        $this->values = [];
        $this->order = [];
        $this->query = '';
        $this->limit = null;
        $this->debug = false;
        $this->single = false;
    }

    public function single(){
        $this->single = true;

        return $this;
    }

    public function table($table){
        $this->empty();
        $this->table = "`{$table}`";
        $this->where($this->master['deleted'], '=', 'N');
        return $this;
    }

    public function select(){

        foreach ((array)func_get_args() as $arg){
            $this->select[] = $arg;
        }

        return $this;
    }

    public function includeDeleted(){

      foreach($this->where as $column => $value){
        if(substr($value, 0, 9)=='`'.$this->master['deleted'].'`'){
          unset($this->where[$column]);
        }
      }

        return $this;
    }

    public function printDebug($query){
        $debug = [
            'table' => $this->table,
            'values' => $this->values,
            'where' => $this->where,
            'order' => $this->order,
            'limit' => $this->limit,
            'query' => $query
        ];
        Util::dump($debug);
    }

    public function debug(){

        $this->debug = true;

        return $this;
    }

    public function asAlias($alias){
        $this->alias = $alias;
        return $this;
    }

    public function count(){
        $this->count = true;
        return $this;
    }

    public function distinct(){
        $this->distinct = true;
        return $this;
    }

    public function where($column, $reg = false, $value = false){

        $this->setWhere('AND');

        if(!$value){ $this->where[] = '`'.$column.'` LIKE \''.$reg.'\''; }
        else { $this->where[] = '`'.$column.'` '.$reg.' \''.$value.'\''; }

        return $this;
    }

    public function orWhere($column, $reg = false, $value = false){

        $this->setWhere('OR');
        if(!$value){ $this->where[] = '`'.$column.'` LIKE \''.$reg.'\''; }
        else { $this->where[] = '`'.$column.'` '.$reg.' \''.$value.'\''; }

        return $this;
    }

    public function whereBetween($column, array $list){
        $this->setWhere('AND');

        $this->where[] = '`'.$column.'` BETWEEN \''.$list[0].'\' AND \''.$list[1].'\'';

        return $this;
    }

    public function whereNotBetween($column, array $list){
        $this->setWhere('AND');

        $this->where[] = '`'.$column.'` NOT BETWEEN \''.$list[0].'\' AND \''.$list[1].'\'';

        return $this;
    }

    public function whereNull($column){
        $this->setWhere('AND');

        $this->where[] = '(`'.$column.'` IS NULL OR `'.$column.'` = \'\')';

        return $this;
    }

    public function whereId($value){
        $this->setWhere('AND');

        $this->where[] = 'id = \''.$value.'\'';

        return $this;
    }

    public function whereIn($column, array $list){
        $this->setWhere('AND');

        $this->where[] = '`'.$column.'` IN (\''.implode('\',\'',$list).'\')';

        return $this;
    }

    public function whereNotIn($column, array $list){
        $this->setWhere('AND');

        $this->where[] = '`'.$column.'` NOT IN (\''.implode('\',\'',$list).'\')';

        return $this;
    }

    public function orderBy($order, $type = false){
        if(is_array($order)){
            foreach($order as $o){
                $this->order[] = $o;
            }
        } else {
            $this->order[] = Array($order, (!$type? 'DESC' : $type));
        }

        return $this;
    }

    public function limit($limit){

        $this->limit = $limit;


        return $this;
    }

    public function sql($sql){
        $this->db->query($sql);
        $this->execute();
    }

    public function get(){

        $query = 'SELECT ';
        $query .= ($this->count ? 'COUNT( ' : '');
        $query .= ($this->distinct ? 'DISTINCT ' : '');
        $query .= (empty($this->select) ? '*' : implode(', ', $this->select) . '');

        $query .= ($this->count ? ' ) ' : '');
        $query .= (null !== $this->alias ? ' AS `' . $this->alias . '`' : '');
        $query .= ' FROM ' . $this->table;

        if(count($this->where)>0){
            $query .= ' WHERE ';
            $query .= implode(' ', $this->where);
        } else {
            $query .= ' WHERE '.$this->master['deleted'].' = "N"';
        }

        if(count($this->order)>0){
            $query .= ' ORDER BY ';
            foreach($this->order as $o){
                $query .= implode(' ', $o).', ';
            }

            $query = rtrim($query, ', ');
        }

        if(!is_null($this->limit)){
            $query .= ' limit '.$this->limit;
        }

        $query = str_replace('  ', ' ', $query);

        if($this->debug){ $this->printDebug($query); }

        $this->query = $query;

        $this->db->query($this->query);

        return $this->single? $this->db->single() : $this->db->resultset();
    }

    public function value($column, $value){

        $this->values[$column] = $value;
        return $this;
    }

    public function values(Array $values){

        foreach($values as $column => $value){
            $this->values[$column] = $value;
        }

        return $this;
    }

    public function insert(){

        $query = 'INSERT INTO '.$this->table;
        $query .=' (';

        $this->values[$this->master['created']] = date('Y-m-d H:i:s');
        $this->values[$this->master['updated']] = date('Y-m-d H:i:s');

        foreach($this->values as $column => $value){
            $query .='`'.$column.'`, ';
        }

        $query = rtrim($query, ', ');
        $query .=') VALUES';
        $query .=' (';

        $values = [];
        $columns = [];
        foreach($this->values as $column => $value){
            $query .=':'.$column.', ';

        }

        $query = rtrim($query, ', ').')';

        if($this->debug){ $this->printDebug($query); }

        $this->query = $query;

        $this->db->query($this->query);

        foreach($this->values as $column => $value){
            $this->db->bind(':'.$column, $value);
        }

        $this->execute();

        return $this->db->lastInsertId();
    }

    public function update(){

        $query = 'UPDATE '.$this->table;
        $query .=' SET ';

        $this->values[$this->master['updated']] = date('Y-m-d H:i:s');
        foreach($this->values as $column => $value){
            $query .= $column.'= :'.$column.', ';
        }
        $query = rtrim($query, ', ');

        if(count($this->where)>0){
            $query .= ' WHERE ';
            $query .= implode(' ', $this->where);
        }

        if($this->debug){ $this->printDebug($query); }

        $this->query = $query;
        $this->db->query($this->query);

        foreach($this->values as $column => $value){
            $this->db->bind(':'.$column, $value);
        }

        $this->execute();
    }

    public function delete(){

        $this->values[$this->master['deleted']] = 'Y';

        $query = 'DELETE from '.$this->table;

        if(count($this->where)>0){
            $query .= ' WHERE ';
            $query .= implode(' ', $this->where);
        }

        if($this->debug){ $this->printDebug($query); }

        $this->query = $query;

        $this->db->query($this->query);

        $this->execute();
    }

    public function truncate(){

        $query = 'TRUNCATE '.$this->table;

        if($this->debug){ $this->printDebug($query); }

        $this->query = $query;
        $this->db->query($this->query);

        $this->db->execute();
    }

    private function setWhere($type){
        if(count($this->where)>0){ $this->where[count($this->where)-1] .= ' '.$type.' '; }
    }

    private function execute(){
        $this->db->execute();

        return $this->db->lastInsertId();
    }

    private function rebuild(){
        $this->table = null;
        $this->select = [];
        $this->where = [];
        $this->values = [];
        $this->query = '';
    }

}
