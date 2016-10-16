<?php

/**
 * Model post
 */
class Model_Post extends Model_Abstract {

    /**
     * 保存 post 数据
     * @param type $postData
     */
    public static function savePost($postData) {

        if (!isset($postData['create_time'])) {
            $postData['create_time'] = date('Y-m-d H:i:s');
        }

        try {
            $daoPost = Model::getDAO('Post');
            $id = $daoPost->add($postData);
            if (!$id) {
                return false;
            }
            $daoPostViewCount = Model::getDAO('PostViewCount');
            $daoPostViewCount->add(array('id' => $id, 'count' => 0));
            return $id;
        } catch (Exception $ex) {
            V::log('save post failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }
    }

    /**
     * 获取制定 post 信息
     * @param type $id
     */
    public static function getPost($id) {

        try {
            $daoPost = Model::getDAO('Post');
            $post = $daoPost->get($id);
        } catch (Exception $ex) {
            V::log('get post failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }

        $viewCounts = self::_batchGetViewCount(array($id));
        $post['view_num'] = $viewCounts[$id];
        return $post;
    }

    /**
     * 按时间倒序获取 post 列表
     * @param type $page
     * @param type $pageNum
     */
    public static function listByTime($page = 1, $pageNum = 10) {

        try {
            $daoPost = Model::getDAO('Post');
            $data = $daoPost->listAll(array(), array('create_time' => 'desc'), ($page-1)*$pageNum, $pageNum);
        } catch (Exception $ex) {
            V::log('list post failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }

        $list = array();
        $ids = Arr::fetchCols($data, 'id');
        $tmpData = Arr::fetchCols($data, array(), 'id');
        $viewCounts = self::_batchGetViewCount($ids);
        foreach ($ids as $id) {
            if (isset($tmpData[$id])) {
                $list[$id] = $tmpData[$id];
                $list[$id]['view_num'] = $viewCounts[$id];
            }
        }
        return $list;
    }

    /**
     * 获取 post 总数
     */
    public static function countAll() {

        try {
            $daoPost = Model::getDAO('Post');
            return $daoPost->countAll();
        } catch (Exception $ex) {
            V::log('count post failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }
    }

    /**
     * 根据 ID 获取对应 post
     * @param type $ids
     */
    public static function listByIds($ids) {

        try {
            $daoPost = Model::getDAO('Post');
            $data = $daoPost->listByPKs($ids);
        } catch (Exception $ex) {
            V::log('list post failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }

        $list = array();
        $tmpData = Arr::fetchCols($data, array(), 'id');
        $viewCounts = self::_batchGetViewCount($ids);
        foreach ($ids as $id) {
            if (isset($tmpData[$id])) {
                $list[$id] = $tmpData[$id];
                $list[$id]['view_num'] = $viewCounts[$id];
            }
        }
        return $list;
    }

    /**
     * 增加指定评论的查看数
     * @param type $id
     * @param type $cnt
     */
    public static function incrViewCount($id, $cnt = 1) {

        try {
            $daoPostViewCount = Model::getDAO('PostViewCount');
            return $daoPostViewCount->incrCount($id, $cnt);
        } catch (Exception $ex) {
            V::log('incr post view num failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }
    }

    /**
     * 批量获取指定评论的查看数
     * @param array $ids
     */
    private static function _batchGetViewCount($ids) {

        try {
            $daoPostViewCount = Model::getDAO('PostViewCount');
            $rows = $daoPostViewCount->listByPKs($ids);
            $tmpCounts = Arr::fetchCols($rows, 'count', 'id');
            $counts = array();
            foreach ($ids as $id) {
                $counts[$id] = isset($tmpCounts[$id]) ? intval($tmpCounts[$id]) : 0;
            }
            return $counts;
        } catch (Exception $ex) {
            V::log('batch get post view num failed:'.$ex->getMessage(), Logger::LEVEL_WARNING);
            throw new Model_Exception($ex);
        }
    }
}