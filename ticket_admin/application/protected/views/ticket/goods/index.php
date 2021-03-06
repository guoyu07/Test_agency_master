<?php
$this->breadcrumbs = array('产品', '基础票管理');
?>



<style>
    .rules {
        position: relative;
        display: inline-block;
    }
    .rules+.rules {
        margin-left: 20px;
    }
    .rules > span {
        color: #999;
        font-size: 12px;
        cursor: pointer
    }
    .rules > div >span {
        margin: 0 10px
    }
    .rules > div {
        display: none;
        position: absolute;
        top: 15px;
        left: 50px;
        z-index: 999;
        width: 500px;
        padding: 10px;
        background-color: #fbf8e9;
        border: 1px solid #fed202;
        border-radius: 2px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        word-wrap: break-word;
    }
    .rules > div .table {
        background: none;
    }
    .rules > div .table tr > * {
        border: 1px solid #e0d9b6
    }
    .rules:hover > div {
        display: block;
    }
</style>
<div class="contentpanel">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title"> 
                门票管理
            </h4>
        </div>

        <div class="panel-body">
            <form class="form-inline" method="get" action="/ticket/goods/">
                <div class="form-group">
                    <select data-placeholder="Choose One" style="width:220px;padding:0 10px;" id="distributor-select-search" name="scenic_id">
                        <option value=""  >请输入景区名称</option>
                        <?php
                        $supplylanIds = PublicFunHelper::arrayKey($supplyLans, 'landscape_id');
                        $param['ids'] = join(',', $supplylanIds);
                        $param['items'] = 100000;
                        $data = Landscape::api()->lists($param);

                        $lanLists = PublicFunHelper::ArrayByUniqueKey(ApiModel::getLists($data), 'id');
                        ?>
                        <?php foreach ($supplyLans as $item): ?>
                            <option value="<?php echo $item['landscape_id'] ?>" <?php if (!empty($_GET['scenic_id']) && $_GET['scenic_id'] == $item['landscape_id']): ?>selected="selected"<?php endif; ?>><?php
                                //todo optimize
                                if (isset($lanLists[$item['landscape_id']])) {
                                    echo $lanLists[$item['landscape_id']]['name'];
                                }
                                ?></option>  
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="form-group">
                    <button  type="submit" class="btn btn-primary btn-sm pull-left">查询</button>
                </div>
            </form>
        </div>
    </div>

    <ul class="nav nav-tabs">
        <li class="active"><a href="/ticket/goods/index" class="now1"><strong>基础门票</strong></a></li>
        <li class=""><a href="/ticket/goods/index2" class="now2"><strong>我的门票</strong></a></li>
    </ul>

    <div class="tab-content mb30">
        <div id="t1" class="tab-pane active">


            <div class="table-responsive">
                <table class="table table-bordered mb30">
                    <thead>
                        <tr>
                            <th>景区</th>
                            <th>门票名称</th>
                            <th>包含景点</th>
                            <th>类型</th>
                            <th>价格</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($lists as $item) {
                            $rs = Landscape::api()->detail(array('id' => $item['scenic_id']), true);
                            $data = ApiModel::getData($rs);
                            $spans = array();
                            if (strlen($item['view_point']) > 0) {
                                $rs = Poi::api()->lists(array('ids' => $item['view_point'], 'items' => 100000), true);
                                $datas = ApiModel::getLists($rs);
	                             foreach ($datas as $v) {
                                    $spans[] = $v['name'];
                                }
                            }
                            ?>                        
                            <tr>
                                <td><?= isset($data['name']) ? $data['name'] : '' ?></td>
                                <td><?= $item['name'] ?></td>
                                <td><div class="rules">
                                        <span style="word-wrap:break-word;word-break:break-all;"><?= count($spans) > 4 ? implode(' ', array_slice($spans, 0, 4)) . ' ...' : implode(' ', $spans) ?></span>
                                        <div class="table-responsive"><?= implode(' ', $spans); ?> </div> 
                                    </div></td>
                                <td><?php
                        foreach ($item['type_prices'] as $rs):
                            $_model = TicketType::model()->findByPk($rs['type']);
                            echo $_model['name'] . '<br>';
                        endforeach;
                            ?></td>
                                <td><?php
                                    foreach ($item['type_prices'] as $rs):
                                        echo $rs['sale_price'] . '<br>';
                                    endforeach;
                                    ?></td>
                                <td> 
                                    <a title="查看" href="/ticket/goods/view/?gid=<?php echo $item['gid'] ?>" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">查看</a>
                                    <?php
                                    if (in_array($item['scenic_id'], $lanIds)):
                                        ?>
                                        <a title="修改" style="margin-left: 10px;" href="/ticket/goods/edit/?gid=<?php echo $item['gid'] ?>" onclick="modal_jump(this);"  data-target=".modal-bank" data-toggle="modal">
                                            <span class="glyphicon glyphicon-edit"></span>
                                        </a>
                                        <a title="删除" style="margin-left: 10px;" href="/ticket/goods/del/" onclick="del(this,<?php echo $item['gid'] ?>);
                                                                return false;" class="del">
                                            <span class="glyphicon glyphicon-trash"></span>
                                        </a>
                                        <?php
                                    endif;
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div style="text-align:center" class="panel-footer">
                <div id="basicTable_paginate" class="pagenumQu">
                    <?php
                    if (!empty($lists)) {
                        $this->widget('common.widgets.pagers.ULinkPager', array(
                                'cssFile' => '',
                                'header' => '',
                                'prevPageLabel' => '上一页',
                                'nextPageLabel' => '下一页',
                                'firstPageLabel' => '',
                                'lastPageLabel' => '',
                                'pages' => $pages,
                                'maxButtonCount' => 5, //分页数量
                            )
                        );
                    }
                    ?>
                </div>
            </div>
        </div>                                  
        <div id="t2" class="tab-pane "></div>                   

    </div>           

    <script type="text/javascript">


        $(function() {
            $("#distributor-select-search").select2(); //景区查询下拉框

            $('.allcheck').click(function() {
                if ($(this).text() == '全选') {
                    $('#staff-body').find('input').prop('checked', true)
                    $(this).text('反选')
                } else {
                    $('#staff-body').find('input').prop('checked', false)
                    $(this).text('全选')
                }
                ;

            });
        });


    </script>
</div>

<div id='verify-modal' class="modal fade modal-bank" tabindex="-1" role="dialog"></div>
<script type="text/javascript">
    function modal_jump_add() {
        $('#verify-modal').html('');
        $.get('/ticket/goods/add/', function(data) {
            $('#verify-modal').html(data);

        });
    }

    function modal_jump(obj) {
        $('#verify-modal').html('');
        $.get($(obj).attr('href'), function(data) {
            $('#verify-modal').html(data);
        });
    }

    function del(obj, id) {
		 PWConfirm('确定要删除?',function(){
			      $.post($(obj).attr('href'), {gid: id, is_del: 1}, function(data) {
            if (data.error) {
                alert(data.msg);
                $('#form-button').attr('disabled', false);
            } else {
                alert('删除门票成功',function(){window.location.reload();});
            }
        }, 'json');
            });
       
    }
    $(function() {
        $("#distributor-select-search").select2(); //景区查询下拉框             
    });
</script> 