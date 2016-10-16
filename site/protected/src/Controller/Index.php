<?php
/**
 * controller index
 */

class Controller_Index extends Controller_Base {

    /**
     * index
     */
    public function actionIndex() {

        try{
            $ids = V::config('recommend-post-ids');
			$postList = array();
			if ($ids) {
                $postList = Model_Post::listByIds($ids);
			}
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR);
        }

        // pic url && tpl name
        try {
            $tplCfg = V::config('post_tpls');
            $picstore = PicStore::getInstance('picstore');
            foreach ($postList as &$postData) {
                $postData['show_id'] = $this->_encryptPostId($postData['id']);
                $postData['pic_url'] = $picstore->getUrl($postData['pic_id']);
                $postData['list_url'] = $picstore->getUrl($postData['pic_id'], 'list');
                $postData['tpl_name'] = $tplCfg[$postData['tpl']]['name'];
            }
            unset($postData);
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR);
        }

        $tplData = array(
            'postTplCfg' => V::config('post_tpls'),
            'postList' => $postList,
        );
        return $this->_view->renderLayout('index', 'layout', $tplData, View::LAYOUT_REBUILD_RESOURCE);
    }

    /**
     * action list
     * 首页
     * @return type
     */
    public function actionList() {

        $page = $this->_request->get('page', 1);
        $page = max(1, $page);
        $pageNum = 20;

        try{
            $postNum = Model_Post::countAll();
            $postList = array();
            if ($postNum > ($page - 1) * $pageNum) {
                $postList = Model_Post::listByTime($page, $pageNum);
            }
        } catch (Exception $ex) {
            V::log($ex, true);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR);
        }

        // pic url && tpl name
        try {
            $tplCfg = V::config('post_tpls');
            $picstore = PicStore::getInstance('picstore');
            foreach ($postList as &$postData) {
                $postData['show_id'] = $this->_encryptPostId($postData['id']);
                $postData['pic_url'] = $picstore->getUrl($postData['pic_id']);
                $postData['list_url'] = $picstore->getUrl($postData['pic_id'], 'list');
                $postData['tpl_name'] = $tplCfg[$postData['tpl']]['name'];
            }
            unset($postData);
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR);
        }

        $tplData = array(
            'postNum' => $postNum,
            'postList' => $postList,
            'pagination' => HTML::widget('pager', array(
                'total' => $postNum,
                'perpage' => $pageNum,
                'current' => $page,
                'url' => '/',
            )),
        );

        return $this->_view->renderLayout('list', 'layout', $tplData, View::LAYOUT_REBUILD_RESOURCE);
    }

    /**
     * 查看指定作品
     */
    public function actionView() {

        $urlForward = array('text' => '返回首页', 'url' => '/');
        $id = intval($this->_decryptPostId($this->_request->get('id')));
        if (!$id) {
            return $this->_showMessage('指定的作品不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        try{
            $postData = Model_Post::getPost($id);
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR, array($urlForward), 3);
        }
        if (!$postData) {
            return $this->_showMessage('指定的作品不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        // pic url && tpl name
        try {
            $tplCfg = V::config('post_tpls');
            $picstore = PicStore::getInstance('picstore');
            $postData['show_id'] = $this->_encryptPostId($postData['id']);
            $postData['pic_url'] = $picstore->getUrl($postData['pic_id']);
            $postData['preview_url'] = $picstore->getUrl($postData['pic_id'], 'preview');
            $postData['tpl_name'] = $tplCfg[$postData['tpl']]['name'];
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        // extra show
        $postData['allow_download'] = false;
        $postData['allow_show'] = false;
        $tplCfg = V::config('post_tpls');
        if (!empty($tplCfg[$postData['tpl']]['show_tpl'])) {
            $postData['allow_show'] = true;
        }
        if (!empty($tplCfg[$postData['tpl']]['download_tpl'])) {
            $postData['allow_download'] = true;
        }

        // 增加查看数
        try {
            Model_Post::incrViewCount($id);
            $postData['view_num'] += 1;
        } catch (Exception $ex) {
            // ignore
        }

        $tplData = array('postData' => $postData);
        return $this->_view->renderLayout(array('extraMeta' => '_view-meta', 'mainContent' => 'view'), 'layout', $tplData, View::LAYOUT_REBUILD_RESOURCE);
    }

    /**
     * 在线展示
     */
    public function actionShow() {

        $urlForward = array('text' => '返回首页', 'url' => '/');
        $id = intval($this->_decryptPostId($this->_request->get('id')));
        if (!$id) {
            return $this->_showMessage('指定的作品不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        try{
            $postData = Model_Post::getPost($id);
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR, array($urlForward), 3);
        }
        if (!$postData) {
            return $this->_showMessage('指定的作品不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        $tplCfg = V::config('post_tpls');
        if (!isset($tplCfg[$postData['tpl']]) || empty($tplCfg[$postData['tpl']]['show_tpl'])) {
            return $this->_showMessage('该作品不支持在线展示！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        $this->_view->assign('code', $postData['code']);
        return $this->_view->render($tplCfg[$postData['tpl']]['show_tpl']);
    }

    /**
     * 下载 投递代码 或 模版
     */
    public function actionDownload() {

        $urlForward = array('text' => '返回首页', 'url' => '/');
        $downloadTpl = '';
        $code = '';
        $tplCfg = V::config('post_tpls');

        // 下载代码
        $id = $this->_request->get('id');
        if ($id) {
            $id = intval($this->_decryptPostId($id));
            if (!$id) {
                return $this->_showMessage('指定的作品不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
            }

            try{
                $postData = Model_Post::getPost($id);
            } catch (Exception $ex) {
                V::log($ex);
                return $this->_showMessage('系统繁忙，请稍候再试！', self::MESSAGE_ERROR, array($urlForward), 3);
            }
            if (!$postData) {
                return $this->_showMessage('指定的作品不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
            }
            if (!isset($tplCfg[$postData['tpl']]) || empty($tplCfg[$postData['tpl']]['download_tpl'])) {
                return $this->_showMessage('该作品的代码不支持下载！', self::MESSAGE_ERROR, array($urlForward), 3);
            }
            $downloadTpl = $tplCfg[$postData['tpl']]['download_tpl'];
            $code = $postData['code'];
        }

        // 下载模版
        $tpl = trim($this->_request->get('tpl'));
        if ($tpl) {
            if (!isset($tplCfg[$tpl]) || empty($tplCfg[$tpl]['download_tpl'])) {
                return $this->_showMessage('指定的模版不存在！', self::MESSAGE_ERROR, array($urlForward), 3);
            }
            $downloadTpl = $tplCfg[$tpl]['download_tpl'];
        }

        if (!$downloadTpl) {
            return $this->_showMessage('未指定要下载的代码或模板！', self::MESSAGE_ERROR, array($urlForward), 3);
        }

        $this->_response->setHeader('content-type', 'text/plain;charset=utf8');
        $this->_view->assign('code', $code);
        return $this->_view->render($downloadTpl);
    }

    /**
     * action post
     * 投递作品
     * @return type
     */
	public function actionPost() {

        $tplData = array(
            'formHash' => $this->_getCsrfToken(),
            'postTplCfg' => V::config('post_tpls'),
        );
        return $this->_view->renderLayout('post', 'layout', $tplData, View::LAYOUT_REBUILD_RESOURCE);
	}

    /**
     * 投递提交
     * @return type
     */
    public function actionPostSubmit() {

        // 提交检查
        if (!$this->_checkPost()) {
            return $this->_ajaxReturn(1, '不合法的访问');
        }

        $postData = $this->_request->postx('title', 'author', 'email', 'tpl', 'code', 'pic_id');
        $postData['title'] = trim($postData['title']);
        $postData['author'] = trim($postData['author']);
        $postData['email'] = trim($postData['email']);
        $postData['tpl'] = trim($postData['tpl']);
        $postData['code'] = $postData['code'];
        $postData['pic_id'] = trim($postData['pic_id']);

        // check param
        try {
            if (empty($postData['title']) || empty($postData['author']) || empty($postData['tpl'])
                    ||  empty($postData['code']) ||  empty($postData['pic_id'])) {

                return $this->_ajaxReturn(1, '必填项未全部填写');
            }

            $tplCfg = V::config('post_tpls');
            if (empty($tplCfg[$postData['tpl']])) {
                return $this->_ajaxReturn(1, '不合法的模版');
            }

            if (strlen($postData['code']) > 1024) {
                return $this->_ajaxReturn(1, '代码长度超出 1024！');
            }

            if (!empty($postData['email']) && !Validator::validate('email', $postData['email'])) {
                return $this->_ajaxReturn(1, '邮箱格式不合法');
            }
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_ajaxReturn(2, '校验提交信息出错！');
        }

        try {
            $id = Model_Post::savePost($postData);
            if (!$id) {
                return $this->_ajaxReturn(2, '保存作品失败！');
            }
            return $this->_ajaxReturn(0, 'ok', $this->_encryptPostId($id));
        } catch (Exception $ex) {
            V::log($ex);
            return $this->_ajaxReturn(2, '保存作品失败！');
        }
    }

    /**
     * 上传图片
     */
    public function actionUpload() {

        // 提交检查
        if (!$this->_checkPost()) {
            return $this->_ajaxReturn(1, '不合法的访问');
        }

        // 检查图片
        if (empty($_FILES['pic'])) {
            return $this->_ajaxReturn(1, '未提交图片');
        }

        $picFile = $_FILES['pic'];
        $picExt = strtolower(FS::getExt($picFile['name']));
        if (!in_array($picExt, array('jpg', 'png', 'gif'))) {
            return $this->_ajaxReturn(1, '提交的不是合法的图片');
        }

        $tmpDir = '';
        try {
            // 生成一个临时目录
            $tmpDir = FS::tmpDir();

            // 保存并上传图片到 picstore
            $picstore = PicStore::getInstance('picstore');
            $tmpFile = $tmpDir .'/'.md5($picFile['name']).'.'.$picExt;
            $ret = move_uploaded_file($picFile['tmp_name'], $tmpFile);
            if (!$ret) {
                throw new Exception('转存图片失败！');
            }
            $picId = $picstore->store($tmpFile, 'post_pic');

            // 清除临时目录
            FS::rmdir($tmpDir);
        } catch (Exception $ex) {

            // 清除临时目录
            if ($tmpDir) {
                FS::rmdir($tmpDir);
            }

            V::log($ex);

            return $this->_ajaxReturn(2, '保存图片失败');
        }

        $picUrl = $picstore->getUrl($picId);
        $previewUrl = $picstore->getUrl($picId, 'preview');
        $result = array(
            'pic_id' => $picId,
            'preview_url' => $previewUrl,
            'pic_url' => $picUrl,
        );
        return $this->_ajaxReturn(0, 'ok', $result);
    }

    /**
     * 加密 post id
     * @param type $id
     */
    private function _encryptPostId($id) {
        $cryptor = $this->_getAuthCryptor();
        $str = $cryptor->encrypt((string)$id, 'post_id');
        $len = strlen($str);
        $str = rtrim($str, '=');
        $n = $len - strlen($str);
        if ($n == 1) {
            $str .= chr(65 + (Str::hash($str, 26)));// A-Z
        } elseif ($n == 2) {
            $str .= chr(97 + (Str::hash($str, 26)));// a-z
        } else {
            $str .= chr(48 + (Str::hash($str, 10))); // 0-9
        }
        $str = str_replace('+', '-', $str);
        $str = str_replace('\\', '_', $str);
        return $str;
    }

    /**
     * 解密 post id
     * @param type $str
     */
    private function _decryptPostId($str) {
        if (!$str || strlen($str) < 1) {
            return false;
        }
        $cryptor = $this->_getAuthCryptor();
        $c = ord(substr($str, -1));
        $str = substr($str, 0, -1);
        if ($c >= 65 && $c <= 90) {
            $str .= '=';
        } elseif ($c >= 97 && $c <= 122) {
            $str .= '==';
        }
        $str = str_replace('-', '+', (string)$str);
        $str = str_replace('_', '\\', (string)$str);
        return $cryptor->decrypt($str, 'post_id');
    }
}
