<?php
$this->breadcrumbs = array('景区管理', '图文全景');
?>
<div class="contentpanel">
    <a href="/ticket/goods/?scenic_id=<?php echo $_GET['id'] ?>"  class="btn btn-primary btn-sm pull-right" style="position: relative; right: 15px;top: 10px;z-index:111">
        门票管理
    </a>
    <ul style="margin-bottom:-1px;position:relative;z-index:1;height:50px;" class="nav nav-tabs">
        <li class="active"><a href="/scenic/scenic/view/?id=<?php echo $_GET['id'] ?>"><strong>图文全景</strong></a></li>
        <!--li><a href="/scenic/Spot/?id=28"><strong>景点管理</strong></a></li-->
    </ul>

    <div class="tab-content">
        <div id="t1" class="row tab-pane active">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <img style="max-width:100%; border: 0px; display: block; margin: 0px auto; height: 208px;" src="<?php echo!empty($data['images']) ? $data['images'][0]['url'] : '/img/default.jpg'; ?>">
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-default change1">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo isset($data['name']) ? $data['name'] : ''; ?></h4>
                        <p></p>
                    </div>
                    <div class="table-responsive th-width">
                        <table class="table table-bordered mb30">
                            <tbody>
                                <tr>
                                    <th>景区级别</th>
                                    <td><?php echo $data['landscape_level_name'] ? $data['landscape_level_name'] : '非A'; ?></td>
                                </tr>
                                <tr>
                                    <th>所在地</th>
                                    <td><?php
                                        $cityname = isset($data['province_name']) ? $data['province_name'] : '';
                                        $cityname .= isset($data['city_name']) ? $data['city_name'] : '';
                                        $cityname .= isset($data['district_name']) ? $data['district_name'] : '';
                                        echo $cityname;
                                        ?></td>
                                </tr>
                                <tr>
                                    <th>详细地址</th>
                                    <td><?php echo $data['address']; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:left; padding:15px 0 15px 10px;">
                                        <div>景区说明</div>
                                        <div style="text-indent:2em; margin-top:10px;" id="td_biography"><?php echo nl2br($data['biography']); ?></div>
<?php if (isset($data['yes']) && $data['yes'] == 1): ?>
                                            <div style="float:right;padding-right:20px;">
                                                <a href="javascript:void(0)" style="color:#06C; margin-left:10px;" class="wirte clearPart">编辑</a>
                                            </div>
<?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel panel-default change2" style="display:none">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?php echo isset($data['name']) ? $data['name'] : ''; ?></h4>
                        <p></p>
                    </div>

                    <div class="table-responsive th-width">
                        <table class="table table-bordered mb30">
                            <tbody>
                                <tr>
                                    <th>景区级别</th>
                                    <td><?php echo $data['landscape_level_name'] ? $data['landscape_level_name'] : '非A'; ?></td>
                                </tr>
                                <tr>
                                    <th>所在地</th>
                                    <td><?php
                                        $cityname = isset($data['province_name']) ? $data['province_name'] : '';
                                        $cityname .= isset($data['city_name']) ? $data['city_name'] : '';
                                        $cityname .= isset($data['district_name']) ? $data['district_name'] : '';
                                        echo $cityname;
                                        ?></td>
                                </tr>
                                <tr>
                                    <th>详细地址</th>
                                    <td><?php echo $data['address']; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="text-align:left; padding:15px 15px 15px 10px;">
                                        <div>景区说明</div>
                                        <div style="text-indent:2em; margin-top:10px;">
                                            <textarea  rows="5" id="biography"  class="form-control" style="text-indent: 2em;" data-prompt-position="topLeft"><?php echo nl2br($data['biography']); ?></textarea>
                                            <input  id="landid" type="hidden" value="<?php echo $_GET['id'] ?>"  name="landscape_id">
                                        </div>
                                        <div style="float:right;padding-right:20px;"><a href="javascript:void(0)" style="color:#06C; margin-left:10px;" class="save clearPart">保存</a></div>
                                    </td>

                                </tr>
                            </tbody></table>
                    </div>

                </div>
            </div><!-- col-md-6 -->
        </div>
    </div>



    <br>
    <div id="verify_return"></div>
    <input type="hidden" name="landscape_id" value="<?php echo $_GET['id']; ?>" id="landscape_id">
    <div class="panel panel-default">


        <div class="panel-heading"><h4 class="panel-title">
