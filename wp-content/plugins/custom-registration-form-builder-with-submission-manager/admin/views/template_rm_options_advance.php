<div class="rmagic">

    <!--Dialogue Box Starts-->
    <div class="rmcontent">


        <?php
//PFBC form
        $form = new RM_PFBC_Form("options_advance");
        $form->configure(array(
            "prevent" => array("bootstrap", "jQuery"),
            "action" => ""
        ));
        $form->addElement(new Element_HTML('<div class="rmheader">Advance Options</div>'));
        $form->addElement(new Element_Select("<b>Save Session at</b>", "session_policy", array('db'=>'Database','file'=>'File'), array("value" =>$data['session_policy'], "class" => "rm_static_field rm_required", "longDesc"=>'Define at which level sessions are saved.')));
        $form->addElement(new Element_HTMLL('&#8592; &nbsp; Cancel', '?page=rm_options_manage', array('class' => 'cancel')));
        $form->addElement(new Element_Button(RM_UI_Strings::get('LABEL_SAVE')));

        $form->render();
        ?>

    </div>
</div>
