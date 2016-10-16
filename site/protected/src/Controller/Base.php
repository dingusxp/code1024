<?php
/**
 * Controller_Base
 */

class Controller_Base extends Controller_Abstract {
    
    /**
     * 提示信息类型：信息
     */
    const MESSAGE_INFO = 1;

    /**
     * 提示信息类型：警告
     */
    const MESSAGE_WARNING = 2;

    /**
     * 提示信息类型：错误
     */
    const MESSAGE_ERROR = 3;

    /**
     * 提示信息类型：成功
     */
    const MESSAGE_SUCCESS = 4;

    /**
     * 提示信息页主模板
     * @var type
     */
    protected $_msgTplMessage = 'message';

    /**
     * 提示信息页布局模板
     * @var type
     */
    protected $_msgTplLayout = 'layout';
    
    /**
     * before action
     */
    protected function _beforeAction() {
        
        if (false === parent::_beforeAction()) {
            return false;
        }

        $this->_view->assign('controllerName', strtolower($this->_controllerName));
        $this->_view->assign('actionName', strtolower($this->_actionName));

        return true;
    }
    
    /**
     * 检查 post 提交
     * @param type $formHashKey
     */
    protected function _checkPost($formHashKey = 'formHash') {

        // 提交
        if (!$this->_request->isPost()) {
            return false;
        }

        // referer 检查
        if (!$this->_request->checkReferer()) {
            return false;
        }

        // formhash
        $formHash = $this->_request->post($formHashKey);
        if (!$this->_checkCsrfToken($formHash)) {
            return false;
        }

        return true;
    }

    /**
     * ajax 格式化返回
     * @param type $errno
     * @param type $errmsg
     * @param type $result
     */
    protected function _ajaxReturn($errno = 0, $errmsg = '', $result = null) {

        $result = json_encode(array(
           'errno' => $errno,
            'errmsg' => $errmsg,
            'result' => $result
        ));
        return $this->_response->setBody($result);
    }

    /**
     * 信息提示页
     * @param <type> $message
     * @param <type> $msgType
     * @param <type> $urlForwards
     * @param <type> $redirectTime
     * @return <type>
     */
    protected function _showMessage($message = '', $msgType = self::MESSAGE_INFO, $urlForwards = array(), $redirectTime = -1) {

        // 清空内容先
        $this->_response->setBody('');

        // 前往链接
        if (!is_array($urlForwards) && $urlForwards) {
            $urlForwards = array(
                array('url' => $urlForwards, 'text' => '前进')
            );
        }
        if (!$urlForwards) {
            $urlForwards = array(
                array('url' => urldecode($this->_request->referer()), 'text' => '返回')
            );
        }
        $redirectUrl = $urlForwards[0]['url'];
        if (0 == $redirectTime && !empty($redirectUrl)) {
            return $this->_response->redirect($urlForwards[0]['url']);
        }

        // 提示框样式
        $msgTypes = array(
            self::MESSAGE_INFO => 'info',
            self::MESSAGE_WARNING => 'warning',
            self::MESSAGE_ERROR => 'error',
            self::MESSAGE_SUCCESS => 'success',
        );
        if (!array_key_exists($msgType, $msgTypes)) {
            $msgType = self::MESSAGE_INFO;
        }

        $this->_view->assign('title', '提示信息');
        $this->_view->assign('message', $message);
        $this->_view->assign('messageType', $msgTypes[$msgType]);
        $this->_view->assign('urlForwards', $urlForwards);
        $this->_view->assign('redirectUrl', $redirectUrl);
        $this->_view->assign('redirectTime', $redirectTime > 0 ? intval($redirectTime) : 0);
        return $this->_view->renderLayout($this->_msgTplMessage, $this->_msgTplLayout, array(), View::LAYOUT_REBUILD_RESOURCE);
    }
}