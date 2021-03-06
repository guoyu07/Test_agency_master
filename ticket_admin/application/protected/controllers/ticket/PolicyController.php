<?php

/* 
 * 分销商策略控制器
 * Date 2015-01-19
 * 
 */

class PolicyController extends Controller
{
    /**
     * 默认方法展示策略列表
     */
    public function actionIndex() {
        $data = array();
		$org_id = Yii::app()->user->org_id;
		if (intval($org_id) > 0) {
			$params['supplier_id'] = $org_id;
			$params['current'] = isset($_GET['page']) ? $_GET['page'] : 1;
			$params['items'] = 20;
            //获取策略列表
			$result = Ticketpolicy::api()->lists($params);
            
			if (isset($result['code']) && $result['code'] == 'succ') {
				$data['supplier_id'] = $org_id;
				$data['lists'] = $result['body'];
				$data['pages'] = new CPagination($data['lists']['pagination']['count']);
				$data['pages']->pageSize = $params['items'];
			}
		}
		$this->render('index', $data);
    }
    
    /**
     * 删除分销策略
     * @throws CHttpException 404
     */
    public function actionDel() {
        if (Yii::app()->request->isPOSTRequest) {
            //根据策略id删除
            $id = Yii::app()->request->getParam('id');
            $result = Ticketpolicy::api()->del(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'user_id' => Yii::app()->user->uid,
			    'user_name' => Yii::app()->user->name,
			    'user_account' => Yii::app()->user->account
			));
			if(isset($result['code']) && $result['code'] == "succ"){
                echo json_encode(array('error'=>0,'message'=>""));
            }else{
            	echo json_encode(array('error'=>1,
                    'message'=>isset($result['message'])?$result['message']:'数据未返回'));
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }
    
    /**
     * 获取当前用户的分销商
     * @return string 分销商列表的html片段
     */
    public function actionGetDistributor() {
        //从组织机构api获取分销商列表
        $result = Organizations::api()->getlist(array('supply_id'=>Yii::app()->user->org_id,'show_all'=> 1));
        if(isset($result['code']) && $result['code'] == "succ"){
            $html = '';      //合作的分销商
            $otherhtml = ''; //未合作分销商
            if(count($result['body']['data'])>0){
                //合作的分销商
                foreach($result['body']['data'] as $distr){
                    $html .='<tr>';
                    $html .='<td style="width:200px;">'.$distr['distributor_name'].'</td>';
                    $html .='<td style="width:116px;"><input id="p_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="blackname_arr['.$distr["distributor_id"].']" class="blackgroup"></td>';
                    $html .='<td style="width:200px;"><input type="text" id="s_price_'.$distr["distributor_id"].'" name="s_price['.$distr["distributor_id"].']" class="spinner"></td>';
                    $html .='<td style="width:200px;"><input type="text" id="g_price_'.$distr["distributor_id"].'" name="g_price['.$distr["distributor_id"].']" class="spinner"></td>';
                    $html .='<td style="width:149px;"><input id="credit_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="credit_arr['.$distr["distributor_id"].']" class="creditgroup"></td>';
                    $html .='<td><input id="advance_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="advance_arr['.$distr["distributor_id"].']" class="advancegroup" style="margin-left: 17px;"></td>';
                    $html .='</tr>';
                }
                $otherhtml  ='<tr style="background-color:#f7f7f7;">';
                $otherhtml .='<td style="width:200px;">未合作分销商</td>';
                $otherhtml .='<td style="width:116px;"><input id="p_0" type="checkbox" value="0" name="blackname_arr[0]"></td>';
                $otherhtml .='<td style="width:200px;"><input type="text" id="s_price_0" name="s_price[0]" class="spinner"></td>';
                $otherhtml .='<td style="width:200px;"><input type="text" id="g_price_0" name="g_price[0]" class="spinner"></td>';
//                $otherhtml .='<td style="width:149px;"><input id="credit_0" type="checkbox" value="0" name="credit_arr[0]"></td>';
//                $otherhtml .='<td><input id="advance_0" type="checkbox" value="0" name="advance_arr[0]"></td>';
                $otherhtml .='<td style="width:149px;"></td>';
                $otherhtml .='<td></td>';
                $otherhtml .='</tr>';
            }
            echo json_encode(array('error'=>0,'message'=>"",'data'=>$html,'otherdata'=>$otherhtml));
        }else{
            echo json_encode(array('error'=>1,'message'=>$result['message']));
        }
    }
    
    /**
     * 获取分销规则的详情
     * @throws CHttpException 404
     */
    public function actionDetail() {
        if (Yii::app()->request->isPOSTRequest) {
            $id = Yii::app()->request->getParam('id');
            $result = Ticketpolicy::api()->detail(array(
				'id' => $id,
			    'supplier_id' => Yii::app()->user->org_id,
			    'show_items' => 1
			));
			if(isset($result['code']) && $result['code'] == "succ"){
                $dist_arr = array();//保存分销商名称
                $html = '';      //合作的分销商
                $otherhtml = ''; //未合作分销商
                $name = isset($result['body']['name'])?$result['body']['name']:'';
                $note = isset($result['body']['note'])?$result['body']['note']:'';
                //获取分销商名称
                $dist_result = Organizations::api()->getlist(array('supply_id'=>Yii::app()->user->org_id,'show_all' => 1));
                if(isset($dist_result['code']) && $dist_result['code'] == "succ"){
                    foreach($dist_result['body']['data'] as $one_distr){
                        $dist_arr[$one_distr['distributor_id']] = $one_distr['distributor_name'];
                    }
                }
                if(count($result['body']['items'])>0){
                    //分销商列表数据
                    foreach($result['body']['items'] as $distr){
                        $tmp_blackname = $distr['blackname_flag']==1?'checked="checked"':'';
                        $tmp_credit = $distr['credit_flag']==1?'checked="checked"':'';
                        $tmp_advance = $distr['advance_flag']==1?'checked="checked"':''; 
                        $distributor_name = '';
                        //循环分销商数组，获得未被设置规则的分销商（可能是新添加的分销商）
                        if(isset($dist_arr[$distr["distributor_id"]])){
                            $distributor_name = $dist_arr[$distr["distributor_id"]];
                            unset($dist_arr[$distr["distributor_id"]]);
                        }
                        $html .='<tr>';
                        $html .='<td style="width:200px;">'.$distributor_name.'</td>';
                        $html .='<td style="width:116px;"><input id="p_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="blackname_arr['.$distr["distributor_id"].']" '.$tmp_blackname.' class="blackgroup"></td>';
                        $html .='<td style="width:200px;"><input type="text" id="s_price_'.$distr["distributor_id"].'" name="s_price['.$distr["distributor_id"].']" class="spinner" value="'.$distr['fat_price'].'"></td>';
                        $html .='<td style="width:200px;"><input type="text" id="g_price_'.$distr["distributor_id"].'" name="g_price['.$distr["distributor_id"].']" class="spinner" value="'.$distr['group_price'].'"></td>';
                        $html .='<td style="width:149px;"><input id="credit_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="credit_arr['.$distr["distributor_id"].']" '.$tmp_credit.' class="creditgroup"></td>';
                        $html .='<td><input id="advance_'.$distr["distributor_id"].'" type="checkbox" value="'.$distr["distributor_id"].'" name="advance_arr['.$distr["distributor_id"].']" '.$tmp_advance.' class="advancegroup" style="margin-left: 17px;"></td>';
                        $html .='</tr>';
                    }
                    //列出未被设置规则的分销商
                    foreach($dist_arr as $distr_id => $distr_name){
                        $html .='<tr>';
                        $html .='<td style="width:200px;">'.$distr_name.'</td>';
                        $html .='<td style="width:116px;"><input id="p_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="blackname_arr['.$distr_id.']" class="blackgroup"></td>';
                        $html .='<td style="width:200px;"><input type="text" id="s_price_'.$distr_id.'" name="s_price['.$distr_id.']" class="spinner"></td>';
                        $html .='<td style="width:200px;"><input type="text" id="g_price_'.$distr_id.'" name="g_price['.$distr_id.']" class="spinner"></td>';
                        $html .='<td style="width:149px;"><input id="credit_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="credit_arr['.$distr_id.']" class="creditgroup"></td>';
                        $html .='<td><input id="advance_'.$distr_id.'" type="checkbox" value="'.$distr_id.'" name="advance_arr['.$distr_id.']" class="advancegroup" style="margin-left: 17px;"></td>';
                        $html .='</tr>';
                    }
                    $tmp_blackname = $result['body']['other_blackname_flag']==1?'checked="checked"':'';
                    $tmp_credit = $result['body']['other_credit_flag']==1?'checked="checked"':'';
                    $tmp_advance = $result['body']['other_advance_flag']==1?'checked="checked"':''; 
                    $otherhtml  ='<tr style="background-color:#f7f7f7;">';
                    $otherhtml .='<td style="width:200px;">未合作分销商</td>';                    
                    $otherhtml .='<td style="width:116px;"><input id="p_0" type="checkbox" value="0" name="blackname_arr[0]" '.$tmp_blackname.'></td>';
                    $otherhtml .='<td style="width:200px;"><input type="text" id="s_price_0" name="s_price[0]" class="spinner" value="'.$result['body']['other_fat_price'].'" ></td>';
                    $otherhtml .='<td style="width:200px;"><input type="text" id="g_price_0" name="g_price[0]" class="spinner" value="'.$result['body']['other_group_price'].'" ></td>';
//                    $otherhtml .='<td style="width:149px;"><input id="credit_0" type="checkbox" value="0" name="credit_arr[0]" '.$tmp_credit.'></td>';
//                    $otherhtml .='<td><input id="advance_0" type="checkbox" value="0" name="advance_arr[0]" '.$tmp_advance.'></td>';
                    $otherhtml .='<td style="width:149px;"></td>';
                    $otherhtml .='<td></td>';
                    $otherhtml .='</tr>';
                }
                echo json_encode(array('error'=>0,'message'=>"",'data'=>$html,'otherdata'=>$otherhtml,'dist_id'=>$id,'name'=>$name,'note'=>$note));
            }else{
            	echo json_encode(array('error'=>1,
                    'message'=>isset($result['message'])?$result['message']:'数据未返回'));
            }
        }else{
            Throw new  CHttpException('404',"找不到请求的页面!");
        }
    }
    /**
     * 保存分销策略
     */
    public function actionSave() {
        if (Yii::app()->request->isPostRequest) {
            $data = $_REQUEST;
            $policy_items = array();

            if (!isset($data['pname']) || empty($data['pname'])) {                
                $this->_end(1, '策略名不可以为空！');
            }
            
            if(!empty($data['distid'])){
                $field['id'] = $data['distid'];
            }
            $field['supplier_id'] = Yii::app()->user->org_id;	              //供应商ID
            $field['user_id'] = Yii::app()->user->uid;                        //操作者UID
            $field['user_name'] = Yii::app()->user->name;                     //操作者用户名
            $field['user_account'] = Yii::app()->user->account;               //操作者账号
            $field['name'] = $data['pname'];                                   //规则名称
            $field['note'] = $data['note'];                                   //说明
            $field['other_fat_price'] = isset($data['s_price'][0])?$data['s_price'][0]:0;	      //未合作分销商散客价
            $field['other_group_price'] = isset($data['g_price'][0])?$data['g_price'][0]:0;       //未合作分销商团客价
            $field['other_blackname_flag'] = isset($data['blackname_arr'][0])?1:0;	              //未合作分销商黑名单开关：0关闭 1开启
            $field['other_credit_flag'] = isset($data['credit_arr'][0])?1:0;	                  //未合作分销商信用支付开关：0关闭 1开启
            $field['other_advance_flag'] = isset($data['advance_arr'][0])?1:0;                    //未合作分销商储值支付开关：0关闭 1开启

            //各分销商的策略
            if(is_array($data['s_price'])){
                foreach ($data['s_price'] as $dist_id=>$s_price){
                    if($dist_id > 0){
                        $item['distributor_id'] = $dist_id;
                        $item['fat_price'] = empty($s_price)?0:$s_price;
                        $item['group_price'] = empty($data['g_price'][$dist_id])?0:$data['g_price'][$dist_id];
                        $item['blackname_flag'] = isset($data['blackname_arr'][$dist_id])?1:0;
                        $item['credit_flag'] = isset($data['credit_arr'][$dist_id])?1:0;
                        $item['advance_flag'] = isset($data['advance_arr'][$dist_id])?1:0;
                                                
                        $policy_items[] = $item;
                    }
                }
            }
            $field['policy_items'] = json_encode($policy_items);
            //存在分销商id则是更新
            if(isset($field['id'])){
                $rs = Ticketpolicy::api()->update($field);
            }else{
                $rs = Ticketpolicy::api()->add($field);
            }
            //Ticketpolicy::api()->debug= true;
            if ($rs['code'] == 'succ') {
                $this->_end(0, $rs['message']);
            }
            else {
                $this->_end(1, $rs['message']);
            }
        }
    }   
}