<?php
function suppliers_admin_form_input() {
    $form['leftt'] 	= [];
    $form['leftb'] 	= ['information' => 'Thông tin nhà sản xuất'];
    $form['lang'] 	= ['info' => 'Thông Tin'];
    $form['right'] 	= ['media' => 'Media', 'seo' => 'Seo'];
    foreach (Language::list() as $key => $name) {
        if($key == Language::default()) $rules = 'trim|required'; else $rules = 'trim';
        $form['field'][$key.'_name']   = array('group' => 'info', 'lang'=> $key, 	'field' => $key.'[name]', 'label' => 'Tiêu đề', 'type'	=> 'text', 'note' 	=> 'Tiêu đề bài viết được lấy làm thẻ H1', 'rules' => $rules);
        $form['field'][$key.'_excerpt']   = array('group' => 'info', 'lang'=> $key, 	'field' => $key.'[excerpt]', 'label' => 'Mô tả', 'type'	=> 'wysiwyg-short', 'note' 	=> '', 'rules' => $rules);
    }

    $form['field']['firstname'] = [
        'group' => 'information',
        'field' => 'firstname',
        'type'  => 'text',
        'after' => '<div class="col-md-6" id="box_firstname"><label for="firstname" class="control-label">Họ</label><div class="form-group group">',
        'before' => '</div></div>'
    ];
    $form['field']['lastname'] = [
        'group' => 'information',
        'field' => 'lastname',
        'type' => 'text',
        'after'=>'<div class="col-md-6" id="box_lastname"><label for="lastname" class="control-label">Tên</label><div class="form-group group">',
        'before' => '</div></div>'
    ];

    $form['field']['email'] = [
        'group' => 'information',
        'field' => 'email',
        'label' => 'Email',
        'type' => 'email'
    ];

    $form['field']['phone'] = [
        'group' => 'information',
        'field' => 'phone',
        'label' => 'Số điện thoại',
        'type' => 'tel'
    ];

    $form['field']['address'] = [
        'group' => 'information',
        'field' => 'address',
        'label' => 'Địa chỉ',
        'type' => 'text'
    ];


    $form['field']['image']           = array('group' => 'media', 'field' => 'image', 			'label' => 'Ảnh đại diện', 		'value'=>'','type' => 'image', 'display' => 'inline');
    $form['field']['seo_title']       = array('group' => 'seo',   'field' => 'seo_title', 		'label' => 'Meta title', 		'value'=>'','type' => 'text',);
    $form['field']['seo_keywords']    = array('group' => 'seo',   'field' => 'seo_keywords', 	'label' => 'Meta Keyword', 		'value'=>'','type' => 'text',);
    $form['field']['seo_description'] = array('group' => 'seo',   'field' => 'seo_description', 'label' => 'Meta Description', 	'value'=>'','type' => 'textarea',);
    foreach (Metabox::gets() as $key => $metabox) {
        if($metabox['module'] == 'suppliers') {
            $content 		= $metabox['content'];
            $content_box 	= $metabox['content_box'];
            $form[$content] = form_add_group($form[$content], $key, $metabox['label'], $content_box);
        }
    }
    return apply_filters("manage_suppliers_input", $form);
}

