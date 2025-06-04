<?php
namespace app\common\model;

use think\db\Query;
use think\Model;

/**
 * @title 商品组模型
 * @desc 商品组模型
 * @use app\common\model\ProductGroupModel
 */
class ProductGroupModel extends Model
{
    protected $name = 'product_group';

    // 设置字段信息
    protected $schema = [
        'id'                => 'int',
        'name'              => 'string',
        'hidden'            => 'int',
        'order'             => 'int',
        'parent_id'         => 'int',
        'create_time'       => 'int',
        'update_time'       => 'int',
        'type'              => 'string',
    ];

    /**
     * 时间 2022-5-17
     * @title 获取商品一级分组
     * @desc 获取商品一级分组
     * @author wyh
     * @version v1
     * @return array list - 商品一级分组
     * @return int list[].id - 商品一级分组ID
     * @return int list[].name - 商品一级分组名称
     * @return int list[].hidden - 是否隐藏0否1是
     * @return int list[].type - 分组类型：type=domain表示域名
     * @return int count - 商品一级分组总数
     */
    public function productGroupFirstList()
    {
        $app = app('http')->getName();

        $where = function (Query $query) use ($app){
            if($app=='home'){
                $query->where('hidden',0);
            }/*else{
                $query->where('type','<>','domain');
            }*/
            $query->where('parent_id',0);
            //$query->where('name','<>','应用商店');
        };

        $group = $this->field('id,name,hidden,type')
            ->where($where)
            ->order('order','desc')
            ->select()
            ->toArray();
        foreach ($group as $key => $value) {
            $multiLanguage = hook_one('multi_language', [
                'replace' => [
                    'name' => $value['name'],
                ],
            ]);
            if($app=='home'){
                unset($group[$key]['hidden']);
                if(isset($multiLanguage['name'])){
                    $group[$key]['name'] = $multiLanguage['name'];
                }
            }else{
                $group[$key]['customfield']['multi_language'] = $multiLanguage;
            }
        }

        return ['list'=>$group,'count'=>count($group)];
    }

    /**
     * 时间 2022-5-17
     * @title 获取商品二级分组
     * @desc 获取商品二级分组
     * @author wyh
     * @version v1
     * @param int param.id - 一级分组ID
     * @return array list - 商品二级分组
     * @return int list[].id - 商品二级分组ID
     * @return int list[].name - 商品二级分组名称
     * @return int list[].parent_id - 商品一级分组ID
     * @return int list[].hidden - 是否隐藏0否1是
     * @return int list[].type - 分组类型：type=domain表示域名
     * @return int count - 商品二级分组总数
     */
    public function productGroupSecondList($param)
    {
        $app = app('http')->getName();

        $where = function (Query $query) use ($param,$app){
            if($app=='home'){
                $query->where('pg.hidden',0);
            }/*else{
                $query->where('type','<>','domain');
            }*/
            if (!empty($param['id']) && intval($param['id'])>0){
                $query->where('pg.parent_id',intval($param['id'])); # 获取指定一级分组下的二级分组
            }else{
                $query->where('pg.parent_id','>',0); # 获取所有二级分组
            }
            //$query->where('pgf.name','<>','应用商店');
        };

        $group = $this->alias('pg')->field('pg.id,pg.name,pg.parent_id,pg.hidden,pg.type')
            //->leftJoin('product_group pgf','pgf.id=pg.parent_id')
            ->where($where)
            ->order('pg.order','desc')
            ->select()
            ->toArray();
        foreach ($group as $key => $value) {
            $multiLanguage = hook_one('multi_language', [
                'replace' => [
                    'name' => $value['name'],
                ],
            ]);
            if($app=='home'){
                unset($group[$key]['hidden']);

                if(isset($multiLanguage['name'])){
                    $group[$key]['name'] = $multiLanguage['name'];
                }
            }else{
                $group[$key]['customfield']['multi_language'] = $multiLanguage;
            }
        }
        return ['list'=>$group,'count'=>count($group)];
    }

