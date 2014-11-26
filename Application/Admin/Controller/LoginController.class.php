<?php
namespace Admin\Controller;
use Admin\Controller\AdminController;
/**
 * 登录页面
 */
class LoginController extends AdminController {

	/**
     * 登录页面
     */
    public function index(){
        if(!IS_POST){
            $this->display();
        }else{
            $userName = I('post.username');
            $passWord = I('post.password');
            if(empty($userName)||empty($passWord)){
                $this->error('用户名或密码未填写！');
            }
            //查询用户
            $map = array();
            $map['username'] = $userName;
            $userInfo = D('AdminUser')->getWhereInfo($map);
            if(empty($userInfo)){
                $this->error('登录用户不能存在！');
            }
            if(!$userInfo['status']||!$userInfo['group_status']){
                $this->error('该用户已被禁止登录！');
            }
            if($userInfo['password']<>md5($passWord)){
                $this->error('您输入的密码不正确！');
            }
            $model = D('AdminUser');
            if($model->setLogin($userInfo['user_id'])){
                $this->success('登录成功！', U('Index/index'));
            }else{
                $this->error($model->getError());
            }
            

        }
    }
    /**
     * 退出登录
     */
    public function logout(){
        D('AdminUser')->logout();
        session('[destroy]');
        $this->success('退出系统成功！', U('index'));
    }
}

