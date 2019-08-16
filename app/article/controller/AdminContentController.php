<?php
namespace app\article\controller;

use app\admin\controller\AdminController;

/**
 * 文章列表
 */

class AdminContentController extends AdminController
{
    /**
     * 当前模块参数
     */
    protected function _infoModule()
    {
        return array(
            'info'  => array(
                'name' => '文章管理',
                'description' => '管理网站的所有文章',
                ),
            'menu' => array(
                    array(
                        'name' => '文章列表',
                        'url' => url('index'),
                        'icon' => 'list',
                    ),
                    
                ),
            'add' => array(
                    array(
                        'name' => '添加文章',
                        'url' => url('add'),
                        'icon' => 'plus',
                    ),
                )
            );
    }
    /**
     * 列表
     */
    public function index()
    {
        //筛选条件
        $where = array();
        $keyword = request('request.keyword', '');
        $classId = request('request.class_id', 0, 'intval');
        $positionId = request('request.position_id', 0, 'intval');
        $status = request('request.status', 0, 'intval');
        if (!empty($keyword)) {
            $where[] = 'A.title like "%'.$keyword.'%"';
        }
        if (!empty($classId)) {
            $where['C.class_id'] = $classId;
        }
        if (!empty($positionId)) {
            $where[] = 'find_in_set('.$positionId.',position) ';
        }
        if (!empty($status)) {
            switch ($status) {
                case '1':
                    $where['A.status'] = 1;
                    break;
                case '2':
                    $where['A.status'] = 0;
                    break;
            }
        }
        //URL参数
        $pageMaps = array();
        $pageMaps['keyword'] = $keyword;
        $pageMaps['status'] = $status;
        $pageMaps['class_id'] = $classId;
        $pageMaps['position_id'] = $positionId;
        //查询数据
        $list = target('ContentArticle')->page(30)->loadList($where, $limit);
        $this->pager = target('ContentArticle')->pager;
        //位置导航
        $breadCrumb = array('文章列表'=>url());
        //模板传值
        $this->assign('breadCrumb', $breadCrumb);
        $this->assign('list', $list);
        $this->assign('page', $this->getPageShow($pageMaps));
        $this->assign('categoryList', target('duxcms/Category')->loadList());
        $this->assign('positionList', target('duxcms/Position')->loadList());
        $this->assign('pageMaps', $pageMaps);

        // 获取百度链接提交配置
        $file = CONFIG_PATH . 'push.php';
        $pushConfig = load_config($file);
        $this->assign('push_open', $pushConfig['push_open']);

        $this->adminDisplay();
    }

    /**
     * 增加
     */
    public function add()
    {
        if (!IS_POST) {
            $breadCrumb = array('文章列表'=>url('index'),'文章添加'=>url());
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '添加');
            $this->assign('categoryList', target('duxcms/Category')->loadList());
            $this->assign('tplList', target('admin/Config')->tplList());
            $this->assign('positionList', target('duxcms/Position')->loadList());
            $this->assign('default_config', current_config());
            $this->adminDisplay('info');
        } else {
            if (target('ContentArticle')->saveData('add')) {
                $this->success('文章添加成功！', url('index'));
            } else {
                $msg = target('ContentArticle')->getError();
                if (empty($msg)) {
                    $this->error('文章添加失败');
                } else {
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 修改
     */
    public function edit()
    {
        if (!IS_POST) {
            $contentId = request('get.content_id', '', 'intval');
            if (empty($contentId)) {
                $this->error('参数不能为空！');
            }
            //获取记录
            $model = target('ContentArticle');
            $info = $model->getInfo($contentId);
            if (!$info) {
                $this->error($model->getError());
            }
            $breadCrumb = array('文章列表'=>url('index'),'文章修改'=>url('', array('content_id'=>$contentId)));
            $this->assign('breadCrumb', $breadCrumb);
            $this->assign('name', '修改');
            $this->assign('info', $info);
            $this->assign('categoryList', target('duxcms/Category')->loadList());
            $this->assign('tplList', target('admin/Config')->tplList());
            $this->assign('positionList', target('duxcms/Position')->loadList());
            $this->assign('default_config', current_config());
            $this->adminDisplay('info');
        } else {
            if (target('ContentArticle')->saveData('edit')) {
                $this->success('文章修改成功！', url('index'));
            } else {
                $msg = target('ContentArticle')->getError();
                if (empty($msg)) {
                    $this->error('文章修改失败');
                } else {
                    $this->error($msg);
                }
            }
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $contentId = request('post.data', 0, 'intval');
        if (empty($contentId)) {
            $this->error('参数不能为空！');
        }
        if (target('ContentArticle')->delData($contentId)) {
            $this->success('文章删除成功！');
        } else {
            $this->error('文章删除失败！');
        }
    }

    /**
     * 批量操作
     */
    public function batchAction()
    {
        $type = request('post.type', 0, 'intval');
        $ids = request('post.ids');
        $classId = request('post.class_id', 0, 'intval');
        if (empty($type)) {
            $this->error('请选择操作！');
        }
        if (empty($ids)) {
            $this->error('请先选择操作项目！');
        }
        if ($type == 3) {
            if (empty($classId)) {
                $this->error('请选择操作栏目！');
            }
        }
        foreach ($ids as $id) {
            $data = array();
            $data['content_id'] = $id;
            switch ($type) {
                case 1:
                    //发布
                    $data['status'] = 1;
                    target('duxcms/Content')->editData($data);
                    break;
                case 2:
                    //草稿
                    $data['status'] = 0;
                    target('duxcms/Content')->editData($data);
                    break;
                case 3:
                    $data['class_id'] = $classId;
                    target('duxcms/Content')->editData($data);
                    break;
                case 4:
                    //删除
                    target('ContentArticle')->delData($id);
                    break;
            }
        }
        $this->success('批量操作执行完毕！');
    }

    /**
     * 实时推送文章链接到百度
     *
     * @return void
     */
    public function pushArticle()
    {
        $aid = request('post.data', 0, 'intval');
        
        if (!$aid) {
            $this->error('参数异常');
        }

        $file = CONFIG_PATH . 'performance.php';
        $config = load_config($file);

        // 伪静态
        if ($config['REWRITE_ON']) {
            \framework\base\Route::parseUrl( config('REWRITE_RULE'), true );

            $app = 'article/Content/index';
            $url = url($app, ['content_id'=>$aid]);

            $res = push($url);
            $res = json_decode($res);

            if ($res->error) {
                $this->error('推送失败：' . $res->message);
            }

            // 由于不是本站url而未处理的url列表
            $not_same_site = '';
            if ($res->not_same_site) {
                $sites = implode(',', $res->not_same_site);
                $not_same_site = '<br/>未处理(不是本站url)的url<br/>' . $sites;
            }

            // 不合法的url列表
            $not_valid = '';
            if ($res->not_valid) {
                $site = implode(',', $res->not_valid);
                $not_valid = '<br/>不合法的url列表:<br/>' . $sites;
            }

            $this->success("推送成功！<br/>成功: {$res->success}条，剩余: {$res->remain}条" . $not_same_site . $not_valid);
        } else {
            $this->error('检测发现，未开启伪静态模式！');
        }
    }
}