    /**
     * 时间 2022-5-17
     * @title 新建商品分组
     * @desc 新建商品分组
     * @author wyh
     * @version v1
     * @param string param.name 电脑 分组名称 required
     * @param int param.id 1(传0表示创建一级分组) 分组ID required
     * @param int param.hidden 0 是否隐藏0放1是
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function createProductGroup($param)
    {
        if (intval($param['id'])>0){
            $productGroup = $this->find(intval($param['id']));
            if (empty($productGroup)){
                return ['status'=>400,'msg'=>lang('product_group_is_not_exist')];
            }
            if ($productGroup->parent_id > 0){
                return ['status'=>400,'msg'=>lang('please_enter_product_group_first')];
            }
        }
        if (intval($param['id'])<0){
            return ['status'=>400,'msg'=>lang('product_group_id_first_greater_than_0')];
        }

        $maxOrder = $this->max('order');

        $this->startTrans();
        try{
            $productGroup = $this->create([
                'name' => $param['name']??'',
                'hidden' => $param['hidden']??0,
                'order' => intval($maxOrder)+1,
                'create_time' => time(),
                'parent_id' => intval($param['id'])<=0?0:intval($param['id']),
                "type" => $param['type']??""
            ]);

            # 记录日志
            active_log(lang('log_admin_create_product_group',['{admin}'=>'admin#'.get_admin_id().'#'.request()->admin_name.'#','{product_group}'=>$param['name']]),'product_group',$productGroup->id);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>lang('create_fail')];
        }

        hook('after_product_group_create',['id'=>$productGroup->id,'customfield'=>$param['customfield']??[]]);

        return ['status'=>200,'msg'=>lang('create_success'),'data'=>['id'=>$productGroup->id]];
    }

    /**
     * 时间 2022-5-31
     * @title 编辑商品分组
     * @desc 编辑商品分组
     * @author wyh
     * @version v1
     * @param int id 1 分组ID required
     * @param string name 电脑 分组名称 required
     * @param int param.hidden 0 是否隐藏0放1是
     */
    public function updateProductGroup($param)
    {
        $productGroup = $this->find(intval($param['id']));
        if (empty($productGroup)){
            return ['status'=>400,'msg'=>lang('product_group_is_not_exist')];
        }

        $old = $productGroup['name'];

        $this->startTrans();

        try{
            $productGroup->save([
                'name' => $param['name']??'',
                'hidden' => $param['hidden']??$productGroup['hidden'],
                'update_time' => time()
            ]);

            # 记录日志
            active_log(lang('log_admin_update_product_group',['{admin}'=>'admin#'.get_admin_id().'#'.request()->admin_name.'#','{old}'=>$old,'{new}'=>$param['name']]),'product_group',$productGroup->id);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>lang('update_fail')];
        }

        hook('after_product_group_edit',['id'=>$productGroup->id,'customfield'=>$param['customfield']??[]]);