<?php if (isset($data['yes']) && $data['yes'] == 1): ?>

                    <button data-toggle="modal" data-target=".bs-example-modal-static" onclick="modal_jump_add();" class="btn btn-primary btn-xs pull-right">新增景点</button>

<?php endif; ?>景点列表</h4>
        </div>





        <div class="table-responsive">
            <table class="table table-bordered mb30" style="word-break:break-all;">
                <thead>
                    <tr>
                        <th style="width:30%">景点名称</th>
                        <th style="width:60%">景点介绍</th>
                        <?php if (isset($data['yes']) && $data['yes'] == 1): ?>
                            <th style="width:10%">操作</th>
<?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?PHP
                    foreach ($lists as $item):
                        ?>
                        <tr>
                            <td><?php
                                echo $item['name'];
                                //if ($item['status'])
                                //    echo "<span class='btn btn-success btn-bordered btn-xs'>已上架</span>";
                                //else
                                //    echo "<span class='btn btn-danger btn-bordered btn-xs'>已下架</span>";
                                ?></td>
                            <td  style="word-break: break-all; word-wrap:break-word;"><?php echo $item['description']; ?></td>
                                <?php if (isset($data['yes']) && $data['yes'] == 1): ?>
                                <td style="word-break: break-all; word-wrap:break-word;">
                                    <?php
                                    if ($item['status'] == 1) {
                                        echo " <a class='clearPart' onclick='downUp(" . $item['id'] . ",1);' title='下架'>下架</a>";
                                    } else {
                                        echo "<a class='clearPart' onclick='downUp(" . $item['id'] . ",0)' title='上架'>上架</a>";
                                    }
                                    ?>

                                    <a class="clearPart" data-toggle="modal" data-target=".bs-example-modal-static" title="编辑" onclick="modal_jump_edit(<?php echo $item['id']; ?>);" >编辑</a>
                                </td>
                        <?php endif; ?>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        </div>
        <div style="text-align:center" class="panel-footer">
            <div id="basicTable_paginate" class="pagenumQu">
                <?php
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
                ?>
            </div>
        </div>
    </div>

    <div class="panel-footer" style="padding-left:5%">
        <button class="btn btn-default" type="button" onclick="javascript:history.go(-1);" id="export">返回</button>
    </div>
    <div id='verify-modal' data-backdrop="static" role="dialog" tabindex="-1" class="modal fade bs-example-modal-static">

    </div>
</div>

<style>
    .th-width tr{height:40px;}
    .th-width th{width:200px;}
</style>



<script type="text/javascript">
    $('.wirte').click(function() {
        $('.change1').hide();
        $('.change2').show();
        return false;
    })
    $('.save').click(function() {
        saveData();
        return false;
    })

    function saveData()
    {
        var biography = $('#biography').val();
        $.post('/scenic/scenic/update', {biography: biography, landscape_id: $('#landid').val()}, function(result) {
            if (result.code == 'fail')
            {
                alert(result.message);
                return false;
            }
            $('#td_biography').html($('#biography').val().replace(/\n/g, '<br>'));
            $('.change1').show();
            $('.change2').hide();

            location.partReload();
        }, 'json');

    }

    function modal_jump_add() {
        $('#verify-modal').html();
        $.get('/scenic/scenic/add/', function(data) {
            $('#verify-modal').html(data);

        });
    }



    function modal_jump_edit(id) {
        $('#verify-modal').html();
        $.get('/scenic/scenic/edit/?id=' + id + '&landscape_id=' + $('#landscape_id').val() + '', function(data) {
            $('#verify-modal').html(data);

        });
    }

    function downUp(id, status) {
        var id = id;
        var status = status;
        $.post('/scenic/scenic/DownUP/', {id: id, status: status, 'landscape_id': $('#landscape_id').val()}, function(data) {
            if (data.error) {
                var warn_msg = '<div class="alert alert-error" style="background:#FF8888;"><button data-dismiss="alert" class="close" type="button">×</button><i class="icon-warning-sign"></i>' + data.msg + '</div>';
                $('#verify_return').html(warn_msg);
            } else {
                // alert(data.msg);
                var succss_msg = '<div class="alert alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>操作成功</strong></div>';
                $('#verify_return').html(succss_msg);
                setTimeout("location.href= '/#'+'/scenic/scenic/view?id=" + $('#landscape_id').val() + "'", '2000');
            }
        }, "json");
        return false;
    }
</script>    
