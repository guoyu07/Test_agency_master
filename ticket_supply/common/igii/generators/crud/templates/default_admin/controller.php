<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $code->getControllerClass(); ?> extends <?php echo $base_controller . "\n"; ?>
{
    /*
    *查询页
    */
    public function actionIndex(){
        $model = array(<?php
        $like = array(); //模糊查询的字段
        $time = array(); //时间范围查询的字段
        $compare = array(); //精确查询
        $pri = 'id';
foreach ($fields as $item) {
    if ($item['column_key'] == 'PRI')
        $pri = $item['column_name'];
    if (!in_array($item['column_name'], $search))
        continue;
    if ($this->searchTime($item['column_comment'])) {
        $time[] = $item['column_name'];
        continue;
    }

    if ($this->searchLike($item['column_comment'])) {
        $like[] = $item['column_name'];
        continue;
    }

    if ($this->searchSelect($item['column_comment'])) {
        echo "'{$item['column_name']}'" . "=>'',";
        $compare[] = $item['column_name'];
    }
}
if ($like) {
    echo "'_like'=>'',";
}

foreach ($time as $item) {
    echo "'begin_{$item}'=>'" . date('Y-m-d') . "','end_{$item}'=>date('Y-m-d'),";
}
?>);
        $criteria = new CDbCriteria();
        $criteria->order = '<?php echo $pri ?> DESC';
        
        #get请求赋值
        foreach ($model as $key => $val){
            if (isset($_GET[$key]) && $_GET[$key] !== ''&& $_GET[$key] !== 'null')$model[$key] = $_GET[$key]; //给model赋值	    
        }           
	
         #post请求赋值
         if (Yii::app()->request->isPostRequest){
            foreach ($model as $key => $val){
                    if (isset($_POST[$key]) && $_POST[$key] !== ''&& $_POST[$key] !== 'null')$model[$key] = $_POST[$key]; //给model赋值
            }
         }
         #一般查询
 <?php
 foreach ($compare as $item){
 ?>    
       if($model['<?php echo $item ?>'])$criteria->compare('<?php echo $item ?>',$model['<?php echo $item ?>']);
<?php }?>
         #like查询
 <?php
 $_arr = array();
 foreach ($like as $item){
     $_arr[] = $item." like :key" ;
 }
 if($_arr){
  ?>     
        if($model['_like']){
          $criteria->addCondition('<?php echo join(' OR  ',  $_arr) ?>');
          $criteria->params[':key']=$model['_like'].'%';
        }
 <?php
 }
 ?>         
         #时间查询
 <?php
 foreach ($time as $item){?>
        $criteria->addCondition("<?php echo $item ?>>=" . strtotime($model['begin_<?php echo $item ?>']));
        $criteria->addCondition("<?php echo $item ?><" . strtotime('+1 day', strtotime($model['end_<?php echo $item ?>'])));
 <?php
 }
 ?>
        
        $result = <?php echo $_POST['model'] ?>::model()->count($criteria);
        $pager = new CPagination($result);
        $pager->pageSize = 15;
        $pager->applyLimit($criteria);
        $list = <?php echo $_POST['model'] ?>::model()->findAll($criteria);
        $this->render('index', compact('model', 'list', 'pager'));
    }
    
    /*
    *创建
    */
    public function actionCreate(){
        if (Yii::app()->request->isPostRequest) {

            #时间转换
           <?php
            foreach ($time as $item) {
                 if (in_array($item, $create)){
                         echo "\$_POST['{$item}'] = strtotime(\$_POST['{$item}']);";
                 }
            }?>
            #创建<?php echo $tableName."\n" ?>
            $model = new <?php echo $_POST['model'] ?>();
            $model->attributes = $_POST;
            $model->isNewRecord = true;
            if ($model->save()) {
                $id = $model->id ;
                $ret = array("message" => '添加成功', 'title' => 'success', 'backUrl' => '/<?php echo $controller_id ?>/update/id/'.$id);
            } else {
                $msg = '';
                foreach ($model->getErrors() as $field => $aMsg) {
                    $msg .= $aMsg[0] . '<br/>';
                }
                $ret = array("message" => $msg, 'title' => 'error', 'backUrl' => '/<?php echo $controller_id ?>/create/');
            }
            $this->render("/public/message", $ret);
            Yii::app()->end();
        }
        $this->render('create');
    }
    
    /*
    *更新
    */
    public function actionUpdate($id){
        $id = intval($id);
        $model = <?php echo $_POST['model'] ?>::model()->findByPk($id);
        if (Yii::app()->request->isPostRequest) {

            #时间转换
           <?php
            foreach ($time as $item) {
                 if (in_array($item, $create)){
                         echo "\$_POST['{$item}'] = strtotime(\$_POST['{$item}']);";
                 }
            }?>
            #创建<?php echo $tableName."\n" ?>
            $model->attributes = $_POST;
            if ($model->save()) {
                $ret = array("message" => '修改成功', 'title' => 'success', 'backUrl' => '/<?php echo $controller_id ?>/update/id/'.$id);
            } else {
                $msg = '';
                foreach ($model->getErrors() as $field => $aMsg) {
                    $msg .= $aMsg[0] . '<br/>';
                }
                $ret = array("message" => $msg, 'title' => 'error', 'backUrl' => '/<?php echo $controller_id ?>/update/id/'.$id);
            }
            $this->render("/public/message", $ret);
            Yii::app()->end();
        }
        $this->render('update', compact('model'));
    }
    
    /*
    *删除
    */
     public function actionDel($id)
    {
        $id = intval($id);
    	if ($id) {
    		if (<?php
                    if($del_field){
                        echo $_POST['model'].'::model()->updateByPk($id,array(\''.$del_field.'\'=>0))';
                    }else{
                        echo $_POST['model'].'::model()->deleteByPk($id)';
                    }?>) 
    		{
    			$ret = array("message" => "操作成功", 'title' => 'success', 'backUrl' => '/<?php echo $controller_id ?>/');
    			$this->render("/public/message", $ret);
    		}
    		else
    		{
    			$ret = array("message" => "意外出错", 'title' => 'error', 'backUrl' => '/<?php echo $controller_id ?>/');
    			$this->render("/public/message", $ret);
    		}
    	}
    	else
    	{
    		$ret = array("message" => "非法ID", 'title' => 'error', 'backUrl' => '/<?php echo $controller_id ?>/');
    		$this->render("/public/message", $ret);
    	}
    }
}
