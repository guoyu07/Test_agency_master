<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
            <h4 class="modal-title">修改银行卡</h4>
        </div>
        <?php if ($list): ?>
            <?php foreach ($list as $bank_list): ?>
                <form id="edit_bank_card">
                    <input type="hidden" value="<?php echo $bank_list['id'] ?>" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">收款银行:</label>
                            <?php if ($bank): ?>
                                <div class="col-sm-10" style="position:static">					  					  	
                                    <select data-placeholder="Choose One" style="width:300px;padding:0 10px;" id="select-basic" name="bank_id">
                                        <option selected='selected' value="<?php echo $bank_list['bank_id'] ?>"><?php echo $bank_list['bank_name'] ?></option>
                                        <?php foreach ($bank as $value): ?>
                                            <option value="<?php echo $value['id'] ?>" ><?php echo $value['name'] ?></option>
                                        <?php endforeach; ?>		
                                    </select>					
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group" style="clear:both">
                            <label class="col-sm-2 control-label">开户行:</label>
                            <div class="col-sm-10" style="position:static">
                                <input type="text" data-prompt-position="topLeft" class="form-control validate[required]" tag="开户行" name="open_bank" value="<?php echo $bank_list['open_bank'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">卡号:</label>
                            <div class="col-sm-10" style="position:static">
                                <input type="text" class="form-control validate[required,custom[onlyNumberSp],minSize[15],maxSize[20]]" tag="卡号" name="account" value="<?php echo $bank_list['account'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">账户名:</label>
                            <div class="col-sm-10" style="position:static">
                                <input type="text" class="form-control validate[required]" tag="账户名" name="account_name" value="<?php echo $bank_list['account_name'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="edit_bank_card_btn">修改</button>
                    </div>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
		jQuery("#select-basic, #select-multi").select2();
        $('#edit_bank_card').validationEngine({
            autoHidePrompt: false,
            scroll: false,
            autoHideDelay: 3000,
            maxErrorsPerField: 1
        });
        $('#edit_bank_card_btn').click(function() {

            if ($('#edit_bank_card').validationEngine('validate') == true) {
                $.post('/finance/account/editBank', $('#edit_bank_card').serialize(), function(data) {
                    if (data.error === 0) {
                        alert('修改成功',function(){window.location.partReload();});
                    } else {
                        alert(data.msg);
                    }
                }, 'json')
            }
        })
    });
</script>
