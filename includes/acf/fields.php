<?php

if ( function_exists( 'acf_add_local_field_group' ) ):

    acf_add_local_field_group( array(
        'key'                   => 'group_5ed78a304a77c',
        'title'                 => 'Post Settings',
        'fields'                => array(
            array(
                'key'               => 'field_5ed83fb1622fc',
                'label'             => 'Template',
                'name'              => '',
                'type'              => 'tab',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'placement'         => 'top',
                'endpoint'          => 0,
            ),
            array(
                'key'               => 'field_5eda8b3a964cc',
                'label'             => 'Page Title',
                'name'              => 'page_title',
                'type'              => 'text',
                'instructions'      => 'The title for the pages that will be generated when a post request is created. You may use spintax and shortcodes here',
                'required'          => 1,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
                'maxlength'         => '',
            ),
            array(
                'key'               => 'field_5eda8b75964cd',
                'label'             => 'Page Slug',
                'name'              => 'page_slug',
                'type'              => 'text',
                'instructions'      => 'The slug for the pages that will be generated when a post request is created. You may use shortcodes here.',
                'required'          => 1,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
                'maxlength'         => '',
            ),
            array(
                'key'               => 'field_5ed78a4639ee1',
                'label'             => 'Show comments',
                'name'              => 'show_comments',
                'type'              => 'true_false',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'message'           => '',
                'default_value'     => 0,
                'ui'                => 1,
                'ui_on_text'        => '',
                'ui_off_text'       => '',
            ),
            array(
                'key'               => 'field_5ed78a6c39ee2',
                'label'             => 'Use Template Slug',
                'name'              => 'use_template_slug',
                'type'              => 'true_false',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'message'           => '',
                'default_value'     => 1,
                'ui'                => 1,
                'ui_on_text'        => '',
                'ui_off_text'       => '',
            ),
            array(
                'key'               => 'field_5ed90b4afa83a',
                'label'             => 'Spin Templates',
                'name'              => 'spin_templates',
                'type'              => 'repeater',
                'instructions'      => 'If you\'re looking for a way to spin layouts as well as text, then this is the way to go! Just decide which pages you want to spin and we\'ll do the rest. <strong>Please note:</strong> that if you are spinning layouts (templates) then the content from the master template will be ignored.',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'collapsed'         => 'field_5ed90c5bfa83b',
                'min'               => 0,
                'max'               => 0,
                'layout'            => 'table',
                'button_label'      => '',
                'sub_fields'        => array(
                    array(
                        'key'               => 'field_5ed90c5bfa83b',
                        'label'             => 'Template',
                        'name'              => 'template',
                        'type'              => 'post_object',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'post_type'         => array(
                            0 => 'mptemplates',
                        ),
                        'taxonomy'          => '',
                        'allow_null'        => 0,
                        'multiple'          => 0,
                        'return_format'     => 'id',
                        'ui'                => 1,
                    ),
                    array(
                        'key'               => 'field_5ed935316fb56',
                        'label'             => 'Post Name',
                        'name'              => 'post_name',
                        'type'              => 'text',
                        'instructions'      => 'The name of the page once it has been generated. You are able to use shortcodes here, i.e: [city] [state] [county] [region] [country]',
                        'required'          => 1,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'default_value'     => '',
                        'placeholder'       => '',
                        'prepend'           => '',
                        'append'            => '',
                        'maxlength'         => '',
                    ),
                    array(
                        'key'               => 'field_5ed90c9cfa83c',
                        'label'             => 'Enabled',
                        'name'              => 'enabled',
                        'type'              => 'true_false',
                        'instructions'      => '',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'message'           => '',
                        'default_value'     => 0,
                        'ui'                => 1,
                        'ui_on_text'        => '',
                        'ui_off_text'       => '',
                    ),
                ),
            ),
            array(
                'key'               => 'field_5ed83fc6622fd',
                'label'             => 'Job Posting',
                'name'              => '',
                'type'              => 'tab',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'placement'         => 'top',
                'endpoint'          => 0,
            ),
            array(
                'key'               => 'field_5eda343b3da09',
                'label'             => 'Enable Job Schema',
                'name'              => 'enable_job_schema',
                'type'              => 'true_false',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'message'           => '',
                'default_value'     => 1,
                'ui'                => 1,
                'ui_on_text'        => '',
                'ui_off_text'       => '',
            ),
            array(
                'key'               => 'field_5ed84672771bc',
                'label'             => 'Employment Type',
                'name'              => 'job_employment_type',
                'type'              => 'select',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'choices'           => array(
                    'Full-time'  => 'Full-time',
                    'Part-time'  => 'Part-time',
                    'Contract'   => 'Contract',
                    'Temporary'  => 'Temporary',
                    'Seasonal'   => 'Seasonal',
                    'Internship' => 'Internship',
                ),
                'default_value'     => 'Contract',
                'allow_null'        => 0,
                'multiple'          => 0,
                'ui'                => 0,
                'return_format'     => 'value',
                'ajax'              => 0,
                'placeholder'       => '',
            ),
            array(
                'key'               => 'field_5ed8401b622fe',
                'label'             => 'Job Title',
                'name'              => '_ld_job_title',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
                'maxlength'         => '',
            ),
            array(
                'key'               => 'field_5eda5e668e85b',
                'label'             => 'Base Salary',
                'name'              => 'base_salary',
                'type'              => 'group',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'layout'            => 'table',
                'sub_fields'        => array(
                    array(
                        'key'               => 'field_5eda5e848e85c',
                        'label'             => 'Base Salary',
                        'name'              => 'base_salary',
                        'type'              => 'number',
                        'instructions'      => 'The amount of the salary',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'default_value'     => '',
                        'placeholder'       => '10,000',
                        'prepend'           => '',
                        'append'            => '',
                        'min'               => 0,
                        'max'               => '',
                        'step'              => 1,
                    ),
                    array(
                        'key'               => 'field_5eda5f468e85d',
                        'label'             => 'Currency',
                        'name'              => 'currency',
                        'type'              => 'text',
                        'instructions'      => 'For example: USD, GBP, CAD, AUD',
                        'required'          => 0,
                        'conditional_logic' => 0,
                        'wrapper'           => array(
                            'width' => '',
                            'class' => '',
                            'id'    => '',
                        ),
                        'default_value'     => 'USD',
                        'placeholder'       => '',
                        'prepend'           => '',
                        'append'            => '',
                        'maxlength'         => '',
                    ),
                ),
            ),
            array(
                'key'               => 'field_5ed85531965c7',
                'label'             => 'Company Name',
                'name'              => 'company_name',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
                'maxlength'         => '',
            ),
            array(
                'key'               => 'field_5ed8403d622ff',
                'label'             => 'Job Description',
                'name'              => '_ld_job_description',
                'type'              => 'wysiwyg',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'tabs'              => 'all',
                'toolbar'           => 'full',
                'media_upload'      => 1,
                'delay'             => 0,
            ),
            array(
                'key'               => 'field_5ed8463f771ba',
                'label'             => 'Date Posted',
                'name'              => 'job_date_posted',
                'type'              => 'date_picker',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'display_format'    => 'd/m/Y',
                'return_format'     => 'd/m/Y',
                'first_day'         => 1,
            ),
            array(
                'key'               => 'field_5ed84658771bb',
                'label'             => 'Valid Through',
                'name'              => 'job_valid_through_date',
                'type'              => 'date_picker',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => array(
                    array(
                        array(
                            'field'    => 'field_5eda343b3da09',
                            'operator' => '==',
                            'value'    => '1',
                        ),
                    ),
                ),
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'display_format'    => 'd/m/Y',
                'return_format'     => 'd/m/Y',
                'first_day'         => 1,
            ),
            array(
                'key'               => 'field_5ed842688b71c',
                'label'             => 'SEO',
                'name'              => '',
                'type'              => 'tab',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'placement'         => 'top',
                'endpoint'          => 0,
            ),
            array(
                'key'               => 'field_5ed842778b71d',
                'label'             => 'Meta Title',
                'name'              => '_yoast_wpseo_title',
                'type'              => 'text',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'prepend'           => '',
                'append'            => '',
                'maxlength'         => 255,
            ),
            array(
                'key'               => 'field_5ed842a641abb',
                'label'             => 'Meta Description',
                'name'              => '_yoast_wpseo_metadesc',
                'type'              => 'textarea',
                'instructions'      => '',
                'required'          => 0,
                'conditional_logic' => 0,
                'wrapper'           => array(
                    'width' => '',
                    'class' => '',
                    'id'    => '',
                ),
                'default_value'     => '',
                'placeholder'       => '',
                'maxlength'         => '',
                'rows'              => '',
                'new_lines'         => '',
            ),
        ),
        'location'              => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'mptemplates',
                ),
                array(
                    'param'    => 'page_type',
                    'operator' => '==',
                    'value'    => 'top_level',
                ),
            ),
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => '',
        'active'                => true,
        'description'           => '',
    ) );

endif;