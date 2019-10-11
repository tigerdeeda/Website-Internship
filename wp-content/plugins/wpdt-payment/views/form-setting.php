<div class="wrap">
  <div id="icon-options-general" class="icon32"> <br />
  </div>
  <h2><?php echo __('WPDT Payment Setting', 'wpdt-payment') ; ?></h2>
  <p><?php echo __('รายละเอียดแจ้งการโอนเงิน', 'wpdt-payment') ?> </p>
  
  <pre>
    <?php 
    
    $wpdt_pament_options = get_option('wpdt_pament_options');

    ?>
  </pre>  
  <form action="" method="post" enctype="multipart/form-data">
    
  	<?php $nonce= wp_create_nonce  ('wpdt_payment_wpnonce'); ?>
    <input name="wpdt_payment_wpnonce" type="hidden" value="<?php echo $nonce; ?>">
    <table class="form-table" width="100%">
      
      <tr valign="top">
        <th scope="row"> <label for="admin_email">ถึง</label></th>
        <td><input name="wpdt_payment_email_to" type="text" id="wpdt_payment_email_to" value="<?php echo $wpdt_pament_options['wpdt_payment_email_to']; ?>" class="regular-text"/>
          <span class="description">อีเมล์สำรับข้อมูลแจ้งการชำระงิน.</span> </td>
        </tr>
        <tr valign="top">
          <th scope="row"> <label for="admin_email">จาก</label></th>
          <td><input name="wpdt_payment_email_form" type="text" id="wpdt_payment_email_form" value="<?php echo $wpdt_pament_options['wpdt_payment_email_form']; ?>" class="regular-text"/>
           <span class="description"> อีเมล์ผู้ส่ง</span> </td>
         </tr>
         <tr valign="top">
          <th scope="row"> <label for="admin_email">หัวข้ออีเมล์</label></th>
          <td><input name="wpdt_payment_email_subject" type="text" id="wpdt_payment_email_subject" value="<?php echo $wpdt_pament_options['wpdt_payment_email_subject']; ?>" class="regular-text"/>
            <span class="description">หัวข้อสำหรับแสดงแจ้งการชำระเงิน.</span> </td>
          </tr>
          <tr valign="top">
            <th scope="row">ประเภทรายการที่ชำระ:</th>
            <td>
              <textarea name="wpdt_payment_types" rows="5" cols="50" id="wpdt_payment_types" class="large-text code"><?php echo $wpdt_pament_options['wpdt_payment_types']; ?></textarea>
              <span class="description">ตัวอย่าง : ประเภทที่ 1,ประเภทที่ 2,ประเภทที่ 3,ประเภทที่ 4....<br>
              </span>        </td>
            </tr>
            <tr valign="top">
              <th scope="row">รายการธนาคาร</th>
              <td>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><strong>ธนาคาร</strong></td>
                    <td><strong>สาขา</strong></td>
                    <td><strong>ชื่อบัญชี</strong></td>
                    <td><strong>เลขที่บัญชี</strong></td>
                  </tr>    
                  <tr>
                    <td>
                      <select name="wpdt_bank[0]" id="wpdt_bank[0]">
                       <option value="" selected > -- เลือกธนาคาร -- </option>
                       <?php
                       global $wpdt_payment_banks_info;
                       foreach ($wpdt_payment_banks_info as $key => $value) {
                        $select="";
                        if($key==$wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks'][0]){$select=' selected="selected"';}
                        echo '<option value="'.$key.'" '.$select.'>'.$value['name'].'</option>';
                      }
                      ?>
                    </select>
                  </td>
                  <td><input class="large-text" type="text" name="wpdt_bank_branch[0]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_branch'][0]; ?>"/></td>
                  <td><input class="large-text" type="text" name="wpdt_bank_account_name[0]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_name'][0]; ?>"/></td>
                  <td><input class="large-text" type="text" name="wpdt_bank_account_number[0]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_number'][0]; ?>" /></td>
                </tr>
                <tr>
                  <td>
                   <select name="wpdt_bank[1]" id="wpdt_bank[1]">
                     <option value=""> -- เลือกธนาคาร -- </option>
                     <?php
                     global $wpdt_payment_banks_info;
                     foreach ($wpdt_payment_banks_info as $key => $value) {
                       $select="";
                       if($key==$wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks'][1]){$select=' selected="selected"';}
                       echo '<option value="'.$key.'" '.$select.'>'.$value['name'].'</option>';
                     }
                     ?>
                   </select>                </td>
                   <td><input class="large-text" type="text" name="wpdt_bank_branch[1]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_branch'][1]; ?>"/></td>
                   <td><input class="large-text" type="text" name="wpdt_bank_account_name[1]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_name'][1]; ?>"/></td>
                   <td><input class="large-text" type="text" name="wpdt_bank_account_number[1]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_number'][1]; ?>" /></td>
                 </tr>
                 
                 <tr>
                  <td>
                   <select name="wpdt_bank[2]" id="wpdt_bank[2]">
                    <option value=""> -- เลือกธนาคาร -- </option>
                    <?php
                    global $wpdt_payment_banks_info;
                    foreach ($wpdt_payment_banks_info as $key => $value) {
                     $select="";
                     if($key==$wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks'][2]){$select=' selected="selected"';}
                     echo '<option value="'.$key.'" '.$select.'>'.$value['name'].'</option>';
                   }
                   ?>
                 </select>                </td>
                 <td><input class="large-text" type="text" name="wpdt_bank_branch[2]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_branch'][2]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_name[2]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_name'][2]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_number[2]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_number'][2]; ?>" /></td>
               </tr>
               
               <tr>
                <td>
                	<select name="wpdt_bank[3]" id="wpdt_bank[3]">
                		<option value=""> -- เลือกธนาคาร -- </option>
                   <?php
                   global $wpdt_payment_banks_info;
                   foreach ($wpdt_payment_banks_info as $key => $value) {
                     $select="";
                     if($key==$wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks'][3]){$select=' selected="selected"';}
                     echo '<option value="'.$key.'" '.$select.'>'.$value['name'].'</option>';
                   }
                   ?>
                 </select>                </td>
                 <td><input class="large-text" type="text" name="wpdt_bank_branch[3]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_branch'][3]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_name[3]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_name'][3]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_number[3]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_number'][3]; ?>"/></td>
               </tr>
               
               <tr>
                <td>
                	<select name="wpdt_bank[4]" id="wpdt_bank[4]">
                		<option value=""> -- เลือกธนาคาร -- </option>
                   <?php
                   global $wpdt_payment_banks_info;
                   foreach ($wpdt_payment_banks_info as $key => $value) {
                     $select="";
                     if($key==$wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks'][4]){$select=' selected="selected"';}
                     echo '<option value="'.$key.'" '.$select.'>'.$value['name'].'</option>';
                   }
                   ?>
                 </select>                </td>
                 <td><input class="large-text" type="text" name="wpdt_bank_branch[4]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_branch'][4]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_name[4]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_name'][4]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_number[4]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_number'][4]; ?>" /></td>
               </tr>



               <tr>
                <td>
                  <select name="wpdt_bank[5]" id="wpdt_bank[5]">
                    <option value=""> -- เลือกธนาคาร -- </option>
                   <?php
                   global $wpdt_payment_banks_info;
                   foreach ($wpdt_payment_banks_info as $key => $value) {
                     $select="";
                     if($key==$wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks'][5]){$select=' selected="selected"';}
                     echo '<option value="'.$key.'" '.$select.'>'.$value['name'].'</option>';
                   }
                   ?>
                 </select>                </td>
                 <td><input class="large-text" type="text" name="wpdt_bank_branch[5]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_branch'][5]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_name[5]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_name'][5]; ?>"/></td>
                 <td><input class="large-text" type="text" name="wpdt_bank_account_number[5]" value="<?php echo $wpdt_pament_options['wpdt_payment_bank_accounts']['wpdt_banks_account_number'][5]; ?>" /></td>
               </tr>


             </table>
           </td>
         </tr>
         
         <tr valign="top">
          <th scope="row">ข้อความหลังแจ้งการโอนเงินเสร็จ</th>
          <td>
            <textarea name="wpdt_payment_form_text_success" rows="5" cols="50" id="wpdt_payment_form_text_success" class="large-text code"><?php echo $wpdt_pament_options['wpdt_payment_form_text_success']; ?></textarea></td>
          </tr>
        </table>

        <p>ฟอร์มแจ้งโอนเงิน Shortcocde  : [WPDTPAYMENT]</p>
        <p>รายการธนาคาร Shortcocde : [WPDT_PAYMENT_BANKS]</p>
        
        <p class="submit">
          <input type="submit" name="Submit" class="button-primary" value="<?php echo  __('Save Changes', 'wpdt-payment'); ?>" />
        </p>
      </form>
    </div>