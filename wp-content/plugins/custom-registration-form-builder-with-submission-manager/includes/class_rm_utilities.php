<?php

/**
 * Utilities of plugin
 *
 * @author cmshelplive
 */
class RM_Utilities {

    private $instance;
    private $table_name_for;

    private function __construct() {
        
    }

    private function __wakeup() {
        
    }

    private function __clone() {
        
    }

    public static function get_instance() {
        if (!isset(self::$instance) && !( self::$instance instanceof RM_Utilities )) {
            self::$instance = new RM_Utilities();
        }

        return self::$instance;
    }

    /**
     * Redirect user to a url or post permalink with some delay
     * 
     * @param string $url 
     * @param boolean $is_post      if set true url will not be used. will redirect the user to $post_id
     * @param int $post_id          ID of the post on which user will be redirected
     * @param boolean/int $delay    Delay in redirection(in ms) or default 5s is used if set true
     */
    public static function redirect($url = '', $is_post = false, $post_id = 0, $delay = false) {

        if ($is_post && $post_id > 0) {
            $url = get_permalink($post_id);
        }

        if (headers_sent() || $delay) {
            if (defined('RM_AJAX_REQ'))
                $prefix = 'parent.';
            else
                $prefix = '';

            $string = '<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript">';
            if ($delay === true) {
                $string .= "window.setTimeout(function(){" . $prefix . "window.location.href = '" . $url . "';}, 5000);";
            } elseif ((int) $delay) {
                $string .= "window.setTimeout(function(){" . $prefix . "window.location.href = '" . $url . "';}, " . (int) $delay . ");";
            } else {
                $string .= $prefix . 'window.location = "' . $url . '"';
            }

            $string .= '</script></pre>';

            echo $string;
        } else {
            if (isset($_SERVER['HTTP_REFERER']) AND ( $url == $_SERVER['HTTP_REFERER']))
                wp_redirect($_SERVER['HTTP_REFERER']);
            else
                wp_redirect($url);

            exit;
        }
    }

    public static function user_role_dropdown($placeholder = false) {
        $roles = array();
        if ($placeholder)
            $roles[null] = RM_UI_Strings::get('PH_USER_ROLE_DD');

        if (!function_exists('get_editable_roles'))
            require_once ABSPATH . 'wp-admin/includes/user.php';

        $user_roles = get_editable_roles();
        foreach ($user_roles as $key => $value) {
            $roles[$key] = $value['name'];
        }
        return $roles;
    }

    public static function wp_pages_dropdown($args = null) {
        $wp_pages = array('Select page');
        if ($args === null)
            $args = array(
                'depth' => 0,
                'child_of' => 0,
                'selected' => 0,
                'echo' => 1,
                'name' => 'page_id',
                'id' => null, // string
                'class' => null, // string
                'show_option_none' => null, // string
                'show_option_no_change' => null, // string
                'option_none_value' => null, // string
            );

        $pages = get_pages($args);
        foreach ($pages as $page) {
            if (!$page->post_title) {
                $page->post_title = "#$page->ID (no title)";
            }
            $wp_pages[$page->ID] = $page->post_title;
        }

        return $wp_pages;
    }

    public static function merge_object($args, $defaults = null) {
        if ($args instanceof stdClass)
            if (is_object($defaults))
                foreach ($defaults as $key => $default)
                    if (!isset($args->$key))
                        $args->$key = $default;

        return $args;
    }

    public static function get_field_types($include_widgets= true,$form_type=1) {
        $field_types = array(
             null => 'Select A Field',
            'Textbox' => 'Text',
            'Select' => 'Drop Down',
            'Radio' => 'Radio Button',
            'Textarea' => 'Textarea',
            'Checkbox' => 'Checkbox',
            'jQueryUIDate' => 'Date',
            'Email' => 'Email',
            'Number' => 'Number',
            'Country' => 'Country',
            'Timezone' => 'Timezone',
            'Terms' => 'T&C Checkbox',
            'File' => 'File Upload',
            'Price' => 'Product',
            'Repeatable' => 'Repeatable Text',
            'Repeatable_M' => 'Repeatable Text',
            'Map' => 'Map',
            'Address' => 'Address',
            'Fname' => 'First Name',
            'Lname' => 'Last Name',
            'BInfo' => 'Biographical Info',
            'Phone' => 'Phone Number',
            'Mobile' => 'Mobile Number',
            'Password' => 'Password',
            'Nickname' => 'Nick Name',
            'Bdate' => 'Birth Date',
            'SecEmail' => 'Secondary Email',
            'Gender' => 'Gender',
            'Language' => 'Language',
            'Facebook' => 'Facebook',
            'Twitter' => 'Twitter',
            'Google' => 'Google+',
            'Linked' => 'LinkedIn',
            'Youtube' => 'YouTube',
            'ImageV'=> 'Image Widget',
            'VKontacte' => 'VKontacte',
            'Instagram' => 'Instagram',
            'Skype' => 'Skype ID',
            'SoundCloud' => 'SoundCloud',
            'Time' => 'Time',
            'Image' => 'Image Upload',
            'Shortcode' => 'Shortcode',
            'Multi-Dropdown' => 'Multi-Dropdown',
            'Rating' => 'Rating',
            'Website' => 'Website',
            'Custom' => 'Custom Field',
            'Hidden' =>'Hidden Field',
            'PriceV' => 'Total Price Widget',
            'MapV'=> 'Map',
            'SubCountV'=> 'Submission Count',
            'Form_Chart'=> 'Form Data Chart',
            'FormData' => 'Form Data',
            'Feed'=> 'Registration Feed',
            'Username' => 'Account Username',
            'UserPassword' => 'Account Password'
        );
        if($form_type==0){
            $field_types['Feed']= 'Submission Feed';
        }
        
        if($include_widgets){
            $field_types= array_merge($field_types,array('Timer'=>'Timer','RichText'=>'Rich Text',
                                     'Divider'=>'Divider','Spacing'=>'Spacing','HTMLP'=>'Paragraph',
                                      'HTMLH'=>'Heading','Link'=>'Link','YouTubeV'=>'YouTube Video',"Iframe"=>"Embed Iframe"));
        }
        return $field_types;
    }
       

    public static function after_login_redirect($user) {

        $gopts = new RM_Options;
        $redirect_to = $gopts->get_value_of("post_submission_redirection_url");
        $enforce_admin_redirect_to_dashboard = ($gopts->get_value_of("redirect_admin_to_dashboard_post_login") == "yes");

        if (!$redirect_to)
            return "";

        if ($enforce_admin_redirect_to_dashboard && isset($user->roles) && is_array($user->roles)) {
            if (in_array('administrator', $user->roles)) {
                return admin_url();
            }
        }

        switch ($redirect_to) {
            case "__current_url":
                if ($GLOBALS['pagenow'] === 'wp-login.php')
                    return admin_url();
                else {
                    $test = get_permalink(); //* Won't work from a widget!!*
                    if (!$test)
                        return "__current_url";
                    else
                        return $test;
                }
            case "__home_page":
                return get_home_url();

            case "__dashboard":
                return admin_url();
        }

        $url = home_url("?p=" . $redirect_to);
        return $url;
    }

    public static function get_current_url() {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);
        $query = '';
        if (!empty($parts['query'])) {
            // drop known fb params
            $params = explode('&', $parts['query']);
            $retained_params = array();
            foreach ($params as $param) {
                $retained_params[] = $param;
            } if (!empty($retained_params)) {
                $query = '?' . implode($retained_params, '&');
            }
        }        // use port if non default
        $port = isset($parts['port']) &&
                (($protocol === 'http://' && $parts['port'] !== 80) ||
                ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
    }

    public static function get_forms_dropdown($service) {
        $forms = $service->get_all('FORMS', $offset = 0, $limit = 0, $column = '*', $sort_by = 'created_on', $descending = true);
        $form_dropdown_array = array();
        if ($forms)
            foreach ($forms as $form)
                $form_dropdown_array[$form->form_id] = $form->form_name;
        return $form_dropdown_array;
    }

    public static function get_paypal_field_types($service) {
        $pricing_fields = $service->get_all('PAYPAL_FIELDS', $offset = 0, $limit = 999999, $column = '*');
        //var_dump($pricing_fields);
        $field_dropdown_array = array();
        if ($pricing_fields)
            foreach ($pricing_fields as $field)
                $field_dropdown_array[$field->field_id] = $field->name;
        else
            $field_dropdown_array[null] = RM_UI_Strings::get('MSG_CREATE_PRICE_FIELD');

        return $field_dropdown_array;
    }

    public static function send_email($to, $data) {
        /*
         * Function to send email
         */
    }

    public static function trim_array($var) {
        if (is_array($var) || is_object($var))
            foreach ($var as $key => $var_)
                if (is_array($var))
                    $var[$key] = self::trim_array($var_);
                else
                    $var->$key = self::trim_array($var_);
        else
            $var = trim($var);

        return $var;
    }

    public static function escape_array($var) {
        if (is_array($var) || is_object($var))
            foreach ($var as $key => $var_)
                if (is_array($var))
                    $var[$key] = self::escape_array($var_);
                else
                    $var->$key = self::escape_array($var_);
        else
            $var = addslashes($var);

        return $var;
    }

    public static function strip_slash_array($var) {
        if (is_array($var) || is_object($var))
            foreach ($var as $key => $var_)
                if (is_array($var))
                    $var[$key] = self::strip_slash_array($var_);
                else
                    $var->$key = self::strip_slash_array($var_);
        else
            $var = stripslashes($var);

        return $var;
    }

    public static function get_current_time($time = null) {
        if (!is_numeric($time))
            return gmdate('Y-m-d H:i:s');
        else
            return gmdate('Y-m-d H:i:s', $time);
    }

