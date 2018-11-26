<?php

namespace backend\controllers;

use backend\models\Admin;
use common\helper\CommonHelper;
use Yii;
use yii\web\Controller;
use yii\web\Request;
use yii\web\Response;

abstract class BaseController extends Controller
{
    /**
     * 管理员ID
     * @var integer
     */
    public $adminId;

    /**
     * 管理员账号名
     * @var string
     */
    public $adminName;

    /**
     * 请求组件
     * @var Request
     */
    public $request;

    /**
     * 是否为ajax请求
     * @var bool
     */
    public $isAjax = false;

    /**
     * 独立操作
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
        ];
    }

    /**
     * 响应状态码
     * @var integer
     */
    const CODE_SUCCESS = 1;
    const CODE_FAIL = 0;

    /**
     * 初始化
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

        // 简便获取登录信息
        if (Yii::$app->user->isGuest === false) {
            $this->adminId = Yii::$app->user->identity->getId();
            $this->adminName = Yii::$app->user->identity->username;
            // 检测是否启用，未启用退出
            if (Yii::$app->user->identity->state != Admin::STATE_ENABLE) {
                Yii::$app->user->logout();
            }
        }

        // 简化获取Request对象
        $this->request = Yii::$app->request;

        // 简便获取ajax请求及模拟请求
        if ($this->request->get('is_ajax') == 1) {
            $this->isAjax = true;
        }else {
            $this->isAjax = $this->request->isAjax;
        }
    }

    /**
     * 处理弹层弹出后的页面样式问题
     *
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id != 'index') {
            $this->layout = 'layer.php';
        }
        return parent::beforeAction($action);
    }

    /**
     * 响应返回JSON数据
     * @param integer $code
     * @param string $msg
     * @param array $data
     * @param array $errorInfo
     */
    public function responseJson($code, $msg = '', $data = [], $errorInfo = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->content = json_encode(CommonHelper::responseJson($code, $msg, $data, $errorInfo));
        Yii::$app->response->send();
        exit;
    }

    /**
     * 响应成功情况下返回的JSON
     *
     * @param string $msg
     * @param array $data
     * @param array $errorInfo
     */
    public function successResponseJson($msg = '', $data = [], $errorInfo = [])
    {
        $this->responseJson(self::CODE_SUCCESS, $msg, $data, $errorInfo);
    }

    /**
     * 响应成功情况下返回的JSON
     *
     * @param string $msg
     * @param array $data
     * @param array $errorInfo
     */
    public function failResponseJson($msg = '', $data = [], $errorInfo = [])
    {
        $this->responseJson(self::CODE_FAIL, $msg, $data, $errorInfo);
    }

    public function layuiListResponseJson($msg = '', $count = 0, $data = [])
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->content = json_encode([
            'code'  => 0,
            'msg'   => $msg,
            'count' => $count,
            'data'  => $data,
        ]);
        Yii::$app->response->send();
        exit;
    }

}
