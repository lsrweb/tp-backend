<?php

namespace app\school\controller\v1;

use app\glob\controller\Excel;
use app\school\validate\QuestValidate;
use think\exception\ValidateException;
use think\Filesystem;

class Question extends BaseController
{
    // 题库列表
    public function questList(): \think\response\Json
    {
        $params = input('post.');
        $page = $params['page'] ?? 1;
        $pageSize = $params['pageSize'] ?? 10;
        $questList = \app\common\model\QuestionModel::where('status', 1)->page($page, $pageSize)->select();
        $questList = $questList->toArray();
        $questList = array_map(function ($item) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            return $item;
        }, $questList);
        return successMsg('获取成功', $questList);
    }

    // 题库添加
    public function questAdd(): \think\response\Json
    {
        $params = input('post.');
        try {
            validate(QuestValidate::class)->scene('add')->check($params);


            return successMsg(1);

        } catch (ValidateException $e) {
            return errorMsg($e->getMessage());
        }
    }

    // 上传 xlx表格文件题库
    public function uploadXls(): \think\response\Json
    {
        $params = input('post.');
        try {
            validate(QuestValidate::class)->scene('add')->check($params);
            if (request()->file('file')) {
                $xlsResultGet = Excel::importExcel($this->request->file('file'));
                // 函数循环遍历每一项,去除空的值,并且去掉每一项下标为0的值

                $xlsResult = array_filter($xlsResultGet['data'], function ($item) {
                    return !empty($item[1]) && !empty($item[2]);
                });
                // 再次循环删除下标为0的元素 foreach
                foreach ($xlsResult as $key => $value) {
                    array_splice($xlsResult[$key], 0, 1);
                    // 删除 null || "null" 的元素
                    $xlsResult[$key] = array_filter($xlsResult[$key], function ($item) {
                        return !empty($item);
                    });
                    // 再次循环每一项,以空格分割,区分选项和分值
                    foreach ($xlsResult[$key] as $k => $v) {
                        $xlsResult[$key][$k] = explode(' ', $v);
                        // 获取title,下标为0的为title
                        $title = $xlsResult[$key][0][0];
                    }
                    $xlsResult[$key]['title'] = $title;
                }
                // 得到最终结果存入数据库
                foreach ($xlsResult as $key => $value) {
                    // 删除下标0
                    array_splice($value, 0, 1);
                    $data = [
                        'ques_title' => $value['title'],
                        'option' => $value,
                    ];
                    // 存库
                    \app\common\model\QuestionModel::create($data);
                }

            }
            unlink(public_path() . 'upload/' . $xlsResultGet['fileName']);

            return successMsg(1, $data);

        } catch (ValidateException $e) {
            // 上传出现问题时删除文件
            unlink(public_path() . 'upload/' . $xlsResultGet['fileName']);

            return errorMsg($e->getMessage());
        }
    }


    // 题目禁用
    public function questDisable(): \think\response\Json
    {
        $params = input('post.disarray');
        $quest = \app\common\model\QuestionModel::where('id', $params['id'])->update(['status' => 0]);
        return successMsg('禁用成功');
    }

    // 删除题目
    public function questDelete(): \think\response\Json
    {
        $params = input('post.');
        $quest = \app\common\model\QuestionModel::where('id', $params['id'])->delete();
        return successMsg('删除成功');
    }


}