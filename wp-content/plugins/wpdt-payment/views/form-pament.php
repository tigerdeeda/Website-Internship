<form class="form-wpdt-payment" name="form1" method="post" action=""  enctype="multipart/form-data">

 <?php $nonce= wp_create_nonce('wpdt_payment_form_wpnonce'); ?>
 <input name="wpdt_payment_form_wpnonce" type="hidden" value="<?php echo $nonce; ?>">

 <fieldset class="payment-info">

  <legend>กรอกข้อมูลชำระเงิน</legend>

  <table border="0" cellpadding="2" cellspacing="1" width="100%">
    <tbody>
      <tr>
        <td class="thead">ชื่อ สกุล</td>
        <td><input name="wpdt_payment_form_c_name" id="wpdt_payment_form_c_name" class="required text" type="text" value="<?php echo @$_POST['wpdt_payment_form_c_name']; ?>" placeholder="ผู้โอน" >
         <span class="required">*</span></td>
       </tr>
       <tr>
        <td class="thead">เบอร์โทร</td>
        <td><input name="wpdt_payment_form_c_tel" id="wpdt_payment_form_c_tel" class="required text" type="text" value="<?php @$_POST['wpdt_payment_form_c_tel']; ?>" placeholder="ผู้โอน">
          <span class="required">*</span></td>
        </tr>
        <tr>
          <td class="thead">อีเมล์ </td>
          <td><input name="wpdt_payment_form_c_email" id="wpdt_payment_form_c_email" class="required text email" type="text"  value="<?php @$_POST['wpdt_payment_form_c_email']; ?>" placeholder="ผู้โอน" > <span class="required">*</span></td>
        </tr>
        <tr>
          <td></td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td class="thead">เลขที่อ้างอิง</td>
          <td><input name="wpdt_payment_form_rf_id" id="wpdt_payment_form_rf_id" type="text" class="text"  value="<?php @$_POST['wpdt_payment_form_rf_id']; ?>" placeholder="ที่ต้องการชำระถ้ามี">        </td>
        </tr>
        <tr>
          <td class="thead">ประเภทรายการชำระ</td>
          <td><select name="wpdt_payment_form_p_type" id="wpdt_payment_form_p_type"  class="required select">
            <option value="">-- เลือก --</option>
            <?php 
            $wpdt_payment_types = explode(",",$wpdt_pament_options['wpdt_payment_types']);
            if(count($wpdt_payment_types)>0){
              foreach ($wpdt_payment_types as  $value) {
               echo '<option value="'.$value.'" >'.$value.'</option>';
             }
           }
           ?>
         </select>
         <span class="required">*</span></td>
       </tr>
       <tr>
        <td class="thead">วันที่โอนเงิน </td>
        <td>
          <select name="wpdt_payment_form_t_day" id="wpdt_payment_form_t_day">
            <option value="01" <?php if (!(strcmp("01", date('d')))) {echo "selected=\"selected\"";} ?>>01</option>
            <option value="02" <?php if (!(strcmp("02", date('d')))) {echo "selected=\"selected\"";} ?>>02</option>
            <option value="03" <?php if (!(strcmp("03", date('d')))) {echo "selected=\"selected\"";} ?>>03</option>
            <option value="04" <?php if (!(strcmp("04", date('d')))) {echo "selected=\"selected\"";} ?>>04</option>
            <option value="05" <?php if (!(strcmp("05", date('d')))) {echo "selected=\"selected\"";} ?>>05</option>
            <option value="06" <?php if (!(strcmp("06", date('d')))) {echo "selected=\"selected\"";} ?>>06</option>
            <option value="07" <?php if (!(strcmp("07", date('d')))) {echo "selected=\"selected\"";} ?>>07</option>
            <option value="08" <?php if (!(strcmp("08", date('d')))) {echo "selected=\"selected\"";} ?>>08</option>
            <option value="09" <?php if (!(strcmp("09", date('d')))) {echo "selected=\"selected\"";} ?>>09</option>
            <option value="10" <?php if (!(strcmp("10", date('d')))) {echo "selected=\"selected\"";} ?>>10</option>
            <option value="11" <?php if (!(strcmp("11", date('d')))) {echo "selected=\"selected\"";} ?>>11</option>
            <option value="12" <?php if (!(strcmp("12", date('d')))) {echo "selected=\"selected\"";} ?>>12</option>
            <option value="13" <?php if (!(strcmp("13", date('d')))) {echo "selected=\"selected\"";} ?>>13</option>
            <option value="14" <?php if (!(strcmp("14", date('d')))) {echo "selected=\"selected\"";} ?>>14</option>
            <option value="15" <?php if (!(strcmp("15", date('d')))) {echo "selected=\"selected\"";} ?>>15</option>
            <option value="16" <?php if (!(strcmp("16", date('d')))) {echo "selected=\"selected\"";} ?>>16</option>
            <option value="17" <?php if (!(strcmp("17", date('d')))) {echo "selected=\"selected\"";} ?>>17</option>
            <option value="18" <?php if (!(strcmp("18", date('d')))) {echo "selected=\"selected\"";} ?>>18</option>
            <option value="19" <?php if (!(strcmp("19", date('d')))) {echo "selected=\"selected\"";} ?>>19</option>
            <option value="20" <?php if (!(strcmp("20", date('d')))) {echo "selected=\"selected\"";} ?>>20</option>
            <option value="21" <?php if (!(strcmp("21", date('d')))) {echo "selected=\"selected\"";} ?>>21</option>
            <option value="22" <?php if (!(strcmp("22", date('d')))) {echo "selected=\"selected\"";} ?>>22</option>
            <option value="23" <?php if (!(strcmp("23", date('d')))) {echo "selected=\"selected\"";} ?>>23</option>
            <option value="24" <?php if (!(strcmp("24", date('d')))) {echo "selected=\"selected\"";} ?>>24</option>
            <option value="25" <?php if (!(strcmp("25", date('d')))) {echo "selected=\"selected\"";} ?>>25</option>
            <option value="26" <?php if (!(strcmp("26", date('d')))) {echo "selected=\"selected\"";} ?>>26</option>
            <option value="27" <?php if (!(strcmp("27", date('d')))) {echo "selected=\"selected\"";} ?>>27</option>
            <option value="28" <?php if (!(strcmp("28", date('d')))) {echo "selected=\"selected\"";} ?>>28</option>
            <option value="29" <?php if (!(strcmp("29", date('d')))) {echo "selected=\"selected\"";} ?>>29</option>
            <option value="30" <?php if (!(strcmp("30", date('d')))) {echo "selected=\"selected\"";} ?>>30</option>
            <option value="31" <?php if (!(strcmp("31", date('d')))) {echo "selected=\"selected\"";} ?>>31</option>
          </select>

          <select name="wpdt_payment_form_t_mount" id="wpdt_payment_form_t_mount">
            <option value="01" <?php if (!(strcmp("01", date('m')))) {echo "selected=\"selected\"";} ?>>มกราคม</option>
            <option value="02" <?php if (!(strcmp("02", date('m')))) {echo "selected=\"selected\"";} ?>>กุมภาพันธ์</option>
            <option value="03" <?php if (!(strcmp("03", date('m')))) {echo "selected=\"selected\"";} ?>>มีนาคม</option>
            <option value="04" <?php if (!(strcmp("04", date('m')))) {echo "selected=\"selected\"";} ?>>เมษายน</option>
            <option value="05" <?php if (!(strcmp("05", date('m')))) {echo "selected=\"selected\"";} ?>>พฤษภาคม</option>
            <option value="06" <?php if (!(strcmp("06", date('m')))) {echo "selected=\"selected\"";} ?>>มิถุนายน</option>
            <option value="07" <?php if (!(strcmp("07", date('m')))) {echo "selected=\"selected\"";} ?>>กรกฎาคม</option>
            <option value="08" <?php if (!(strcmp("08", date('m')))) {echo "selected=\"selected\"";} ?>>สิงหาคม</option>
            <option value="09" <?php if (!(strcmp("09", date('m')))) {echo "selected=\"selected\"";} ?>>กันยายน</option>
            <option value="10" <?php if (!(strcmp("10", date('m')))) {echo "selected=\"selected\"";} ?>>ตุลาคม</option>
            <option value="11" <?php if (!(strcmp("11", date('m')))) {echo "selected=\"selected\"";} ?>>พฤศจิกายน</option>
            <option value="02" <?php if (!(strcmp("12", date('m')))) {echo "selected=\"selected\"";} ?>>ธันวาคม</option>
          </select>
          <input name="wpdt_payment_form_t_year" type="text" id="wpdt_payment_form_t_year" size="10" maxlength="4"  class="required text" value="<?php echo date('Y'); ?>"><span class="required">*</span>

        </td>
      </tr>
      <tr>
        <td class="thead">เวลา</td>
        <td> <input name="wpdt_payment_formt_time" type="text" id="wpdt_payment_form_time" size="10"  class="required text" maxlength="10"  value="<?php @$_POST['wpdt_payment_form_time']; ?>" placeholder="การโอน"> <span class="required">*</span> </td>
      </tr>
      <tr>
        <td class="thead">จำนวนเงิน</td>
        <td><input name="wpdt_payment_formtotal" id="wpdt_payment_formtotal"  class="required text" type="text"  value="<?php @$_POST['wpdt_payment_formtotal']; ?>" placeholder="ทีโอน">
          <span class="required">*</span></td>
        </tr>
      </tbody>
    </table>
  </fieldset>

  <br>

  <fieldset class="banks">
    <legend>เลือกธนาคารที่โอนเข้า</legend>

    <table class="table-bank">
      <tbody>

       <?php
       if(count($wpdt_payment_bank_accounts['wpdt_banks']) > 0 ){
       foreach ($wpdt_payment_bank_accounts['wpdt_banks'] as $key => $value) {
        if($value){
         ?>
         <tr>
          <td class="thead"><label><input name="bank_select" type="radio" value="<?php echo $wpdt_payment_bank_accounts['wpdt_banks_name'][$key]; ?> <?php echo $wpdt_payment_bank_accounts['wpdt_banks_branch'][$key]; ?> ชื่อบัญชี <?php echo $wpdt_payment_bank_accounts['wpdt_banks_account_name'][$key]; ?> เลขที่ <?php echo $wpdt_payment_bank_accounts['wpdt_banks_account_number'][$key]; ?> " <?php if($wpdt_payment_bank_accounts['wpdt_banks_name'][$key] == @$_POST['bank_select']){ echo 'checked="checked"'; } ?> >
            <img src="<?php echo $wpdt_payment_bank_accounts['wpdt_banks_icon'][$key]; ?>" height="30" width="30" align="middle" alt="<?php echo $wpdt_payment_bank_accounts['wpdt_banks_name'][$key]; ?>"></label></td>
            <td>
              <div class="wpdt_payment_form_bank_info">
                <span class="wpdt_payment_form_bank_name"><?php echo $wpdt_payment_bank_accounts['wpdt_banks_name'][$key]; ?></span> <span class="wpdt_payment_form_bank_branch"><?php echo $wpdt_payment_bank_accounts['wpdt_banks_branch'][$key]; ?></span><br>
                <span class="wpdt_payment_form_account_name" >ชื่อบัญชี <?php echo $wpdt_payment_bank_accounts['wpdt_banks_account_name'][$key]; ?></span> <br><span class="wpdt_payment_form_account_number_text">เลขที่บัญชี</span> <span class="wpdt_payment_form_account_number"><?php echo $wpdt_payment_bank_accounts['wpdt_banks_account_number'][$key]; ?></span>
              </div>
            </td>
          </tr>
          <?php } } } ?>    
        </tbody>
      </table>
      
    </fieldset>

    <br>
    <strong>รายละเอียดเพิ่มเติม:</strong>
    <br>
    <p>
      <textarea name="wpdt_payment_formother_detail" cols="50" rows="5" id="wpdt_payment_formother_detail"><?php echo @$_POST['wpdt_payment_formother_detail']; ?></textarea>
    </p>
    <p>
      <strong>สลิป</strong> <input type="file" name="wpdt_payment_upload_file" id="my_image_upload"  multiple="false" />
    </p>

    <br>

    <p>
     <input name="Submit" class="submit" id="Submit3" value="แจ้งการชำระเงิน" type="submit" />
     <input name="Submit2" class="submit" value="ล้างข้อมูล" type="reset" />
   </p>


 </form>

 <script type="text/javascript">
   jQuery(document).ready(function($) {
    jQuery('form.form-wpdt-payment').validate();
  });
</script>