    public static function create_submission_page() {
        global $wpdb;

        $submission_page = array(
            'post_type' => 'page',
            'post_title' => 'Submissions',
            'post_status' => 'publish',
            'post_name' => 'rm_submissions',
            'post_content' => '[RM_Front_Submissions]'
        );

        $page_id = get_option('rm_option_front_sub_page_id');

        if ($page_id) {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Front_Submissions]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[CRF_Submissions]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
        } else {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Front_Submissions]%\" AND `post_status`='publish'");
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[CRF_Submissions]%\" AND `post_status`='publish'");
        }

        if (!$post) {
            $page_id = wp_insert_post($submission_page);
            update_option('rm_option_front_sub_page_id', $page_id);
        } else {
            if ($page_id != $post)
                update_option('rm_option_front_sub_page_id', $post);
        }
    }

    public static function create_login_page() {
        global $wpdb;

        $submission_page = array(
            'post_type' => 'page',
            'post_title' => 'Login',
            'post_status' => 'publish',
            'post_name' => 'rm_login',
            'post_content' => '[RM_Login]'
        );

        $page_id = get_option('rm_option_front_login_page_id');

        if ($page_id) {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish' AND `ID` = " . $page_id);
        } else {
            $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish'");
            if (!$post)
                $post = $wpdb->get_var("SELECT `ID` FROM  `" . $wpdb->prefix . "posts` WHERE  `post_content` LIKE  \"%[RM_Login]%\" AND `post_status`='publish'");
        }

        if (!$post) {
            $page_id = wp_insert_post($submission_page);
            update_option('rm_option_front_login_page_id', $page_id);
        } else {
            if ($page_id != $post)
                update_option('rm_option_front_login_page_id', $post);
        }
    }

    public static function get_class_name_for($model_identifier) {
        $prefix = 'RM_';
        $class_name = $prefix . self::ucwords(strtolower($model_identifier));
        return $class_name;
    }

    public static function ucwords($string, $delimiter = " ") {
        if ($delimiter != " ") {
            $str = str_replace($delimiter, " ", $string);
            $str = ucwords($str);
            $str = str_replace(" ", $delimiter, $str);
        } elseif ($delimiter == " ")
            $str = ucwords($string);

        return $str;
    }

    public static function convert_to_unix_timestamp($mysql_timestamp) {
        return strtotime($mysql_timestamp);
    }

    public static function convert_to_mysql_timestamp($unix_timestamp) {
        return date("Y-m-d H:i:s", $unix_timestamp);
    }

    public static function create_pdf($html = null, $title = null) {
        require_once plugin_dir_path(dirname(__FILE__)) . 'external/tcpdf_min/tcpdf.php';
// create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Registration Magic');
        $pdf->SetTitle('Submission');
        $pdf->SetSubject('PDF for Submission');
        $pdf->SetKeywords('submission,pdf,print');

// set default header data
        //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 006', PDF_HEADER_STRING);
        $pdf->SetHeaderData('', '', $title, '');

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set font
        $pdf->SetFont('courier', '', 10);

// add a page
        $pdf->AddPage();

        //var_dump(htmlentities(ob_get_contents()));die;
// output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');


// reset pointer to the last page
        $pdf->lastPage();
        if (ob_get_contents()) {
            ob_end_clean();
        }

//Close and output PDF document
        $pdf->Output('rm_submission.pdf', 'D');
    }

    public static function create_json_for_chart($string_label, $numeric_label, array $dataset) {
        $data_table = new stdClass;
        $data_table->cols = array();
        $data_table->rows = array();
        $data_table->cols = array(
            // Labels for your chart, these represent the column titles
            // Note that one column is in "string" format and another one is in "number" format as pie chart only require "numbers" for calculating percentage and string will be used for column title
            (object) array('label' => $string_label, 'type' => 'string'),
            (object) array('label' => $numeric_label, 'type' => 'number')
        );

        $rows = array();

        foreach ($dataset as $name => $value) {
            $temp = array();
            // the following line will be used to slice the Pie chart
            $temp[] = (object) array('v' => (string) $name);

            // Values of each slice
            $temp[] = (object) array('v' => (int) $value);
            $rows[] = (object) array('c' => $temp);
        }
        $data_table->rows = $rows;
        $json_table = json_encode($data_table);
        return $json_table;
    }

    public static function HTMLToRGB($htmlCode) {
        if ($htmlCode[0] == '#')
            $htmlCode = substr($htmlCode, 1);

        if (strlen($htmlCode) == 3) {
            $htmlCode = $htmlCode[0] . $htmlCode[0] . $htmlCode[1] . $htmlCode[1] . $htmlCode[2] . $htmlCode[2];
        }

        $r = hexdec($htmlCode[0] . $htmlCode[1]);
        $g = hexdec($htmlCode[2] . $htmlCode[3]);
        $b = hexdec($htmlCode[4] . $htmlCode[5]);

        return $b + ($g << 0x8) + ($r << 0x10);
    }

    public static function RGBToHSL($RGB) {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;

        $r = ((float) $r) / 255.0;
        $g = ((float) $g) / 255.0;
        $b = ((float) $b) / 255.0;

        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);

        $l = ($maxC + $minC) / 2.0;

        if ($maxC == $minC) {
            $s = 0;
            $h = 0;
        } else {
            if ($l < .5) {
                $s = ($maxC - $minC) / ($maxC + $minC);
            } else {
                $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
            }
            if ($r == $maxC)
                $h = ($g - $b) / ($maxC - $minC);
            if ($g == $maxC)
                $h = 2.0 + ($b - $r) / ($maxC - $minC);
            if ($b == $maxC)
                $h = 4.0 + ($r - $g) / ($maxC - $minC);

            $h = $h / 6.0;
        }

        $h = (int) round(255.0 * $h);
        $s = (int) round(255.0 * $s);
        $l = (int) round(255.0 * $l);

        return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
    }

    public static function send_mail($email) {
        add_action('phpmailer_init', 'RM_Utilities::config_phpmailer');

        $success = true;

        if (!$email->to)
            return false;

        //Just in case if data has not been supplied, set proper default values so email function does not fail.
        $exdata = property_exists($email, 'exdata') ? $email->exdata : null;
        //Checking using isset instead of property_exists as we do not want to get null value getting passed as attachments.
        $attachments = isset($email->attachments) ? $email->attachments : array();

        if (is_array($email->to)) {
            foreach ($email->to as $to) {

                if (!self::rm_wp_mail($email->type, $to, $email->subject, $email->message, $email->header, $exdata, $attachments))
                    ;
                $success = false;
            }
        } else
            $success = self::rm_wp_mail($email->type, $email->to, $email->subject, $email->message, $email->header, $exdata, $attachments);

        return $success;
    }

    //Sends a generic mail to a given address.
    public static function quick_email($to, $sub, $body, $mail_type = RM_EMAIL_GENERIC, array $extra_params = null) {
        $params = new stdClass;
        $params->type = $mail_type;
        $params->to = $to;
        $params->subject = $sub;
        $params->message = $body;

        //Add exra params if available
        if ($extra_params) {
            foreach ($extra_params as $param_name => $param_value)
                $params->$param_name = $param_value;
        }

        RM_Email_Service::quick_email($params);
    }

    private static function rm_wp_mail($mail_type, $to, $subject, $message, $header, $additional_data = null, $attachments = array()) {

        $mails_not_to_be_saved = array(RM_EMAIL_USER_ACTIVATION_ADMIN,
            RM_EMAIL_PASSWORD_USER,
            RM_EMAIL_POSTSUB_ADMIN,
            /* RM_EMAIL_NOTE_ADDED, */
            RM_EMAIL_TEST);

        $sent_res = wp_mail($to, $subject, $message, $header, $attachments);
        $was_sent_successfully = $sent_res ? 1 : 0;

        $sent_on = gmdate('Y-m-d H:i:s');
        if (!in_array($mail_type, $mails_not_to_be_saved)) {
            $form_id = null;
            $exdata = null;

            if (is_array($additional_data) && count($additional_data) > 0) {
                if (isset($additional_data['form_id']))
                    $form_id = $additional_data['form_id'];
                if (isset($additional_data['exdata']))
                    $exdata = $additional_data['exdata'];
            }
            $row_data = array('type' => $mail_type, 'to' => $to, 'sub' => htmlspecialchars($subject), 'body' => htmlspecialchars($message), 'sent_on' => $sent_on, 'headers' => $header, 'form_id' => $form_id, 'exdata' => $exdata, 'was_sent_success' => $was_sent_successfully);
            $fmts = array('%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d');

            RM_DBManager::insert_row('SENT_EMAILS', $row_data, $fmts);
        }
        return $sent_res;
    }

