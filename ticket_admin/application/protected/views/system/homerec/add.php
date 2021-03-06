<style>
.ui-datepicker { z-index:9999!important }
</style>
<div class="contentpanel" id="maincontent">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-btns">
                        <a href="" class="panel-minimize tooltips" data-toggle="tooltip" title="折叠"><i class="fa fa-minus"></i></a>
                        <a href="" class="panel-close tooltips" data-toggle="tooltip" title="隐藏面板"><i class="fa fa-times"></i></a>
                    </div>
                    <!-- panel-btns -->
                    <h4 class="panel-title">新增首页推荐</h4>
                </div>
                <div id="show_msg"></div>
                <!-- panel-heading -->

                <div class="panel-body nopadding">
                    <form class="form-horizontal" id="homerec-form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span> 主题</label>
                                    <div class="col-sm-6">
                                        <input type="text" placeholder="" maxlength="20" tag="主题" class="validate[required] form-control" id="title" name="title" />
                                    </div>
                                </div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>位置</label>
									<div class="col-sm-6">
										<div class="checkbox-inline">
											<label>
												<input type="checkbox" name="pos_id[]" id="pos_id" class="validate[minCheckbox[1]]" checked value="1"/>登陆页
											</label>
										</div>
										<div class="checkbox-inline">
											<label>
												<input type="checkbox" name="pos_id[]" id="pos_id" class="validate[minCheckbox[1]]" value="2"/>工作台
											</label>
										</div>
									</div>
								</div>
                                <!-- form-group -->

                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>简介</label>
                                    <div class="col-sm-6">
										<textarea name="detail" id="introduce" tag="简介" class="validate[required] form-control" rows="4"></textarea>
                                    </div>
                                </div>
                                <!-- form-group -->

								<div class="form-group">
									<label class="col-sm-2 control-label"><span class="text-danger">*</span>活动时段</label>
									<div class="col-sm-6">
										<input type="text" style="cursor: pointer;cursor: hand;display:inline-block" name="start_time" id="start_time" tag="开始时间" class="validate[required] form-control datepicker" readonly="readonly" placeholder="开始时间">
										<input type="text" style="cursor: pointer;cursor: hand;display:inline-block" name="end_time" id="end_time" tag="结束时间" class="validate[required] form-control datepicker" readonly="readonly" placeholder="结束时间">
									</div>
								</div>
								<!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">链接地址</label>
                                    <div class="col-sm-6">
										<input type="text" class="validate[custom[url]] form-control" name="url" id="url"/>
                                    </div>
                                </div>
                                <!-- form-group -->
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>图片上传</label>
									<div class="col-sm-6">
										<div id="bimg" style="max-width:180px;height:150px;"> 
                                            <img id="bimg_img"  src="/img/uploadfile01.png" class="hover" style="max-width:180px;height:150px;">
                                            <input type="hidden" class="sp_sxming" name="bimg" id="bimg_hidden"/>
                                            <label class="text-danger">* 图片大小 1366px X 658px</label>
                                        </div>
									</div>

                                </div>
                            </div>
                        </div>

                        <div class="panel-footer" style="padding-left:8%">
                            <button class="btn btn-primary mr20" id="btn-pub" style="width:130px;">发布</button>
                            <button class="btn btn-primary mr20" id="btn-save" style="width:130px;">保存</button>
                            <a class="btn btn-default" href="/system/homerec">取消</a>
                        </div>

                    </form>
                </div>
                <!-- panel-body -->
            </div>
            <!-- panel -->

        </div>
        <!-- col-md-6 -->
    </div>
    <!-- row -->
</div>
<script type="text/javascript"  charset="utf8" src="/js/ajaxUpload.js"></script>
<script type="text/javascript">

    $(document).ready(function () {

        /**
         * 日期控制设置 
         * 开始时间必须大于等于当前时间
         */
        $('.datepicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
            monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
            yearRange: "1995:2065",
            minDate: new Date(),
            beforeShow: function(d){
                setTimeout(function(){
                    $('.ui-datepicker-title select').select2({
                        minimumResultsForSearch: -1
                    });
                },0)
            },
            onChangeMonthYear: function(){
                setTimeout(function(){
                    $('.ui-datepicker-title select').select2({
                        minimumResultsForSearch: -1
                    });
                },0)
            },
            onClose: function(dateText, inst) { 
                $('.select2-drop').hide(); 
            }
        });

        //文件上传
        window.imgField = '';
<?php Yii::app()->upyun->x_gmkerl_value = '1366'; ?>
        new AjaxUpload('#bimg', {
            action: 'http://v0.api.upyun.com/<?php echo Yii::app()->upyun->bucket ?>/',
            name: 'file',
            onSubmit: function (file, ext) {
                //上传文件格式限制
                if (!ext || !/^(jpg|png|jpeg|gif)$/i.test(ext)) {
                    alert('上传格式不正确');
                    return false;
                }
                this.setData(<?php echo Yii::app()->upyun->getCode() ?>);
                window.imgField = 'bimg';
            },
            onComplete: function (file, data) {
            }
        });

        window.upload_callback = function (data) {
            if (data.status != 200) {
                alert('上传失败！');
                return false;
            }
            $('input[name=' + window.imgField + ']').val(data.msg);
            $('#' + window.imgField + '_img').attr('src', data.msg);
        }

        /**
         * 表单验证 
         */
        $('#btn-save').validationEngine({
            promptPosition: 'topLeft',
            addFailureCssClassToField: 'error',
            autoHidePrompt: true,
            autoHideDelay: 3000
        });
        $('#btn-save, #btn-pub').click(function () {
            var obj = $('#homerec-form');
            var status = 0;
            if (this.id == 'btn-pub') {		// 发布
                status = 1;
            }
            //alert(obj.serialize());
            if (obj.validationEngine('validate') == true) {
                if ($("#start_time").val() > $("#end_time").val()) {	// 时间验证
                    $('#end_time').PWShowPrompt('结束时间不能小于开始时间');
                } else if ($('#bimg_hidden').val() == "") {			// 图片必选
                    $('#bimg_hidden').parent().PWShowPrompt('请选择图片');
                } else {
                    $postdata = obj.serialize() + "&status=" + status;
                    ;
                    $.post('/system/homerec/saveRec', $postdata, function (data) {
                        if (data.code != "succ")
                        {
                            var tmp_errors = '';
                            $.each(data.message, function (i, n)
                            {
                                tmp_errors += n;
                            });
                            alert(tmp_errors);
                        }
                        else if (data.code == "succ")
                        {
                            var type_msg;
                            type_msg = '操作成功' + data.message;
                            alert(type_msg, function() {
                                location.href = '/site/switch/#/system/homerec';
                            });
                        }
                    }, "json");
                }
            }
            return false;
        });

    });

</script>