<h2><?php echo $text_credit_card; ?></h2>
<form class="form-horizontal">
    <fieldset id="payment">
        <?php if($payment_model=="3d_hosting" || $payment_model=="hosting"){ ?>
        <label class="col-sm-12 control-label"><?php echo $text_3d_hosting; ?></label>
        <?php } else { ?>
        <legend></legend>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-owner"><?php echo $entry_cc_owner; ?></label>
            <div class="col-sm-10">
                <input type="text" name="cc_owner" value="" placeholder="<?php echo $entry_cc_owner; ?>" id="input-cc-owner" class="form-control"/>
            </div>
        </div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-number"><?php echo $entry_cc_number; ?></label>
            <div class="col-sm-10">
                <input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" id="input-cc-number" class="form-control"/>
            </div>
        </div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-expire-date"><?php echo $entry_cc_expire_date; ?></label>
            <div class="col-sm-3">
                <select name="cc_expire_date_month" id="input-cc-expire-date" class="form-control">
                    <?php foreach ($months as $month) { ?>
                    <option value="<?php echo $month['value']; ?>"><?php echo $month['value']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="cc_expire_date_year" class="form-control">
                    <?php foreach ($year_expire as $year) { ?>
                    <option value="<?php echo substr($year['value'], -2); ?>"><?php echo $year['text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="cc_type" class="form-control">
                    <?php foreach ($cc_types as $cc_type) { ?>
                    <option value="<?php echo $cc_type['value']; ?>"><?php echo $cc_type['text']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-cvv2"><?php echo $entry_cc_cvv2; ?></label>
            <div class="col-sm-10">
                <input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control"/>
            </div>
        </div>
        <?php } ?>
    </fieldset>
</form>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"/>
    </div>
</div>
<script type="text/javascript"><!--
    $('#button-confirm').bind('click', function () {
        $.ajax({
            url       : 'index.php?route=payment/trpos/send',
            type      : 'post',
            data      : $('#payment :input'),
            dataType  : 'json',
            cache     : false,
            beforeSend: function () {
                $('#button-confirm').button('loading');
            },
            complete  : function () {
                $('#button-confirm').button('reset');
            },
            success   : function (json) {
                if (json['error']) {
                    alert(json['error']);
                }
                if (json['form']) {
                    $('#button-confirm').button('loading');
                    $('body').append(json['form']);
                    $('#trpos_form').submit();
                }
                if (json['redirect']) {
                    location = json['redirect'];
                }
                if (json['payu3d']) {
                    location = json['payu3d'][0];
                }
            }
        });
    });
//--></script>
<style>
#form-trpos-confirm .form-horizontal {
    margin-top: -15px;
}

@media screen and (max-width: 768px) {
   #form-trpos-confirm h2 {
        margin-top: 40px;
   }
}
</style>