// format date string
    public static function localize_time($date_string, $dateformatstring = null, $advanced = false, $is_timestamp = false) {

        if ($is_timestamp) {
            $date_string = gmdate('Y-m-d H:i:s', $date_string);
        }

        if (!$dateformatstring) {
            $df = get_option('date_format', null) ?: 'd M Y';
            $tf = get_option('time_format', null) ?: 'h:ia';
            $dateformatstring = $df . ' @ ' . $tf;
        }

        return get_date_from_gmt($date_string, $dateformatstring);
    }

    public static function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'csv' => 'text/csv; charset=utf-8',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );
        $arr = explode('.', $filename);
        $ext = array_pop($arr);
        $ext = strtolower($ext);
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } else {
            return 'application/octet-stream';
        }
    }

    public static function config_phpmailer($phpmailer) {
        $options = new RM_Options;

        if ($options->get_value_of('enable_smtp') == 'yes') {
            $phpmailer->isSMTP();
            $phpmailer->SMTPDebug = 0;
            $phpmailer->Host = $options->get_value_of('smtp_host');
            $phpmailer->SMTPAuth = $options->get_value_of('smtp_auth') == 'yes' ? true : false;
            $phpmailer->Port = $options->get_value_of('smtp_port');
            $phpmailer->Username = $options->get_value_of('smtp_user_name');
            $phpmailer->Password = $options->get_value_of('smtp_password');
            $phpmailer->SMTPSecure = ($options->get_value_of('smtp_encryption_type') == 'enc_tls') ? 'tls' : (($options->get_value_of('smtp_encryption_type') == 'enc_ssl') ? 'ssl' : '' );
        }
        $phpmailer->From = $options->get_value_of('senders_email');
        $phpmailer->FromName = $options->get_value_of('senders_display_name');
        if (empty($phpmailer->AltBody))
            $phpmailer->AltBody = self::html_to_text_email($phpmailer->Body);

        return;
    }

    public static function check_smtp() {

        $options = new RM_Options;

        $bckup = $options->get_all_options();

        $email = isset($_POST['test_email']) ? $_POST['test_email'] : null;

        $options->set_values(array(
            'enable_smtp' => 'yes',
            'smtp_host' => isset($_POST['smtp_host']) ? $_POST['smtp_host'] : null,
            'smtp_auth' => isset($_POST['SMTPAuth']) ? $_POST['SMTPAuth'] : null,
            'smtp_port' => isset($_POST['Port']) ? $_POST['Port'] : null,
            'smtp_user_name' => isset($_POST['Username']) ? $_POST['Username'] : null,
            'smtp_password' => isset($_POST['Password']) ? $_POST['Password'] : null,
            'smtp_encryption_type' => isset($_POST['SMTPSecure']) ? $_POST['SMTPSecure'] : null,
            'senders_email' => isset($_POST['From']) ? $_POST['From'] : null,
            'senders_display_name' => isset($_POST['FromName']) ? $_POST['FromName'] : null
        ));
        if (!$email) {
            echo 'blank_email ' . RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_REQUIRED_MESSAGE');
            $options->set_values($bckup);
            die;
        }

        $test_email = new stdClass();
        $test_email->type = RM_EMAIL_TEST;
        $test_email->to = $email;
        $test_email->subject = 'Test SMTP Connection';
        $test_email->message = 'Test';
        $test_email->header = '';
        $test_email->attachments = array();
        if (self::send_mail($test_email))
            echo RM_UI_Strings::get('LABEL_SMTP_SUCCESS_MESSAGE');
        else
            echo RM_UI_Strings::get('LABEL_SMTP_FAIL_MESSAGE');

        $options->set_values($bckup);
        die;
    }

    public static function check_wordpress_default_mail() {

        $options = new RM_Options;

        $bckup = $options->get_all_options();

        $to = isset($_POST['test_email']) ? $_POST['test_email'] : null;
        $message = isset($_POST['message']) ? $_POST['message'] : null;
        $from = isset($_POST['From']) ? $_POST['From'] : null;
        $headers = "From:" . $from;

        if (!$to) {
            echo 'blank_email ' . RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_REQUIRED_MESSAGE');

            die;
        }
        if (wp_mail($to, 'Test Mail', $message, $headers)) {
            echo RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_SUCCESS_MESSAGE');
        } else {
            echo RM_UI_Strings::get('LABEL_WORDPRESS_DEFAULT_EMAIL_FAIL_MESSAGE');
        }
        die;
    }

    public static function handle_rating_operations() {
        $type = $_POST['type'];
        $data = $_POST['info'];
        $options = new RM_Options;
        $service = new RM_Services;
        $events = $options->get_value_of('review_events');
        if ($type == 'remind') {
            $events['event'] = $service->get_review_event();
            $events['status']['flag'] = 'remind';
            $events['status']['time'] = date('Y-m-d');
        } elseif ($type == 'wordpress') {
            $events['event'] = $service->get_review_event();
            $events['status']['flag'] = 'reviewed';
            $events['status']['time'] = date('Y-m-d');
        } elseif ($type == 'rating') {
            $events['rating'] = $data;
        } elseif ($type == 'feedback') {
            $events['event'] = $service->get_review_event();
            $events['status']['flag'] = 'feedback';
            $events['status']['time'] = date('Y-m-d');
        } else {
            
        }
        $options->set_value_of('review_events', $events);
        die;
    }

    public static function disable_newsletter_banner() {
        global $rm_env_requirements;

        if ($rm_env_requirements & RM_REQ_EXT_CURL) {
            require_once RM_EXTERNAL_DIR . "Xurl/rm_xurl.php";

            $xurl = new RM_Xurl("https://registrationmagic.com/subscribe_to_newsletter/");

            if (function_exists('is_multisite') && is_multisite()) {
                $nl_sub_mail = get_site_option('admin_email');
            } else {
                $nl_sub_mail = get_option('admin_email');
            }

            $user = get_user_by('email', $nl_sub_mail);
            $req_arr = array('sub_email' => $nl_sub_mail, 'fname' => $user->first_name, 'lname' => $user->last_name);

            $xurl->post($req_arr);
        }
        if (function_exists('is_multisite') && is_multisite()) {
            update_site_option('rm_option_newsletter_subbed', 1);
        } else {
            update_option('rm_option_newsletter_subbed', 1);
        }

        wp_die();
    }

    public static function is_ssl() {
        //return true;
        return is_ssl();
    }

    //More reliable check for write permission to a directory than the php native is_writable.
    public static function is_writable_extensive_check($path) {
        //NOTE: use a trailing slash for folders!!!
        if ($path{strlen($path) - 1} == '/') // recursively return a temporary file path
            return self::is_writable_extensive_check($path . uniqid(mt_rand()) . '.tmp');
        else if (is_dir($path))
            return self::is_writable_extensive_check($path . '/' . uniqid(mt_rand()) . '.tmp');
        // check tmp file for read/write capabilities
        $rm = file_exists($path);
        $f = @fopen($path, 'a');
        if ($f === false)
            return false;
        fclose($f);
        if (!$rm)
            unlink($path);
        return true;
    }

    //Check for fatal errors with which can not continue.
    public static function fatal_errors() {
        global $rm_env_requirements;
        global $regmagic_errors;
        $fatality = false;
        $error_msgs = array();

        //Now check for any other remaining errors that might be originally in the global variable
        if(is_array($regmagic_errors)){
            foreach ($regmagic_errors as $err) {
                if (!$err->should_cont) {
                    $fatality = true;
                    break;
                }
            }
        }
        

        if (!($rm_env_requirements & RM_REQ_EXT_SIMPLEXML)) {
            $regmagic_errors[RM_ERR_ID_EXT_SIMPLEXML] = (object) array('msg' => RM_UI_Strings::get('CRIT_ERR_XML'), 'should_cont' => false); //"PHP extension SimpleXML is not enabled on server. This plugin cannot function without it.";
            $fatality = true;
        }

        if (!($rm_env_requirements & RM_REQ_PHP_VERSION)) {
            $regmagic_errors[RM_ERR_ID_PHP_VERSION] = (object) array('msg' => RM_UI_Strings::get('CRIT_ERR_PHP_VERSION'), 'should_cont' => false); //"This plugin requires atleast PHP version 5.3. Cannot continue.";
            $fatality = true;
        }

        if (!($rm_env_requirements & RM_REQ_EXT_CURL)) {
            $regmagic_errors[RM_ERR_ID_EXT_CURL] = (object) array('msg' => RM_UI_Strings::get('RM_ERROR_EXTENSION_CURL'), 'should_cont' => true);
        }

        if (!($rm_env_requirements & RM_REQ_EXT_ZIP)) {
            $regmagic_errors[RM_ERR_ID_EXT_ZIP] = (object) array('msg' => RM_UI_Strings::get('RM_ERROR_EXTENSION_ZIP'), 'should_cont' => true);
        }


        return $fatality;
    }

    public static function rm_error_handler($errno, $errstr, $errfile, $errline) {
        global $regmagic_errors;

        var_dump($errno);
        var_dump($errstr);

        return true;
    }

    public static function is_banned_ip($ip_to_check, $format) {
        if ($format === null)
            return false;

        //compare directly in case of ipv6 ban pattern
        if ((bool) filter_var($format, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if ($ip_to_check == $format)
                return true;
            else
                return false;
        }

        $matchrx = '/';
        $gen_regex = array('[0-2]', '[0-9]', '[0-9]', '\.',
            '[0-2]', '[0-9]', '[0-9]', '\.',
            '[0-2]', '[0-9]', '[0-9]', '\.',
            '[0-2]', '[0-9]', '[0-9]');

        for ($i = 0; $i < 15; $i++) {
            if ($format[$i] == '?' || $format[$i] == '.')
                $matchrx .= $gen_regex[$i];
            else
                $matchrx .= $format[$i];
        }

        $matchrx .= '/';

        if (preg_match($matchrx, $ip_to_check) === 1)
            return true;
        else
            return false;
    }

    public static function is_banned_email($email_to_check, $format) {
        if (!$format)
            return false;

        $matchrx = '/';

        $gen_regex = array('?' => '.',
            '*' => '.*',
            '.' => '\.'
        );

        $formatlen = strlen($format);

        for ($i = 0; $i < $formatlen; $i++) {
            if ($format[$i] == '?' || $format[$i] == '.' || $format[$i] == '*')
                $matchrx .= $gen_regex[$format[$i]];
            else
                $matchrx .= $format[$i];
        }

        $matchrx .= '/';

        //Following check is employed instead preg_match so that partial matches
        //will not get selected unless user specifies using wildcard '*'.      
        $test = preg_replace($matchrx, '', $email_to_check);

        if ($test == '')
            return true;
        else
            return false;
    }

    public static function enc_str($string) {
        if (function_exists('mcrypt_encrypt')) {
            $key = 'A Terrific tryst with tyranny';
            $iv = @mcrypt_create_iv(
                            mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_DEV_URANDOM
            );

            $encrypted = @base64_encode($iv . mcrypt_encrypt(
                                    MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), $string, MCRYPT_MODE_CBC, $iv
                            )
            );
        } else { //Using open SSL
            $key = self::get_enc_key();
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($string, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            $encrypted = base64_encode($iv . $hmac . $ciphertext_raw);
        }

        return $encrypted;
    }

    public static function dec_str($string) {
        if (function_exists('mcrypt_encrypt')) {
            $key = 'A Terrific tryst with tyranny';

            $data = base64_decode($string);
            $iv = @substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

            $decrypted = @rtrim(
                            mcrypt_decrypt(
                                    MCRYPT_RIJNDAEL_128, hash('sha256', $key, true), substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv
                            ), "\0"
            );
        } else {
            $key = self::get_enc_key();
            $c = base64_decode($string);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $decrypted = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            if (hash_equals($hmac, $calcmac)) {//PHP 5.6+ timing attack safe comparison
                return $decrypted;
            }
        }


        return $decrypted;
    }

    public static function get_enc_key() {
        return "e0cb6eecb9ff1b6397ff";
    }

    public static function link_activate_user() {
        $req = $_GET['user'];

        $user_service = new RM_User_Services();

        $req_deco = self::dec_str($req);

        $user_data = json_decode($req_deco);

        echo '<!DOCTYPE html>
                    <html>
                    <head>
                      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                      <meta http-equiv="Content-Style-Type" content="text/css">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                      <title></title>
                      <meta name="Generator" content="Cocoa HTML Writer">
                      <meta name="CocoaVersion" content="1404.34">
                        <link rel="stylesheet" type="text/css" href="' . RM_BASE_URL . 'admin/css/style_rm_admin.css">
                    </head>
                    <body class="rmajxbody">
        <div class="rmagic">';

        echo '<div class="rm_user_activation_msg">';

        if ($user_data->activation_code == get_user_meta($user_data->user_id, 'rm_activation_code', true)) {
            if (!delete_user_meta($user_data->user_id, 'rm_activation_code')) {
                echo '<div class="rm_fail_del">' . RM_UI_Strings::get('ACT_AJX_FAILED_DEL') . '</div>';
                die;
            }

            if ($user_service->activate_user_by_id($user_data->user_id)) {
                $users = array($user_data->user_id);
                $user_service->notify_users($users, 'user_activated');
                echo '<h1 class="rm_user_msg_ajx">' . RM_UI_Strings::get('ACT_AJX_ACTIVATED') . '</h1>';
                $user = get_user_by('id', $user_data->user_id);
                echo '<div class = rm_user_info><div class="rm_field_cntnr"><div class="rm_user_label">' . RM_UI_Strings::get('LABEL_USER_NAME') . ' : </div><div class="rm_label_value">' . $user->user_login . '</div></div><div class="rm_field_cntnr"><div class="rm_user_label">' . RM_UI_Strings::get('LABEL_USEREMAIL') . ' : </div><div class="rm_label_value">' . $user->user_email . '</div></div></div>';
                echo '<div class="rm_user_msg_ajx">' . RM_UI_Strings::get('ACT_AJX_ACTIVATED2') . '</div>';
            } else
                echo '<div class="rm_not_authorized_ajax rm_act_fl">' . RM_UI_Strings::get('ACT_AJX_ACTIVATE_FAIL') . '</div>';
        } else
            echo '<div class="rm_not_authorized_ajax">' . RM_UI_Strings::get('ACT_AJX_NO_ACCESS') . '</div>';

        echo '</div></div></html></body>';
        /* ?>
          <button type="button" onclick="window.location.reload()">Retry</button>
          <button type="button" onclick="window.history.back()">GO BACK</button>
          <?php */
        die;
    }

    public static function html_to_text_email($html) {
        $html = str_replace('<br>', "\r\n", $html);
        $html = str_replace('<br/>', "\r\n", $html);
        $html = str_replace('</br>', "\r\n", $html);

        $html = strip_tags($html);

        return trim($html);
    }

    public static function set_default_form() {
        if (isset($_POST['rm_def_form_id'])) {
            $gopts = new RM_Options;
            $gopts->set_value_of('default_form_id', $_POST['rm_def_form_id']);
        }
        die;
    }

    public static function unset_default_form() {
        if (isset($_POST['rm_def_form_id'])) {
            $gopts = new RM_Options;
            $gopts->set_value_of('default_form_id', null);
        }
        die;
    }

    //One time login
    public static function safe_login() {
        if (isset($_SESSION['RM_SLI_UID'])) {
            $user_status_flag = get_user_meta($_SESSION['RM_SLI_UID'], 'rm_user_status', true);
            if ($user_status_flag === '0' || $user_status_flag === '')
                wp_set_auth_cookie($_SESSION['RM_SLI_UID']);
            unset($_SESSION['RM_SLI_UID']);
        }
    }

    //Loads scripts without wp_enque_script for ajax calls.
    public static function enqueue_external_scripts($handle, $src = false, $deps = array(), $ver = false, $in_footer = false) {
        if (!defined('RM_AJAX_REQ')) {
            if (!wp_script_is($handle, 'enqueued')) {
                if (wp_script_is($handle, 'registered'))
                    wp_enqueue_script($handle);
                else
                    wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
            }
        }elseif (!isset(self::$script_handle[$handle])) {
            self::$script_handle[$handle] = $src;
            return '<pre class="rm-pre-wrapper-for-script-tags"><script type="text/javascript" src="' . $src . '"></script></pre>';
        }
    }

    /*
     * Loads all the data requires in JS 
     * It will allow to use language strings in JS
     */

    public static function load_admin_js_data() {
        $data = new stdClass();
        echo json_encode($data);
        die;
    }

    public static function load_js_data() {
        $data = new stdClass();

        // Validation message override
        $data->validations = array();
        $data->validations['required'] = RM_UI_Strings::get("VALIDATION_REQUIRED");
        $data->validations['email'] = RM_UI_Strings::get("INVALID_EMAIL");
        $data->validations['url'] = RM_UI_Strings::get("INVALID_URL");
        $data->validations['pattern'] = RM_UI_Strings::get("INVALID_FORMAT");
        $data->validations['number'] = RM_UI_Strings::get("INVALID_NUMBER");
        $data->validations['digits'] = RM_UI_Strings::get("INVALID_DIGITS");
        $data->validations['maxlength'] = RM_UI_Strings::get("INVALID_MAXLEN");
        $data->validations['minlength'] = RM_UI_Strings::get("INVALID_MINLEN");
        $data->validations['max'] = RM_UI_Strings::get("INVALID_MAX");
        $data->validations['min'] = RM_UI_Strings::get("INVALID_MIN");

        echo json_encode($data);
        wp_die();
    }

    public static function save_submit_label() {
        $form_id = $_POST['form_id'];
        $label = $_POST['label'];

        $form = new RM_Forms;
        $form->load_from_db($form_id);
        $form->form_options->form_submit_btn_label = $label;
        $form->update_into_db();
        echo "changed";
        die;
    }

    public static function update_tour_state($tour_id, $state) {
        $gopts = new RM_Options;

        $existing_tour = $gopts->get_value_of('tour_state');

        if (is_array($existing_tour)) {
            $existing_tour[$tour_id] = strtolower($state);
        } else {
            $existing_tour = array($tour_id => strtolower($state));
        }
        $gopts->set_value_of('tour_state', $existing_tour);
    }

    public static function has_taken_tour($tour_id) {
        $gopts = new RM_Options;

        $existing_tour = $gopts->get_value_of('tour_state');

        if (isset($existing_tour[$tour_id]))
            return ($existing_tour[$tour_id] == 'taken');
        else
            return false;
    }

    public static function update_tour_state_ajax() {
        $tour_id = $_POST['tour_id'];
        $state = $_POST['state'];

        self::update_tour_state($tour_id, $state);
        wp_die();
    }

    public static function process_field_options($value) {
        $p_options = array();

        if (!is_array($value))
            $tmp_options = explode(',', $value);
        else
            $tmp_options = $value;

        foreach ($tmp_options as $val) {
            $val = trim($val);
            $val = trim($val, "|");
            $t = explode("|", $val);

            if (count($t) <= 1 || trim($t[1]) === "")
                $p_options[$val] = $val;
            else
                $p_options[trim($t[1])] = trim($t[0]);
        }

        return $p_options;
    }

    public static function get_lable_for_option($field_id, $opt_value) {
        $rmf = new RM_Fields;
        if (!$rmf->load_from_db($field_id))
            return $opt_value;

        //Return same value if it is not a multival field
        if (!in_array($rmf->field_type, array('Checkbox', 'Radio', 'Select')))
            return $opt_value;

        $val = $rmf->get_field_value();
        $p_opts = self::process_field_options($val);

        if (!is_array($opt_value)) {
            if (isset($p_opts[$opt_value]))
                return $p_opts[$opt_value];
            else
                return $opt_value;
        }
        else {
            $tmp = array();
            foreach ($opt_value as $val) {
                if (isset($p_opts[$val]))
                    $tmp[] = $p_opts[$val];
                else
                    $tmp[] = $val;
            }
            return $tmp;
        }
    }

    //Print nested array like vars as html table.
    public static function var_to_html($variable) {
        $html = "";

        if (is_array($variable) || is_object($variable)) {
            $html .= "<table style='border:none; padding:3px; width:100%; margin: 0px;'>";
            if (count($variable) === 0)
                $html .= "empty";
            foreach ($variable as $k => $v) {
                $html .= '<tr><td style="background-color:#F0F0F0; vertical-align:top; min-width:100px;">';
                $html .= '<strong>' . $k . "</strong></td><td>";
                $html .= self::var_to_html($v);
                $html .= "</td></tr>";
            }

            $html .= "</table>";
            return $html;
        }

        $html .= $variable ? $variable : "NULL";
        return $html;
    }

    public static function is_date_valid() {
        $date = $_POST['date'];

        try {
            $test = new DateTime($date);
            echo "VALID";
        } catch (Exception $e) {
            echo "INVALID";
        }

        wp_die();
    }

    public function handel_fb_subscribe() {
        $gopts = new RM_Options;
        $gopts->set_value_of('has_subbed_fb_page', 'yes');
        wp_die();
    }

    //Methods to simplify one-time-action option handeling
    public static function update_action_state($act_id, $state) {
        $gopts = new RM_Options;

        $one_time_actions = $gopts->get_value_of('one_time_actions');

        if (is_array($one_time_actions)) {
            $one_time_actions[$act_id] = $state;
        } else {
            $one_time_actions = array($act_id => $state);
        }
        $gopts->set_value_of('one_time_actions', $one_time_actions);
    }

    public static function has_action_occured($act_id) {
        $gopts = new RM_Options;

        $one_time_actions = $gopts->get_value_of('one_time_actions');

        if (isset($one_time_actions[$act_id]))
            return $one_time_actions[$act_id];
        else
            return false;
    }

    public static function get_allowed_conditional_fields() {
        return array('Textbox', 'Select', 'Radio', 'Checkbox', 'jQueryUIDate', 'Email', 'Number', 'Country', 'Website',
            'Language', 'Timezone', 'Fname', 'Lname', 'Phone', 'Mobile', 'Nickname', 'Bdate', 'Gender', 'Custom', 'Repeatable', 'Password', 'Terms');
    }

    public static function get_fields_dropdown($config = array()) {
        $service = new RM_Services();
        $fields = $service->get_all_form_fields($config['form_id']);
        $options = '';
        if (isset($config['full']))
            $options .= '<select name="' . $config['name'] . '" id="' . (isset($config['id']) ? $config['id'] : $config['name']) . '">';
        if ($fields)
            foreach ($fields as $field) {
                if (!empty($config['exclude']) && in_array($field->field_id, $config['exclude']))
                    continue;
                if (!empty($config['inc_by_type']) && !in_array($field->field_type, $config['inc_by_type']))
                    continue;
                if (!empty($config['ex_by_type']) && in_array($field->field_type, $config['ex_by_type']))
                    continue;
                if (isset($config['def']) && $field->field_id == $config['def'])
                    $options .= '<option selected value="' . $field->field_id . '">' . $field->field_label . '</option>';
                else
                    $options .= '<option value="' . $field->field_id . '">' . $field->field_label . '</option>';
            }
        if (isset($config['full']))
            $options .= '</select>';
        return $options;
    }

    public static function update_action_state_ajax() {
        $act_id = $_POST['action_id'];
        //Pass 'state' as string "true" or "false".
        $state = ($_POST['state'] == 'true');

        self::update_action_state($act_id, $state);
        wp_die();
    }

    public static function get_allowed_cond_op($config = array()) {
        return array(
            'Equals' => '==', 'Not equals' => '!=', 'Less than or equals' => '<=',
            'Less than' => '<', 'Greater than' => '>', 'Greater than or equals' => '>=',
            'Contains' => 'in',
            'Empty' => '_blank', 'Not Empty' => '_not_blank'
        );
    }

    public static function get_cond_op_dd($config = array()) {
        $operators = self::get_allowed_cond_op();
        $options = '';
        if (isset($config['full']))
            $options .= '<select name="' . $config['name'] . '" id="' . (isset($config['id']) ? $config['id'] : $config['name']) . '">';

        foreach ($operators as $key => $op) {
            if (isset($config['def']) && $op == $config['def'])
                $options .= '<option selected value="' . $op . '">' . $key . '</option>';
            else
                $options .= '<option value="' . $op . '">' . $key . '</option>';
        }
        if (isset($config['full']))
            $options .= '</select>';
        return $options;
    }

    public static function pdf_excluded_widgets() {
        return array("Spacing", "HTMLCustomized", "HTML", "Timer", "Iframe","UserPassword");
    }

    public static function csv_excluded_widgets(){
        return array("HTMLH","Spacing","HTMLCustomized","HTML","Timer","HTMLP","Divider","Spacing","RichText","Link","YouTubeV","Iframe",'PriceV','SubCountV',"MapV","Form_Chart","FormData","Feed","ImageV","UserPassword","Username");
    }
    
    public static function submission_manager_excluded_fields(){
        return array('File','Spacing','Divider','HTMLH','HTMLP','RichText','Timer','YouTubeV',"Link","Iframe",'HTMLCustomized','ImageV','PriceV','SubCountV',"MapV","Form_Chart","FormData","Feed","UserPassword","Username");
    }

    public static function extract_youtube_embed_src($string) {
        return preg_replace(
                "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i", "$2", $string
        );
    }

    public static function extract_vimeo_embed_src($string) {

        return (int) substr(parse_url($string, PHP_URL_PATH), 1);
    }

    public static function check_src_type($string) {
        if (strpos($string, 'youtube') > 0) {
            return 'youtube';
        } elseif (strpos($string, 'vimeo') > 0) {
            return 'vimeo';
        } else {
            return 'unknown';
        }
    }

    public static function get_usa_states() {
        return array(
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'AA' => 'Armed Forces America',
            'AE' => 'Armed Forces Europe',
            'AP' => 'Armed Forces Pacific',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'DC' => 'District Of Columbia',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming',
        );
    }

    public static function get_canadian_provinces() {
        return array(
            'AB' => 'Alberta',
            'BC' => 'British Columbia',
            'MB' => 'Manitoba',
            'NB' => 'New Brunswick',
            'NL' => 'Newfoundland and Labrador',
            'NT' => 'Northwest Territories',
            'NS' => 'Nova Scotia',
            'NU' => 'Nunavut',
            'ON' => 'Ontario',
            'PE' => 'Prince Edward Island',
            'QC' => 'Québec',
            'SK' => 'Saskatchewan',
            'YT' => 'Yukon');
    }

    public static function get_countries() {
        return array(
            null => RM_UI_Strings::get("LABEL_SELECT_COUNTRY"),
            "Afghanistan[AF]" => "Afghanistan",
            "Aland Islands[AX]" => "Aland Islands",
            "Albania[AL]" => "Albania",
            "Algeria[DZ]" => "Algeria",
            "American Samoa[AS]" => "American Samoa",
            "Andorra[AD]" => "Andorra",
            "Angola[AO]" => "Angola",
            "Anguilla[AI]" => "Anguilla",
            "Antarctica[AQ]" => "Antarctica",
            "Antigua and Barbuda[AG]" => "Antigua and Barbuda",
            "Argentina[AR]" => "Argentina",
            "Armenia[AM]" => "Armenia",
            "Aruba[AW]" => "Aruba",
            "Australia[AU]" => "Australia",
            "Austria[AT]" => "Austria",
            "Azerbaijan[AZ]" => "Azerbaijan",
            "Bahamas, The[BS]" => "Bahamas, The",
            "Bahrain[BH]" => "Bahrain",
            "Bangladesh[BD]" => "Bangladesh",
            "Barbados[BB]" => "Barbados",
            "Belarus[BY]" => "Belarus",
            "Belgium[BE]" => "Belgium",
            "Belize[BZ]" => "Belize",
            "Benin[BJ]" => "Benin",
            "Bermuda[BM]" => "Bermuda",
            "Bhutan[BT]" => "Bhutan",
            "Bolivia[BO]" => "Bolivia",
            "Bosnia and Herzegovina[BA]" => "Bosnia and Herzegovina",
            "Botswana[BW]" => "Botswana",
            "Bouvet Island[BV]" => "Bouvet Island",
            "Brazil[BR]" => "Brazil",
            "British Indian Ocean Territory[IO]" => "British Indian Ocean Territory",
            "Brunei Darussalam[BN]" => "Brunei Darussalam",
            "Bulgaria[BG]" => "Bulgaria",
            "Burkina Faso[BF]" => "Burkina Faso",
            "Burundi[BI]" => "Burundi",
            "Cambodia[KH]" => "Cambodia",
            "Cameroon[CM]" => "Cameroon",
            "Canada[CA]" => "Canada",
            "Cape Verde[CV]" => "Cape Verde",
            "Cayman Islands[KY]" => "Cayman Islands",
            "Central African Republic[CF]" => "Central African Republic",
            "Chad[TD]" => "Chad",
            "Chile[CL]" => "Chile",
            "China[CN]" => "China",
            "Christmas Island[CX]" => "Christmas Island",
            "Cocos (Keeling) Islands[CC]" => "Cocos (Keeling) Islands",
            "Colombia[CO]" => "Colombia",
            "Comoros[KM]" => "Comoros",
            "Congo[CG]" => "Congo",
            "Congo, The Democratic Republic Of The[CD]" => "Congo, The Democratic Republic Of The",
            "Cook Islands[CK]" => "Cook Islands",
            "Costa Rica[CR]" => "Costa Rica",
            "Cote D'ivoire[CI]" => "Cote D'ivoire",
            "Croatia[HR]" => "Croatia",
            "Cuba[CU]" => "Cuba",
            "Cyprus[CY]" => "Cyprus",
            "Czech Republic[CZ]" => "Czech Republic",
            "Denmark[DK]" => "Denmark",
            "Djibouti[DJ]" => "Djibouti",
            "Dominica[DM]" => "Dominica",
            "Dominican Republic[DO]" => "Dominican Republic",
            "Ecuador[EC]" => "Ecuador",
            "Egypt[EG]" => "Egypt",
            "El Salvador[SV]" => "El Salvador",
            "Equatorial Guinea[GQ]" => "Equatorial Guinea",
            "Eritrea[ER]" => "Eritrea",
            "Estonia[EE]" => "Estonia",
            "Ethiopia[ET]" => "Ethiopia",
            "Falkland Islands (Malvinas)[FK]" => "Falkland Islands (Malvinas)",
            "Faroe Islands[FO]" => "Faroe Islands",
            "Fiji[FJ]" => "Fiji",
            "Finland[FI]" => "Finland",
            "France[FR]" => "France",
            "French Guiana[GF]" => "French Guiana",
            "French Polynesia[PF]" => "French Polynesia",
            "French Southern Territories[TF]" => "French Southern Territories",
            "Gabon[GA]" => "Gabon",
            "Gambia, The[GM]" => "Gambia, The",
            "Georgia[GE]" => "Georgia",
            "Germany[DE]" => "Germany",
            "Ghana[GH]" => "Ghana",
            "Gibraltar[GI]" => "Gibraltar",
            "Greece[GR]" => "Greece",
            "Greenland[GL]" => "Greenland",
            "Grenada[GD]" => "Grenada",
            "Guadeloupe[GP]" => "Guadeloupe",
            "Guam[GU]" => "Guam",
            "Guatemala[GT]" => "Guatemala",
            "Guernsey[GG]" => "Guernsey",
            "Guinea[GN]" => "Guinea",
            "Guinea-Bissau[GW]" => "Guinea-Bissau",
            "Guyana[GY]" => "Guyana",
            "Haiti[HT]" => "Haiti",
            "Heard Island and the McDonald Islands[HM]" => "Heard Island and the McDonald Islands",
            "Holy See[VA]" => "Holy See",
            "Honduras[HN]" => "Honduras",
            "Hong Kong[HK]" => "Hong Kong",
            "Hungary[HU]" => "Hungary",
            "Iceland[IS]" => "Iceland",
            "India[IN]" => "India",
            "Indonesia[ID]" => "Indonesia",
            "Iraq[IQ]" => "Iraq",
            "Ireland[IE]" => "Ireland",
            "Isle Of Man[IM]" => "Isle Of Man",
            "Israel[IL]" => "Israel",
            "Italy[IT]" => "Italy",
            "Jamaica[JM]" => "Jamaica",
            "Japan[JP]" => "Japan",
            "Jersey[JE]" => "Jersey",
            "Jordan[JO]" => "Jordan",
            "Kazakhstan[KZ]" => "Kazakhstan",
            "Kenya[KE]" => "Kenya",
            "Kiribati[KI]" => "Kiribati",
            "Korea, Republic Of[KR]" => "Korea, Republic Of",
            "Kuwait[KW]" => "Kuwait",
            "Kyrgyzstan[KG]" => "Kyrgyzstan",
            "Lao People's Democratic Republic[LA]" => "Lao People's Democratic Republic",
            "Latvia[LV]" => "Latvia",
            "Lebanon[LB]" => "Lebanon",
            "Lesotho[LS]" => "Lesotho",
            "Liberia[LR]" => "Liberia",
            "Libya[LY]" => "Libya",
            "Liechtenstein[LI]" => "Liechtenstein",
            "Lithuania[LT]" => "Lithuania",
            "Luxembourg[LU]" => "Luxembourg",
            "Macao[MO]" => "Macao",
            "Macedonia, The Former Yugoslav Republic Of[MK]" => "Macedonia, The Former Yugoslav Republic Of",
            "Madagascar[MG]" => "Madagascar",
            "Malawi[MW]" => "Malawi",
            "Malaysia[MY]" => "Malaysia",
            "Maldives[MV]" => "Maldives",
            "Mali[ML]" => "Mali",
            "Malta[MT]" => "Malta",
            "Marshall Islands[MH]" => "Marshall Islands",
            "Martinique[MQ]" => "Martinique",
            "Mauritania[MR]" => "Mauritania",
            "Mauritius[MU]" => "Mauritius",
            "Mayotte[YT]" => "Mayotte",
            "Mexico[MX]" => "Mexico",
            "Micronesia, Federated States Of[FM]" => "Micronesia, Federated States Of",
            "Moldova, Republic Of[MD]" => "Moldova, Republic Of",
            "Monaco[MC]" => "Monaco",
            "Mongolia[MN]" => "Mongolia",
            "Montenegro[ME]" => "Montenegro",
            "Montserrat[MS]" => "Montserrat",
            "Morocco[MA]" => "Morocco",
            "Mozambique[MZ]" => "Mozambique",
            "Myanmar[MM]" => "Myanmar",
            "Namibia[NA]" => "Namibia",
            "Nauru[NR]" => "Nauru",
            "Nepal[NP]" => "Nepal",
            "Netherlands[NL]" => "Netherlands",
            "Netherlands Antilles[AN]" => "Netherlands Antilles",
            "New Caledonia[NC]" => "New Caledonia",
            "New Zealand[NZ]" => "New Zealand",
            "Nicaragua[NI]" => "Nicaragua",
            "Niger[NE]" => "Niger",
            "Nigeria[NG]" => "Nigeria",
            "Niue[NU]" => "Niue",
            "Norfolk Island[NF]" => "Norfolk Island",
            "Northern Mariana Islands[MP]" => "Northern Mariana Islands",
            "Norway[NO]" => "Norway",
            "Oman[OM]" => "Oman",
            "Pakistan[PK]" => "Pakistan",
            "Palau[PW]" => "Palau",
            "Palestinian Territories[PS]" => "Palestinian Territories",
            "Panama[PA]" => "Panama",
            "Papua New Guinea[PG]" => "Papua New Guinea",
            "Paraguay[PY]" => "Paraguay",
            "Peru[PE]" => "Peru",
            "Philippines[PH]" => "Philippines",
            "Pitcairn[PN]" => "Pitcairn",
            "Poland[PL]" => "Poland",
            "Portugal[PT]" => "Portugal",
            "Puerto Rico[PR]" => "Puerto Rico",
            "Qatar[QA]" => "Qatar",
            "Reunion[RE]" => "Reunion",
            "Romania[RO]" => "Romania",
            "Russian Federation[RU]" => "Russian Federation",
            "Rwanda[RW]" => "Rwanda",
            "Saint Barthelemy[BL]" => "Saint Barthelemy",
            "Saint Helena[SH]" => "Saint Helena",
            "Saint Kitts and Nevis[KN]" => "Saint Kitts and Nevis",
            "Saint Lucia[LC]" => "Saint Lucia",
            "Saint Martin[MF]" => "Saint Martin",
            "Saint Pierre and Miquelon[PM]" => "Saint Pierre and Miquelon",
            "Saint Vincent and The Grenadines[VC]" => "Saint Vincent and The Grenadines",
            "Samoa[WS]" => "Samoa",
            "San Marino[SM]" => "San Marino",
            "Sao Tome and Principe[ST]" => "Sao Tome and Principe",
            "Saudi Arabia[SA]" => "Saudi Arabia",
            "Senegal[SN]" => "Senegal",
            "Serbia[RS]" => "Serbia",
            "Seychelles[SC]" => "Seychelles",
            "Sierra Leone[SL]" => "Sierra Leone",
            "Singapore[SG]" => "Singapore",
            "Slovakia[SK]" => "Slovakia",
            "Slovenia[SI]" => "Slovenia",
            "Solomon Islands[SB]" => "Solomon Islands",
            "Somalia[SO]" => "Somalia",
            "South Africa[ZA]" => "South Africa",
            "South Georgia and the South Sandwich Islands[GS]" => "South Georgia and the South Sandwich Islands",
            "Spain[ES]" => "Spain",
            "Sri Lanka[LK]" => "Sri Lanka",
            "Suriname[SR]" => "Suriname",
            "Svalbard and Jan Mayen[SJ]" => "Svalbard and Jan Mayen",
            "Swaziland[SZ]" => "Swaziland",
            "Sweden[SE]" => "Sweden",
            "Switzerland[CH]" => "Switzerland",
            "Taiwan[TW]" => "Taiwan",
            "Tajikistan[TJ]" => "Tajikistan",
            "Tanzania, United Republic Of[TZ]" => "Tanzania, United Republic Of",
            "Thailand[TH]" => "Thailand",
            "Timor-leste[TL]" => "Timor-leste",
            "Togo[TG]" => "Togo",
            "Tokelau[TK]" => "Tokelau",
            "Tonga[TO]" => "Tonga",
            "Trinidad and Tobago[TT]" => "Trinidad and Tobago",
            "Tunisia[TN]" => "Tunisia",
            "Turkey[TR]" => "Turkey",
            "Turkmenistan[TM]" => "Turkmenistan",
            "Turks and Caicos Islands[TC]" => "Turks and Caicos Islands",
            "Tuvalu[TV]" => "Tuvalu",
            "Uganda[UG]" => "Uganda",
            "Ukraine[UA]" => "Ukraine",
            "United Arab Emirates[AE]" => "United Arab Emirates",
            "United Kingdom[GB]" => "United Kingdom",
            "United States[US]" => "United States",
            "United States Minor Outlying Islands[UM]" => "United States Minor Outlying Islands",
            "Uruguay[UY]" => "Uruguay",
            "Uzbekistan[UZ]" => "Uzbekistan",
            "Vanuatu[VU]" => "Vanuatu",
            "Venezuela[VE]" => "Venezuela",
            "Vietnam[VN]" => "Vietnam",
            "Virgin Islands, British[VG]" => "Virgin Islands, British",
            "Virgin Islands, U.S.[VI]" => "Virgin Islands, U.S.",
            "Wallis and Futuna[WF]" => "Wallis and Futuna",
            "Western Sahara[EH]" => "Western Sahara",
            "Yemen[YE]" => "Yemen",
            "Zambia[ZM]" => "Zambia",
            "Zimbabwe[ZW]" => "Zimbabwe"
        );
    }

    public static function get_formdata_widget_html($field_id) {
        $field = new RM_Fields();
        $field->load_from_db($field_id);

        $class = $field->field_options->field_css_class;
        $html = "<div class='rmrow'><div class='fdata-row'>";
        $form_name = '';
        $form = new RM_Forms();
        $form->load_from_db($field->get_form_id());
        $stats = new RM_Analytics_Service();
        $stats_data = $stats->calculate_form_stats($field->get_form_id());
        $options = array("nu_form_views" => array("nu_views_text_before", "nu_views_text_after"),
            "nu_submissions" => array("nu_sub_text_before", "nu_sub_text_after"),
            "sub_limits" => array("sub_limit_text_before", "sub_limit_text_after"),
            "sub_date_limits" => array("sub_date_limit_text_before", "sub_date_limit_text_after"),
            "last_sub_rec" => array("ls_text_before", "ls_text_after"));

        foreach ($options as $key => $values) {
            $value = '';
            if (!empty($field->field_options->{$key}) && $field->field_options->{$key}) {
                if ($key == 'nu_form_views') {
                    $value = $stats_data->total_entries;
                } else if ($key == 'nu_submissions') {
                    $value = $stats_data->successful_submission;
                } else if ($key == "sub_limits") {
                    $fo = $form->form_options;
                    $value = $fo->form_submissions_limit;
                } else if ($key == "sub_date_limits") {
                    $limit_type = empty($field->field_options->sub_limit_ind) ? 'date' : $field->field_options->sub_limit_ind;
                    $fo = $form->form_options;
                    if ($form->get_form_should_auto_expire()) {
                        if (!empty($fo->form_expiry_date)) {
                            if ($limit_type == "days") {
                                $diff = strtotime($fo->form_expiry_date) - time();
                                if ($diff > 0) {
                                    $value = floor($diff / (60 * 60 * 24)) . ' Days ';
                                }
                            } else {
                                $value = $fo->form_expiry_date;
                            }
                        }
                    }
                } else if ($key == "last_sub_rec") {
                    $submission = RM_DBManager::get_last_submission();
                    if (!empty($submission)) {
                        $visited_on = strtotime($submission->submitted_on);
                        if (!empty($visited_on)) {
                            $visited_on = self::convert_to_mysql_timestamp(strtotime($submission->submitted_on));
                            $visited_on = self::localize_time($visited_on, 'd M Y, h:ia');
                            $value = $visited_on;
                        }
                    }
                }
                $html .= $field->field_options->{$values[0]} . " <span>$value</span> " . $field->field_options->{$values[1]} . '<br>';
            }
        }
        if ($field->field_options->show_form_name) {
            $html .= '<div class="rm-form-name"><h3>' . $form->get_form_name() . '</h3></div>';
        }

        if ($field->field_options->form_desc) {
            $html .= '<div class="rm-form-name">' . $form->form_options->form_description . '</div>';
        }

        $html .= "</div></div>";
        return $html;
    }

    public static function get_feed_widget_html($field_id) {
        $field= new RM_Fields();
        $field->load_from_db($field_id);
        
        $class=  $field->field_options->field_css_class;
        $limit=  (int)$field->field_options->max_items>0 ? $field->field_options->max_items : 5;
        $html = "<div class='rmrow $class'>";
        $initial='';
        $form_id= $field->get_form_id();
        $form= new RM_Forms();
        $form->load_from_db($form_id);

        if($form->get_form_type()==1){
            $user_repo= new RM_User_Repository();
            $users= $user_repo->get_users_for_front(array("form_id"=>$form_id,'limit'=>$limit));

            if(is_array($users) && count($users)>0){
            foreach($users as $user){
                if(empty($user->ID))
                    continue;
                
                $initial='';
                $value= $field->field_value;
                if($value=="user_login"){
                    $initial= $user->user_login;
                } else if($value=="first_name"){
                    $initial= get_user_meta($user->ID, "first_name", true);
                    $initial= empty($initial) ? $user->display_name : $initial;
                } else if($value=="last_name"){
                     $initial= get_user_meta($user->ID, "last_name", true);
                     $initial= empty($initial) ? $user->display_name : $initial;
                } else if($value=="custom"){
                    $initial= $field->field_options->custom_value;
                }
                else if($value=="display_name"){
                    $initial= $user->display_name;
                }
                else if($value=='in_last_name'){
                    $first_name= get_user_meta($user->ID, "first_name", true);
                    $last_name= get_user_meta($user->ID, "last_name", true);
                    if(empty($first_name) && empty($last_name)){
                        $initial= $user->display_name;
                    } else{
                        $first_initial= !empty($first_name) ? strtoupper($first_name[0]) : '';
                        $initial= $first_initial.' '.ucwords($last_name);
                    }
                }
                else if($value=="both_names"){
                    $first_name= get_user_meta($user->ID, "first_name", true);
                    $last_name= get_user_meta($user->ID, "last_name", true);
                    if(empty($first_name) && empty($last_name)){
                        $initial= $user->display_name;
                    }
                    else
                    $initial= $first_name.' '.$last_name;
                }
                $html .= "<div class='rm-rgfeed'>";
                 if($field->field_options->show_gravatar){
                    $html .= "<span class='rm-avatar'>".get_avatar($user->user_email)."</span>";
                }
                $html .="<div class='rm-rgfeed-user-info'> <span class='rm-rgfeed-user'>$initial </span>";
                if(!$field->field_options->hide_date){
                    if(empty($user->user_registered)){
                        $submission= RM_DBManager::get_submissions_for_user($user->user_email,1);
                        if(!empty($submission)){
                           $html .= RM_UI_Strings::get("LABEL_UNREGISTERED_SUB")." <b>".self::format_on_time($submission[0]->submitted_on)."</b>";
                        }
                        
                    }
                    else{
                        $html .= RM_UI_Strings::get("LABEL_REGISTERED_ON")." <b>".self::format_on_time($user->user_registered)."</b>";
                    }
                }
                else{
                    if(empty($user->user_registered)){
                        $submission= RM_DBManager::get_submissions_for_user($user->user_email,1);
                        if(!empty($submission)){
                           $html .= RM_UI_Strings::get("LABEL_UNREGISTERED_SUB");
                        }
                    }
                    else{
                        $html .= RM_UI_Strings::get("LABEL_REGISTERED_ON");
                    }
                }
      
                
                if(!$field->field_options->hide_country){ 
                    $submissions= RM_DBManager::get_latest_submission_for_user($user->user_email,$form_id);
                    if(!empty($submissions) && is_array($submissions))
                    {
                        $data= maybe_unserialize($submissions[0]->data);
                        $country='';
                        $country_field= RM_DBManager::get_field_by_type($form_id,'Country');
                        if(!empty($country_field) && isset($data[$country_field->field_id])){
                            $country= $data[$country_field->field_id]->value;
                            preg_match("/\[[A-Z]{2}\]/", $country,$matches);
                            if(!empty($matches)){
                                preg_match("/[A-Z]{2}/",$matches[0],$matches);
                                if(!empty($matches)){
                                    $flag= strtolower($matches[0]);
                                    $country_name= str_replace("["."$matches[0]"."]", '', $country);
                                    $country= '<b>'.$country_name.'</b> <img class="rm_country_flag" src="'.RM_IMG_URL.'flag/16/'.$flag.'.png" />';
                                }
                                
                            }     
                        }
                        if(!empty($country))
                             $html .= " from $country ";
                    }
                }
                $html .=" </div></div>";
            }
            } 
        } else {
            $submissions= RM_DBManager::get_submissions_for_form($form_id,$limit,0,'*','submitted_on',true);
            
            $value= $field->field_value;
            if($value=='custom'){
                $initial= $field->field_options->custom_value.' ';
            }
            else
            {
                $initial= ' User ';
            }
            foreach($submissions as $submission){ 
                $data= maybe_unserialize($submission->data);
                $html .= "<div class='rm-rgfeed'> ";
            
                  if($field->field_options->show_gravatar){
                    $html .= "<span class='rm-avatar'>".get_avatar($submission->user_email)."</span>";
                }
                    $html .="<div class='rm-rgfeed-user-info'><span class='rm-rgfeed-user'>$initial</span>";
                if(!$field->field_options->hide_date){
                    $html .= RM_UI_Strings::get("LABEL_SUBMITTED_ON")." <b>". self::format_on_time($submission->submitted_on)."</b>";
                }
                if(!$field->field_options->hide_country){
                    $data= maybe_unserialize($submission->data);
                    $country='';
                    $country_field= RM_DBManager::get_field_by_type($form_id,'Country');
                    if(!empty($country_field) && isset($data[$country_field->field_id])){
                        $country= $data[$country_field->field_id]->value;   
                        preg_match("/\[[A-Z]{2}\]/",$country,$matches);
                            if(!empty($matches)){
                                preg_match("/[A-Z]{2}/",$matches[0],$matches);
                                if(!empty($matches)){
                                    $flag= strtolower($matches[0]);
                                    $country_name= str_replace("["."$matches[0]"."]", '', $country);
                                    $country = '<b>'.$country_name.'</b> <img class="rm_country_flag" src="'.RM_IMG_URL.'flag/16/'.$flag.'.png" />';
                                }
                                
                        }  
                    }
                    if(!empty($country))
                         $html .= " from $country";
                }
                $html .="</div> </div>";
              
            }
        }
      $html .= "</div>";
      return $html;
    }

    public static function format_on_time($t) {
        $ts = strtotime($t);
        if ($ts >= strtotime("today"))
            return date('g:i A', $ts) . ' today';
        else if ($ts >= strtotime("yesterday"))
            return date('g:i A', $ts) . ' yesterday';
        else {
            $on = self::convert_to_mysql_timestamp($ts);
            $on = self::localize_time($on, 'd M Y, h:i A');
            return $on;
        }
    }
    
     public static function get_form_expiry_message($form_id){
         $service= new RM_Services();
         $form= new RM_Forms();
         $form->load_from_db($form_id);
         $expiry_details = $service->get_form_expiry_stats($form);
         $exp_str='';
         if($form->form_options->display_progress_bar=='default')
            $check_setting=$service->get_setting('display_progress_bar');
         else
            $check_setting=$form->form_options->display_progress_bar;
         if ($expiry_details->state !== 'perpetual' && $check_setting == 'yes')
          {
           if ($expiry_details->state === 'expired')
            $exp_str .= '<div class="rm-formcard-expired">' . 'Expired' . '</div>';
           else
            {
                switch ($expiry_details->criteria)
                {
                    case 'both':
                        $message = sprintf(RM_UI_Strings::get('EXPIRY_DETAIL_BOTH'), ($expiry_details->sub_limit - $expiry_details->remaining_subs), $expiry_details->sub_limit, $expiry_details->remaining_days);
                        $exp_str .= '<div class="rm-formcard-expired"><span class="rm_sandclock"></span>' . $message . '</div>';
                        break;
                    case 'subs':
                        $total = $expiry_details->sub_limit;
                        $rem = $expiry_details->remaining_subs;
                        $wtot = 100;
                        $rem = ($rem * 100) / $total;
                        $done = 100 - $rem;
                        $message = sprintf(RM_UI_Strings::get('EXPIRY_DETAIL_SUBS'), ($expiry_details->sub_limit - $expiry_details->remaining_subs), $expiry_details->sub_limit);
                        $exp_str .= '<div class="rm-formcard-expired"><span class="rm_sandclock"></span>' . $message . '</div>';
                        break;

                    case 'date':
                        $message = sprintf(RM_UI_Strings::get('EXPIRY_DETAIL_DATE'), $expiry_details->remaining_days);
                        $exp_str .= '<div class="rm-formcard-expired"><span class="rm_sandclock"></span>' . $message . '</div>';
                        break;
                }
            } 
            }
            return $exp_str;
    }
    
    public static function validate_username_characters($username,$form_id){
      $error= '';
      if(isset($username) && $username){
             $username= sanitize_text_field($username);
             $rm_service= new RM_Services();
             $field= $rm_service->get_primary_field_options('Username',$form_id);
             if(!isset($field->field_options))
                 return $error;
             $field_options= maybe_unserialize($field->field_options);
             if(is_array($field_options->username_characters)){
                  $expression_chars= array();        
                  foreach($field_options->username_characters as $scheme){
                      switch($scheme){
                          case 'alphabets': array_push ($expression_chars, 'a-zA-Z'); break;
                          case 'numbers': array_push ($expression_chars, '0-9'); break;
                          case 'underscores': array_push ($expression_chars, '_'); break;
                          case 'periods': array_push ($expression_chars, '.'); break;
                      }
 
                  }
                  if(!empty($expression_chars)){
                    $expression= implode('', $expression_chars);   
                    $expression= "/^[$expression]+$/";
                    if(!preg_match($expression, $username)){
                          $error= str_replace('{{allowed_characters}}',implode(',', $field_options->username_characters),$field_options->invalid_username_format);
                    }
                  }
                   
             }
         }
     return $error;    
  }
  
  public static function is_username_hidden($form_id){
      $form= new RM_Forms();
      $form->load_from_db($form_id);
      $form_options= $form->get_form_options();
      return isset($form_options->hide_username) ? $form_options->hide_username : false;
      
      /*
      $username_field = RM_DBManager::get_field_by_type($form_id, 'Username');
      if(empty($username_field))
          return true;
      
      return false;*/
  }  
  
   public static function sync_username_hide_option($form_id){
      $username_field = RM_DBManager::get_field_by_type($form_id, 'Username');
      $form_model= new RM_Forms();
      $form_model->load_from_db($form_id);
      if($form_model->get_form_type()!=RM_REG_FORM)
              return;
      $form_options= $form_model->get_form_options();
      if(empty($username_field)){
            $form_options->hide_username= 1;
       }
       else{
           $form_options->hide_username= 0;
       }
        $form_model->set_form_options($form_options);
        $form_model->update_into_db();
  }
  
  public static function get_password_regex($pw_rests) {
        if (in_array('PWR_MINLEN', $pw_rests->selected_rules) && isset($pw_rests->min_len) && $pw_rests->min_len)
            $min_len = $pw_rests->min_len;
        else
            $min_len = 0;

        if (in_array('PWR_MAXLEN', $pw_rests->selected_rules) && isset($pw_rests->max_len) && $pw_rests->max_len)
            $max_len = $pw_rests->max_len;
        else
            $max_len = '';

        $regex = '[A-Za-z\d$@$!%*#?&~`^(){}\[\]\-_+=;:"\'|\\\\\\/<>.,]{' . $min_len . ',' . $max_len . '}';

        if (in_array('PWR_UC', $pw_rests->selected_rules))
            $regex = '(?=.*[A-Z])' . $regex;
        if (in_array('PWR_NUM', $pw_rests->selected_rules))
            $regex = '(?=.*\d)' . $regex;
        if (in_array('PWR_SC', $pw_rests->selected_rules))
            $regex = '(?=.*[$@$!%*#?&])' . $regex;

        return $regex;
    }
    
    public static function sync_hide_option_with_fields($form_id){
      $service= new RM_Services();
      $form_model= new RM_Forms();
      $form_model->load_from_db($form_id);
      $has_primary_fields= $service->has_primary_fields($form_id);
      if($form_model->get_form_type()==RM_REG_FORM){
          if(!$has_primary_fields){
              $service->add_primary_fields($form_id);
          }
      }
      else
      {
          if($has_primary_fields){
            $username_field = RM_DBManager::get_field_by_type($form_id, 'Username');
            if(!empty($username_field)){
                $service->remove($username_field->field_id, 'FIELDS', array());
            }
            
            $password_field = RM_DBManager::get_field_by_type($form_id, 'UserPassword');
            if(!empty($password_field)){
                $service->remove($password_field->field_id, 'FIELDS', array());
            }
          }
      }
      
  }
  
  public static function get_country_dial_codes(){
	return array("AF"=> "+93",
	"AL"=> "+355",
	"DZ"=> "+213",
	"AS"=> "+1",
	"AD"=> "+376",
	"AO"=> "+244",
	"AI"=> "+1",
	"AG"=> "+1",
	"AR"=> "+54",
	"AM"=> "+374",
	"AW"=> "+297",
	"AU"=> "+61",
	"AT"=> "+43",
	"AZ"=> "+994",
	"BH"=> "+973",
	"BD"=> "+880",
	"BB"=> "+1",
	"BY"=> "+375",
	"BE"=> "+32",
	"BZ"=> "+501",
	"BJ"=> "+229",
	"BM"=> "+1",
	"BT"=> "+975",
	"BO"=> "+591",
	"BA"=> "+387",
	"BW"=> "+267",
	"BR"=> "+55",
	"IO"=> "+246",
	"VG"=> "+1",
	"BN"=> "+673",
	"BG"=> "+359",
	"BF"=> "+226",
	"MM"=> "+95",
	"BI"=> "+257",
	"KH"=> "+855",
	"CM"=> "+237",
	"CA"=> "+1",
	"CV"=> "+238",
	"KY"=> "+1",
	"CF"=> "+236",
	"TD"=> "+235",
	"CL"=> "+56",
	"CN"=> "+86",
	"CO"=> "+57",
	"KM"=> "+269",
	"CK"=> "+682",
	"CR"=> "+506",
	"CI"=> "+225",
	"HR"=> "+385",
	"CU"=> "+53",
	"CY"=> "+357",
	"CZ"=> "+420",
	"CD"=> "+243",
	"DK"=> "+45",
	"DJ"=> "+253",
	"DM"=> "+1",
	"DO"=> "+1",
	"EC"=> "+593",
	"EG"=> "+20",
	"SV"=> "+503",
	"GQ"=> "+240",
	"ER"=> "+291",
	"EE"=> "+372",
	"ET"=> "+251",
	"FK"=> "+500",
	"FO"=> "+298",
	"FM"=> "+691",
	"FJ"=> "+679",
	"FI"=> "+358",
	"FR"=> "+33",
	"GF"=> "+594",
	"PF"=> "+689",
	"GA"=> "+241",
	"GE"=> "+995",
	"DE"=> "+49",
	"GH"=> "+233",
	"GI"=> "+350",
	"GR"=> "+30",
	"GL"=> "+299",
	"GD"=> "+1",
	"GP"=> "+590",
	"GU"=> "+1",
	"GT"=> "+502",
	"GN"=> "+224",
	"GW"=> "+245",
	"GY"=> "+592",
	"HT"=> "+509",
	"HN"=> "+504",
	"HK"=> "+852",
	"HU"=> "+36",
	"IS"=> "+354",
	"IN"=> "+91",
	"ID"=> "+62",
	"IR"=> "+98",
	"IQ"=> "+964",
	"IE"=> "+353",
	"IL"=> "+972",
	"IT"=> "+39",
	"JM"=> "+1",
	"JP"=> "+81",
	"JO"=> "+962",
	"KZ"=> "+7",
	"KE"=> "+254",
	"KI"=> "+686",
	"XK"=> "+381",
	"KW"=> "+965",
	"KG"=> "+996",
	"LA"=> "+856",
	"LV"=> "+371",
	"LB"=> "+961",
	"LS"=> "+266",
	"LR"=> "+231",
	"LY"=> "+218",
	"LI"=> "+423",
	"LT"=> "+370",
	"LU"=> "+352",
	"MO"=> "+853",
	"MK"=> "+389",
	"MG"=> "+261",
	"MW"=> "+265",
	"MY"=> "+60",
	"MV"=> "+960",
	"ML"=> "+223",
	"MT"=> "+356",
	"MH"=> "+692",
	"MQ"=> "+596",
	"MR"=> "+222",
	"MU"=> "+230",
	"YT"=> "+262",
	"MX"=> "+52",
	"MD"=> "+373",
	"MC"=> "+377",
	"MN"=> "+976",
	"ME"=> "+382",
	"MS"=> "+1",
	"MA"=> "+212",
	"MZ"=> "+258",
	"NA"=> "+264",
	"NR"=> "+674",
	"NP"=> "+977",
	"NL"=> "+31",
	"AN"=> "+599",
	"NC"=> "+687",
	"NZ"=> "+64",
	"NI"=> "+505",
	"NE"=> "+227",
	"NG"=> "+234",
	"NU"=> "+683",
	"NF"=> "+672",
	"KP"=> "+850",
	"MP"=> "+1",
	"NO"=> "+47",
	"OM"=> "+968",
	"PK"=> "+92",
	"PW"=> "+680",
	"PS"=> "+970",
	"PA"=> "+507",
	"PG"=> "+675",
	"PY"=> "+595",
	"PE"=> "+51",
	"PH"=> "+63",
	"PL"=> "+48",
	"PT"=> "+351",
	"PR"=> "+1",
	"QA"=> "+974",
	"CG"=> "+242",
	"RE"=> "+262",
	"RO"=> "+40",
	"RU"=> "+7",
	"RW"=> "+250",
	"BL"=> "+590",
	"SH"=> "+290",
	"KN"=> "+1",
	"MF"=> "+590",
	"PM"=> "+508",
	"VC"=> "+1",
	"WS"=> "+685",
	"SM"=> "+378",
	"ST"=> "+239",
	"SA"=> "+966",
	"SN"=> "+221",
	"RS"=> "+381",
	"SC"=> "+248",
	"SL"=> "+232",
	"SG"=> "+65",
	"SK"=> "+421",
	"SI"=> "+386",
	"SB"=> "+677",
	"SO"=> "+252",
	"ZA"=> "+27",
	"KR"=> "+82",
	"ES"=> "+34",
	"LK"=> "+94",
	"LC"=> "+1",
	"SD"=> "+249",
	"SR"=> "+597",
	"SZ"=> "+268",
	"SE"=> "+46",
	"CH"=> "+41",
	"SY"=> "+963",
	"TW"=> "+886",
	"TJ"=> "+992",
	"TZ"=> "+255",
	"TH"=> "+66",
	"BS"=> "+1",
	"GM"=> "+220",
	"TL"=> "+670",
	"TG"=> "+228",
	"TK"=> "+690",
	"TO"=> "+676",
	"TT"=> "+1",
	"TN"=> "+216",
	"TR"=> "+90",
	"TM"=> "+993",
	"TC"=> "+1",
	"TV"=> "+688",
	"UG"=> "+256",
	"UA"=> "+380",
	"AE"=> "+971",
	"GB"=> "+44",
	"US"=> "+1",
	"UY"=> "+598",
	"VI"=> "+1",
	"UZ"=> "+998",
	"VU"=> "+678",
	"VA"=> "+39",
	"VE"=> "+58",
	"VN"=> "+84",
	"WF"=> "+681",
	"YE"=> "+967",
	"ZM"=> "+260",
	"ZW"=> "+263");
    } 
    
    public static function get_country_code($country){
        $code='';
        preg_match("/\[[A-Z]{2}\]/", $country, $matches);
        if (!empty($matches)) {
            preg_match("/[A-Z]{2}/", $matches[0], $matches);
            if (!empty($matches)) {
                $code = strtolower($matches[0]);
            }
        }  
        return $code;
    }
}