        return ['status'=>200,'msg'=>lang('update_success'),'data'=>['id'=>$productGroup->id]];
    }

    /**
     * 时间 2022-5-17
     * @title 删除商品分组
     * @desc 删除商品分组
     * @author wyh
     * @version v1
     * @param int id 1 分组ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function deleteProductGroup($id)
    {
        $productGroup = $this->find($id);
        if (empty($productGroup)){
            return ['status'=>400,'msg'=>lang('product_group_is_not_exist')];
        }
        # 存在二级分组,不可删除
        $existProductGroup = $this->where('parent_id',$id)->count();
        if ($existProductGroup>0){
            return ['status'=>400,'msg'=>lang('product_group_has_son_cannot_delete')];
        }
        # 存在商品,不可删除
        $ProductModel = new ProductModel();
        $existProduct = $ProductModel->where('product_group_id',$id)->count();
        if ($existProduct>0){
            return ['status'=>400,'msg'=>lang('product_group_has_product_cannot_delete')];
        }

        $this->startTrans();
        try{
            $productGroup->delete();

            # 记录日志
            active_log(lang('log_admin_delete_product_group',['{admin}'=>'admin#'.get_admin_id().'#'.request()->admin_name.'#','{product_group}'=>$productGroup['name']]),'product_group',$productGroup->id);

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>lang('delete_fail')];
        }

        hook('after_product_group_delete',['id'=>$id]);

        return ['status'=>200,'msg'=>lang('delete_success')];
    }

    /**
     * 时间 2022-5-17
     * @title 移动商品至其他商品组
     * @desc 移动商品至其他商品组
     * @author wyh
     * @version v1
     * @param int id 1 二级分组ID required
     * @param int target_product_group_id 1 移动后二级分组ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function moveProduct($param)
    {
        $id = intval($param['id']);

        $targetProductGroupId = intval($param['target_product_group_id']);

        $productGroup = $this->find($id);

        $productGroupTarget = $this->find($targetProductGroupId);

        if (empty($productGroup) || empty($productGroupTarget)){
            return ['status'=>400,'msg'=>lang('product_group_is_not_exist')];
        }
        if ($productGroup->parent_id == 0 || $productGroupTarget->parent_id == 0){
            return ['status'=>400,'msg'=>lang('please_select_product_group_second')];
        }

        $this->startTrans();

        try{
            $ProductModel = new ProductModel();
            $products = $ProductModel->where('product_group_id',$id)->select();

            foreach ($products as $product){
                $product->product_group_id = $targetProductGroupId;
                $product->save();
            }

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>lang('move_fail')];
        }

        return ['status'=>200,'msg'=>lang('move_success')];
    }

    /**
     * 时间 2022-07-11
     * @title 商品分组拖动排序
     * @desc 商品分组拖动排序
     * @author wyh
     * @version v1
     * @param int id 1 分组ID required
     * @param int pre_product_group_id 1 移动后前一个分组ID(没有则传0) required
     * @param int pre_first_product_group_id 1 移动后的一级分组ID required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function orderProductGroup($param)
    {
        $this->startTrans();

        try{
            $productGroup = $this->where('id',$param['id'])
                ->where('parent_id','>',0)
                ->find();
            if (empty($productGroup)){
                throw new \Exception(lang('product_group_is_not_exist'));
            }

            $firstProductGroup = $this->where('id',$param['pre_first_product_group_id'])
                ->where('parent_id',0)
                ->find();
            if (empty($firstProductGroup)){
                throw new \Exception(lang('first_product_group_is_not_exist'));
            }

            $time = time();

            if ($param['pre_product_group_id']){
                $preProductGroup = $this->where('id',$param['pre_product_group_id'])
                    ->where('parent_id',$param['pre_first_product_group_id'])
                    ->find();
                if (empty($preProductGroup)){
                    throw new \Exception(lang('pre_product_group_is_not_exist'));
                }

                if (isset($param['backward']) && $param['backward']){
                    $tmps = $this->where('parent_id',$param['pre_first_product_group_id'])
                        ->where('order','>=',$preProductGroup['order'])
                        ->where('id','<>',$param['id'])
                        ->select()->toArray();
                    foreach ($tmps as $tmp){
                        $this->update([
                            'order' => $tmp['order']+1,
                            'update_time' => $time
                        ],['id'=>$tmp['id']]);
                    }
                    $productGroup->save([
                        'order' => $preProductGroup['order'],
                        'parent_id' => $param['pre_first_product_group_id'],
                        'update_time' => $time
                    ]);
                }else{
                    $tmps = $this->where('parent_id',$param['pre_first_product_group_id'])
                        ->where('order','<=',$preProductGroup['order'])
                        ->select()->toArray();
                    foreach ($tmps as $tmp){
                        $this->update([
                            'order' => $tmp['order']-1,
                            'update_time' => $time
                        ],['id'=>$tmp['id']]);
                    }
                    $productGroup->save([
                        'order' => $preProductGroup['order']+1,
                        'parent_id' => $param['pre_first_product_group_id'],
                        'update_time' => $time
                    ]);
                }


            }else{
                $minOrder = $this->where('parent_id',$param['pre_first_product_group_id'])->min('order');
                $productGroup->save([
                    'order' => $minOrder-1,
                    'parent_id' => $param['pre_first_product_group_id'],
                    'update_time' => $time
                ]);

            }

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();

            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang('move_success')];
    }

    /**
     * 时间 2022-07-11
     * @title 一级商品分组拖动排序
     * @desc 一级商品分组拖动排序
     * @author wyh
     * @version v1
     * @param int id 1 一级分组ID required
     * @param int pre_first_product_group_id 1 移动后前一个一级分组ID(没有则传0) required
     * @param int backward 1 是否向后移动:1是,0否 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function orderFristProductGroup($param)
    {
        $this->startTrans();

        try{
            $firstProductGroup = $this->where('id',$param['id'])
                ->where('parent_id',0)
                ->find();
            if (empty($firstProductGroup)){
                throw new \Exception(lang('first_product_group_id_is_not_exist'));
            }

            if ($param['pre_first_product_group_id']){
                $preFirstProductGroup = $this->where('id',$param['pre_first_product_group_id'])
                    ->where('parent_id',0)
                    ->find();

                if (empty($preFirstProductGroup)){
                    throw new \Exception(lang('first_product_group_is_not_exist'));
                }
                $order = $preFirstProductGroup['order'];

                if (isset($param['backward']) && $param['backward']){ # 向后移动
                    $firstProductGroup->save([
                        'order' => $order
                    ]);

                    $tmps = $this->where('parent_id',0)
                        ->where('order','>=',$order)
                        ->where('id','<>',$param['id'])
                        ->select()
                        ->toArray();
                    foreach ($tmps as $tmp){
                        $this->update([
                            'order' => $tmp['order']+1,
                            'update_time' => time()
                        ],['id'=>$tmp['id']]);
                    }

                }else{
                    $tmps = $this->where('parent_id',0)
                        ->where('order','<=',$order)
                        ->select()
                        ->toArray();
                    foreach ($tmps as $tmp){
                        $this->update([
                            'order' => $tmp['order']-1,
                            'update_time' => time()
                        ],['id'=>$tmp['id']]);
                    }
                    $firstProductGroup->save([
                        'order' => $order+1
                    ]);
                }

            }else{
                $minOrder = $this->where('parent_id',0)->min('order');
                $firstProductGroup->save([
                    'order' => $minOrder-1,
                    'update_time' => time()
                ]);
            }

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>$e->getMessage()];
        }

        return ['status'=>200,'msg'=>lang('move_success')];
    }

    /**
     * 时间 2023-01-31
     * @title 隐藏/显示商品分组
     * @desc 隐藏/显示商品分组
     * @author theworld
     * @version v1
     * @param int param.id 1 商品分组ID required
     * @param int param.hidden 0 是否隐藏0否1是 required
     * @return int status - 状态码,200成功,400失败
     * @return string msg - 提示信息
     */
    public function hiddenProductGroup($param)
    {
        $productGroup = $this->find(intval($param['id']));
        if (empty($productGroup)){
            return ['status'=>400,'msg'=>lang('product_group_is_not_exist')];
        }

        $hidden = intval($param['hidden']);

        if ($productGroup['hidden'] == $hidden){
            return ['status'=>400,'msg'=>lang('cannot_repeat_opreate')];
        }

        $this->startTrans();
        try{
            $this->update([
                'hidden' => $hidden,
                'update_time' => time(),
            ],['id'=>intval($param['id'])]);

            # 记录日志
            if ($hidden == 1){
                active_log(lang('log_admin_hidden_product_group',['{admin}'=>'admin#'.get_admin_id().'#'.request()->admin_name.'#','{product_group}'=>$productGroup['name']]),'product_group',$productGroup->id);
            }else{
                active_log(lang('log_admin_show_product_group',['{admin}'=>'admin#'.get_admin_id().'#'.request()->admin_name.'#','{product_group}'=>$productGroup['name']]),'product_group',$productGroup->id);
            }

            $this->commit();
        }catch (\Exception $e){
            $this->rollback();
            return ['status'=>400,'msg'=>lang('fail_message')];
        }

        return ['status'=>200,'msg'=>lang('success_message')];
    }

}
