<?php

/**
 * DAO PostViewCount
 */
class DAO_PostViewCount extends DAO_Base {
    
    /**
     * 增加查看数
     * @param type $id
     * @param type $cnt
     */
    public function incrCount($id, $cnt = 1) {
        
        return $this->update($id, array('count' => array('+', intval($cnt))));
    }   
}