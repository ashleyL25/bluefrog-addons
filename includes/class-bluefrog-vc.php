<?php

/**
 * Class Bluefrog_Addons_VC
 */
class Bluefrog_Addons_VC {
	/**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $_instance = null;

	/**
	 * Temporary cached terms variable
	 *
	 * @var array
	 */
	protected $terms = array();

	/**
	 * Main Instance.
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @return Bluefrog_Addons_VC - Main instance.
	 */
	public static function init() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->modify_elements();
		$this->map_shortcodes();

		remove_action( 'admin_bar_menu', array( vc_frontend_editor(), 'adminBarEditLink' ), 1000 );

		if ( function_exists( 'vc_license' ) ) {
			remove_action( 'admin_notices', array( vc_license(), 'adminNoticeLicenseActivation' ) );
		}

		add_filter( 'vc_google_fonts_get_fonts_filter', array( $this, 'add_google_fonts' ) );
	}

	/**
	 * Modify VC element params
	 */
	public function modify_elements() {
		// Add new option to Custom Header element
		vc_add_param( 'vc_custom_heading', array(
			'heading'     => esc_html__( 'Separate URL' ),
			'description' => esc_html__( 'Do not wrap heading text with link tag. Display URL separately' ),
			'type'        => 'checkbox',
			'param_name'  => 'separate_link',
			'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
			'weight'      => 0,
		) );
		vc_add_param( 'vc_custom_heading', array(
			'heading'     => esc_html__( 'Link Arrow' ),
			'description' => esc_html__( 'Add an arrow to the separated link when hover' ),
			'type'        => 'checkbox',
			'param_name'  => 'link_arrow',
			'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
			'weight'      => 0,
			'dependency'  => array(
				'element' => 'separate_link',
				'value'   => 'yes',
			),
		) );
	}

	/**
	 * Register custom shortcodes within Visual Composer interface
	 *
	 * @see http://kb.wpbakery.com/index.php?title=Vc_map
	 */
	public function map_shortcodes() {

		// Post Grid
		vc_map( array(
			'name'        => esc_html__( 'Bluefrog Post Grid' ),
			'description' => esc_html__( 'Display posts in grid' ),
			'base'        => 'bluefrog_post_grid',
			'icon'        => $this->get_icon( 'post-grid.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'description' => esc_html__( 'Number of posts you want to show' ),
					'heading'     => esc_html__( 'Number of posts' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 3,
				),
				array(
					'heading'     => esc_html__( 'Columns' ),
					'description' => esc_html__( 'Display posts in how many columns' ),
					'param_name'  => 'columns',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( '3 Columns' ) => 3,
						esc_html__( '4 Columns' ) => 4,
					),
				),
				array(
					'heading'     => esc_html__( 'Category' ),
					'description' => esc_html__( 'Enter categories name' ),
					'param_name'  => 'category',
					'type'        => 'autocomplete',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'values'   => $this->get_terms( 'category' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Hide Post Meta' ),
					'description' => esc_html__( 'Hide information about date, category' ),
					'type'        => 'checkbox',
					'param_name'  => 'hide_meta',
					'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Countdown
		vc_map( array(
			'name'        => esc_html__( 'Countdown' ),
			'description' => esc_html__( 'Countdown digital clock' ),
			'base'        => 'bluefrog_countdown',
			'icon'        => $this->get_icon( 'countdown.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Date' ),
					'description' => esc_html__( 'Enter the date in format: YYYY/MM/DD' ),
					'admin_label' => true,
					'type'        => 'textfield',
					'param_name'  => 'date',
				),
				array(
					'heading'     => esc_html__( 'Text Align' ),
					'description' => esc_html__( 'Select text alignment' ),
					'param_name'  => 'text_align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Right' )  => 'right',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Button
		vc_map( array(
			'name'        => esc_html__( 'Bluefrog Button' ),
			'description' => esc_html__( 'Button in style' ),
			'base'        => 'bluefrog_button',
			'icon'        => $this->get_icon( 'button.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Text' ),
					'description' => esc_html__( 'Enter button text' ),
					'admin_label' => true,
					'type'        => 'textfield',
					'param_name'  => 'label',
				),
				array(
					'heading'    => esc_html__( 'URL (Link)' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Style' ),
					'description' => esc_html__( 'Select button style' ),
					'param_name'  => 'style',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Normal' )  => 'normal',
						esc_html__( 'Outline' ) => 'outline',
						esc_html__('Full') => 'cover',
						esc_html__('Arrow Button') => 'arrBtn',
						esc_html__( 'Light' )   => 'light',
					),
				),
				array(
					'heading'     => esc_html__('Add Arrow'),
					'description' => esc_html__('Add arrow to button'),
					'param_name'  => 'add_arrow',
					'type'        => 'checkbox',
					'value'       => array(esc_html__('Yes') => 'yes'),
					'weight'      => 0,
					'dependency'  => array(
						'element' => 'style',
						'value'   => 'cover',
					),
				),
				array(
					'heading'     => esc_html__( 'Size' ),
					'description' => esc_html__( 'Select button size' ),
					'param_name'  => 'size',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Normal' ) => 'normal',
						esc_html__( 'Large' )  => 'large',
						esc_html__( 'Small' )  => 'small',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( 'normal', 'outline' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Color' ),
					'description' => esc_html__( 'Select button color' ),
					'param_name'  => 'color',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Dark' )  => 'dark',
						esc_html__( 'White' ) => 'white',
					),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( 'normal', 'outline' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Alignment' ),
					'description' => esc_html__( 'Select button alignment' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Inline' ) => 'inline',
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Right' )  => 'right',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Banner
		vc_map( array(
			'name'        => esc_html__( 'Banner Image' ),
			'description' => esc_html__( 'Banner image for promotion' ),
			'base'        => 'bluefrog_banner',
			'icon'        => $this->get_icon( 'banner.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Banner Image' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Banner description' ),
					'description' => esc_html__( 'A short text display before the banner text' ),
					'type'        => 'textfield',
					'param_name'  => 'desc',
				),
				array(
					'heading'     => esc_html__( 'Banner Text' ),
					'description' => esc_html__( 'Enter the banner text' ),
					'type'        => 'textarea',
					'param_name'  => 'content',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Banner Text Position' ),
					'description' => esc_html__( 'Select text position' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_position',
					'value'       => array(
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Right' )  => 'right',
					),
				),
				array(
					'type'       => 'font_container',
					'param_name' => 'font_container',
					'value'      => '',
					'settings'   => array(
						'fields' => array(
							'font_size',
							'line_height',
							'color',
							'font_size_description'   => esc_html__( 'Enter text font size.' ),
							'line_height_description' => esc_html__( 'Enter text line height.' ),
							'color_description'       => esc_html__( 'Select text color.' ),
						),
					),
				),
				array(
					'heading'     => esc_html__( 'Use theme default font family?' ),
					'description' => esc_html__( 'Use font family from the theme.' ),
					'type'        => 'checkbox',
					'param_name'  => 'use_theme_fonts',
					'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
				),
				array(
					'type'       => 'google_fonts',
					'param_name' => 'google_fonts',
					'value'      => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
					'settings'   => array(
						'fields' => array(
							'font_family_description' => esc_html__( 'Select font family.' ),
							'font_style_description'  => esc_html__( 'Select font styling.' ),
						),
					),
					'dependency' => array(
						'element'            => 'use_theme_fonts',
						'value_not_equal_to' => 'yes',
					),
				),
				array(
					'heading'    => esc_html__( 'Link (URL)' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Type' ),
					'description' => esc_html__( 'Select button type' ),
					'type'        => 'dropdown',
					'param_name'  => 'button_type',
					'value'       => array(
						esc_html__( 'Light Button' )  => 'light',
						esc_html__( 'Normal Button' ) => 'normal',
						esc_html__( 'Arrow Icon' )    => 'arrow_icon',
					),
				),
				array(
					'heading'     => esc_html__( 'Button Text' ),
					'description' => esc_html__( 'Enter the text for banner button' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
					'dependency'  => array(
						'element' => 'button_type',
						'value'   => array( 'light', 'normal' ),
					),
				),
				array(
					'heading'     => esc_html__( 'Button Visibility' ),
					'description' => esc_html__( 'Select button visibility' ),
					'type'        => 'dropdown',
					'param_name'  => 'button_visibility',
					'value'       => array(
						esc_html__( 'Always visible' ) => 'always',
						esc_html__( 'When hover' )     => 'hover',
						esc_html__( 'Hidden' )         => 'hidden',
					),
				),
				array(
					'heading'     => esc_html__( 'Banner Color Scheme' ),
					'description' => esc_html__( 'Select color scheme for description, button color' ),
					'type'        => 'dropdown',
					'param_name'  => 'scheme',
					'value'       => array(
						esc_html__( 'Dark' )  => 'dark',
						esc_html__( 'Light' ) => 'light',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => esc_html__( 'CSS box' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options' ),
				),
			),
		) );

		// Banner 2
		vc_map( array(
			'name'        => esc_html__( 'Banner Image 2' ),
			'description' => esc_html__( 'Simple banner that supports multiple buttons' ),
			'base'        => 'bluefrog_banner2',
			'icon'        => $this->get_icon( 'banner2.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Banner Image' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Buttons' ),
					'description' => esc_html__( 'Enter link and label for buttons.' ),
					'type'        => 'param_group',
					'param_name'  => 'buttons',
					'params'      => array(
						array(
							'heading'    => esc_html__( 'Button Text' ),
							'type'       => 'textfield',
							'param_name' => 'text',
						),
						array(
							'heading'    => esc_html__( 'Button Link' ),
							'type'       => 'vc_link',
							'param_name' => 'link',
						),
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Banner 3
		vc_map( array(
			'name'        => esc_html__( 'Banner Image 3' ),
			'description' => esc_html__( 'Simple banner with text at bottom' ),
			'base'        => 'bluefrog_banner3',
			'icon'        => $this->get_icon( 'banner3.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Banner Image' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => '',
				),
				array(
					'heading'     => esc_html__( 'Banner Text' ),
					'description' => esc_html__( 'Enter banner text' ),
					'type'        => 'textfield',
					'param_name'  => 'text',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Banner Text Position' ),
					'description' => esc_html__( 'Select text position' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_align',
					'value'       => array(
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Right' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Text Color Scheme' ),
					'description' => esc_html__( 'Select color scheme for banner content' ),
					'type'        => 'dropdown',
					'param_name'  => 'scheme',
					'value'       => array(
						esc_html__( 'Dark' )  => 'dark',
						esc_html__( 'Light' ) => 'light',
					),
					'std' => 'dark'
				),
				array(
					'heading'    => esc_html__( 'Link (URL)' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Text' ),
					'description' => esc_html__( 'Enter the text for banner button' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Banner 4
		vc_map( array(
			'name'        => esc_html__( 'Banner Image 4' ),
			'description' => esc_html__( 'Simple banner image with text' ),
			'base'        => 'bluefrog_banner4',
			'icon'        => $this->get_icon( 'banner4.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Banner Image' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size. Example: "thumbnail", "medium", "large", "full" or other sizes defined by current theme. Alternatively enter image size in pixels: 200x100 (Width x Height). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => 'full',
				),
				array(
					'heading'    => esc_html__( 'Link (URL)' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => esc_html__( 'Banner Content' ),
					'type'       => 'textarea_html',
					'param_name' => 'content',
					'group'      => esc_html__( 'Text' ),
				),
				array(
					'heading'     => esc_html__( 'Button Text' ),
					'description' => esc_html__( 'Enter the text for banner button' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
					'group'       => esc_html__( 'Text' ),
				),
				array(
					'heading'     => esc_html__( 'Button Style' ),
					'description' => esc_html__( 'Select button style' ),
					'type'        => 'dropdown',
					'param_name'  => 'button_style',
					'group'       => esc_html__( 'Text' ),
					'std'         => 'light',
					'value'       => array(
						esc_html__( 'Normal' )  => 'normal',
						esc_html__( 'Outline' ) => 'outline',
						esc_html__( 'Light' ) => 'light',
					),
				),
				array(
					'heading'     => esc_html__( 'Text Color Scheme' ),
					'description' => esc_html__( 'Select color scheme for banner content' ),
					'type'        => 'dropdown',
					'param_name'  => 'scheme',
					'group'       => esc_html__( 'Text' ),
					'value'       => array(
						esc_html__( 'Dark' )  => 'dark',
						esc_html__( 'Light' ) => 'light',
					),
				),
				array(
					'heading'     => esc_html__( 'Content Horizontal Alignment' ),
					'description' => esc_html__( 'Horizontal alignment of banner text' ),
					'type'        => 'dropdown',
					'param_name'  => 'align_horizontal',
					'group'       => esc_html__( 'Text' ),
					'value'       => array(
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Right' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Content Vertical Alignment' ),
					'description' => esc_html__( 'Vertical alignment of banner text' ),
					'type'        => 'dropdown',
					'param_name'  => 'align_vertical',
					'group'       => esc_html__( 'Text' ),
					'value'       => array(
						esc_html__( 'Top' )    => 'top',
						esc_html__( 'Middle' ) => 'middle',
						esc_html__( 'Bottom' ) => 'bottom',
					),
				),
			),
		) );

		// Category Banner
		vc_map( array(
			'name'        => esc_html__( 'Category Banner' ),
			'description' => esc_html__( 'Banner image with special style' ),
			'base'        => 'bluefrog_category_banner',
			'icon'        => $this->get_icon( 'category-banner.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Banner Image' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image Position' ),
					'description' => esc_html__( 'Select image position' ),
					'type'        => 'dropdown',
					'param_name'  => 'image_position',
					'value'       => array(
						esc_html__( 'Left' )         => 'left',
						esc_html__( 'Right' )        => 'right',
						esc_html__( 'Top' )          => 'top',
						esc_html__( 'Bottom' )       => 'bottom',
						esc_html__( 'Top Left' )     => 'top-left',
						esc_html__( 'Top Right' )    => 'top-right',
						esc_html__( 'Bottom Left' )  => 'bottom-left',
						esc_html__( 'Bottom Right' ) => 'bottom-right',
					),
				),
				array(
					'heading'     => esc_html__( 'Title' ),
					'description' => esc_html__( 'The banner title' ),
					'type'        => 'textfield',
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Description' ),
					'description' => esc_html__( 'The banner description' ),
					'type'        => 'textarea',
					'param_name'  => 'content',
				),
				array(
					'heading'     => esc_html__( 'Text Position' ),
					'description' => esc_html__( 'Select the position for title and description' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_position',
					'value'       => array(
						esc_html__( 'Top Left' )     => 'top-left',
						esc_html__( 'Top Right' )    => 'top-right',
						esc_html__( 'Middle Left' )  => 'middle-left',
						esc_html__( 'Middle Right' ) => 'middle-right',
						esc_html__( 'Bottom Left' )  => 'bottom-left',
						esc_html__( 'Bottom Right' ) => 'bottom-right',
					),
				),
				array(
					'heading'    => esc_html__( 'Link (URL)' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Text' ),
					'description' => esc_html__( 'Enter the text for banner button' ),
					'type'        => 'textfield',
					'param_name'  => 'button_text',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => __( 'CSS box' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options' ),
				),
			),
		) );

		// Banner Grid 4
		// vc_map( array(
		// 	'name'                    => esc_html__( 'Banner Grid 4' ),
		// 	'description'             => esc_html__( 'Arrange 4 banners per row with unusual structure.' ),
		// 	'base'                    => 'bluefrog_banner_grid_4',
		// 	'icon'                    => $this->get_icon( 'banner-grid-4.png' ),
		// 	'category'                => esc_html__( 'Bluefrog' ),
		// 	'js_view'                 => 'VcColumnView',
		// 	'content_element'         => true,
		// 	'show_settings_on_create' => false,
		// 	'as_parent'               => array( 'only' => 'bluefrog_banner,bluefrog_banner2,bluefrog_banner3' ),
		// 	'params'                  => array(
		// 		array(
		// 			'heading'     => esc_html__( 'Reverse Order' ),
		// 			'description' => esc_html__( 'Reverse the order of banners inside this grid' ),
		// 			'param_name'  => 'reverse',
		// 			'type'        => 'checkbox',
		// 			'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
		// 		),
		// 		array(
		// 			'heading'     => esc_html__( 'Extra class name' ),
		// 			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
		// 			'param_name'  => 'el_class',
		// 			'type'        => 'textfield',
		// 		),
		// 	),
		// ) );

		// Banner Grid 5
		// vc_map( array(
		// 	'name'                    => esc_html__( 'Banner Grid 5' ),
		// 	'description'             => esc_html__( 'Arrange 5 banners in 3 columns.' ),
		// 	'base'                    => 'bluefrog_banner_grid_5',
		// 	'icon'                    => $this->get_icon( 'banner-grid-5.png' ),
		// 	'category'                => esc_html__( 'Bluefrog' ),
		// 	'js_view'                 => 'VcColumnView',
		// 	'content_element'         => true,
		// 	'show_settings_on_create' => false,
		// 	'as_parent'               => array( 'only' => 'bluefrog_banner,bluefrog_banner2,bluefrog_banner3' ),
		// 	'params'                  => array(
		// 		array(
		// 			'heading'     => esc_html__( 'Extra class name' ),
		// 			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
		// 			'param_name'  => 'el_class',
		// 			'type'        => 'textfield',
		// 		),
		// 	),
		// ) );

		// Banner Grid 5 v2
		// vc_map( array(
		// 	'name'                    => esc_html__( 'Banner Grid 5 (v2)' ),
		// 	'description'             => esc_html__( 'Arrange 5 banners in 2 rows.' ),
		// 	'base'                    => 'bluefrog_banner_grid_5_2',
		// 	'icon'                    => $this->get_icon( 'banner-grid-5-v2.png' ),
		// 	'category'                => esc_html__( 'Bluefrog' ),
		// 	'js_view'                 => 'VcColumnView',
		// 	'content_element'         => true,
		// 	'show_settings_on_create' => false,
		// 	'as_parent'               => array( 'only' => 'bluefrog_banner,bluefrog_banner2,bluefrog_banner3,bluefrog_banner4' ),
		// 	'params'                  => array(
		// 		array(
		// 			'heading'     => esc_html__( 'Extra class name' ),
		// 			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
		// 			'param_name'  => 'el_class',
		// 			'type'        => 'textfield',
		// 		),
		// 	),
		// ) );

		// Banner Grid 6
		// vc_map( array(
		// 	'name'                    => esc_html__( 'Banner Grid 6' ),
		// 	'description'             => esc_html__( 'Arrange 6 banners in 4 columns.' ),
		// 	'base'                    => 'bluefrog_banner_grid_6',
		// 	'icon'                    => $this->get_icon( 'banner-grid-6.png' ),
		// 	'category'                => esc_html__( 'Bluefrog' ),
		// 	'js_view'                 => 'VcColumnView',
		// 	'content_element'         => true,
		// 	'show_settings_on_create' => false,
		// 	'as_parent'               => array( 'only' => 'bluefrog_banner,bluefrog_banner2,bluefrog_banner3' ),
		// 	'params'                  => array(
		// 		array(
		// 			'heading'     => esc_html__( 'Reverse Order' ),
		// 			'description' => esc_html__( 'Reverse the order of banners inside this grid' ),
		// 			'param_name'  => 'reverse',
		// 			'type'        => 'checkbox',
		// 			'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
		// 		),
		// 		array(
		// 			'heading'     => esc_html__( 'Extra class name' ),
		// 			'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
		// 			'param_name'  => 'el_class',
		// 			'type'        => 'textfield',
		// 		),
		// 	),
		// ) );

		// Circle Chart
		vc_map( array(
			'name'        => esc_html__( 'Circle Chart' ),
			'description' => esc_html__( 'Circle chart with animation' ),
			'base'        => 'bluefrog_chart',
			'icon'        => $this->get_icon( 'chart.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Value' ),
					'description' => esc_html__( 'Enter the chart value in percentage. Minimum 0 and maximum 100.' ),
					'type'        => 'textfield',
					'param_name'  => 'value',
					'value'       => 100,
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Circle Size' ),
					'description' => esc_html__( 'Width of the circle' ),
					'type'        => 'textfield',
					'param_name'  => 'size',
					'value'       => 200,
				),
				array(
					'heading'     => esc_html__( 'Circle thickness' ),
					'description' => esc_html__( 'Width of the arc' ),
					'type'        => 'textfield',
					'param_name'  => 'thickness',
					'value'       => 8,
				),
				array(
					'heading'     => esc_html__( 'Color' ),
					'description' => esc_html__( 'Pick color for the circle' ),
					'type'        => 'colorpicker',
					'param_name'  => 'color',
					'value'       => '#6dcff6',
				),
				array(
					'heading'     => esc_html__( 'Label Source' ),
					'description' => esc_html__( 'Chart label source' ),
					'param_name'  => 'label_source',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Auto' )   => 'auto',
						esc_html__( 'Custom' ) => 'custom',
					),
				),
				array(
					'heading'     => esc_html__( 'Custom label' ),
					'description' => esc_html__( 'Text label for the chart' ),
					'param_name'  => 'label',
					'type'        => 'textfield',
					'dependency'  => array(
						'element' => 'label_source',
						'value'   => 'custom',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Message Box
		vc_map( array(
			'name'        => esc_html__( 'Bluefrog Message Box' ),
			'description' => esc_html__( 'Notification box with close button' ),
			'base'        => 'bluefrog_message_box',
			'icon'        => $this->get_icon( 'message-box.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'          => esc_html__( 'Type' ),
					'description'      => esc_html__( 'Select message box type' ),
					'edit_field_class' => 'vc_col-xs-12 vc_message-type',
					'type'             => 'dropdown',
					'param_name'       => 'type',
					'default'          => 'success',
					'admin_label'      => true,
					'value'            => array(
						esc_html__( 'Success' )       => 'success',
						esc_html__( 'Informational' ) => 'info',
						esc_html__( 'Error' )         => 'danger',
						esc_html__( 'Warning' )       => 'warning',
					),
				),
				array(
					'heading'    => esc_html__( 'Message Text' ),
					'type'       => 'textarea_html',
					'param_name' => 'content',
					'holder'     => 'div',
				),
				array(
					'heading'     => esc_html__( 'Closeable' ),
					'description' => esc_html__( 'Display close button for this box' ),
					'type'        => 'checkbox',
					'param_name'  => 'closeable',
					'value'       => array(
						esc_html__( 'Yes' ) => true,
					),
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name' ),
					'param_name'  => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
				),
			),
		) );

		// Icon Box
		vc_map( array(
			'name'        => esc_html__( 'Icon Box' ),
			'description' => esc_html__( 'Information box with icon' ),
			'base'        => 'bluefrog_icon_box',
			'icon'        => $this->get_icon( 'icon-box.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Icon library' ),
					'description' => esc_html__( 'Select icon library.' ),
					'param_name'  => 'icon_type',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Font Awesome' )   => 'fontawesome',
						esc_html__( 'Open Iconic' )    => 'openiconic',
						esc_html__( 'Typicons' )       => 'typicons',
						esc_html__( 'Entypo' )         => 'entypo',
						esc_html__( 'Linecons' )       => 'linecons',
						esc_html__( 'Mono Social' )    => 'monosocial',
						esc_html__( 'Material' )       => 'material',
						esc_html__( 'Custom Image' )   => 'image',
						esc_html__( 'External Image' ) => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_fontawesome',
					'value'       => 'fa fa-adjust',
					'settings'    => array(
						'emptyIcon'    => false,
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_openiconic',
					'value'       => 'vc-oi vc-oi-dial',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'openiconic',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'openiconic',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_typicons',
					'value'       => 'typcn typcn-adjust-brightness',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'typicons',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'typicons',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_entypo',
					'value'       => 'entypo-icon entypo-icon-note',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'entypo',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'entypo',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_linecons',
					'value'       => 'vc_li vc_li-heart',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'linecons',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'linecons',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_monosocial',
					'value'       => 'vc-mono vc-mono-fivehundredpx',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'monosocial',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'monosocial',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon' ),
					'description' => esc_html__( 'Select icon from library.' ),
					'type'        => 'iconpicker',
					'param_name'  => 'icon_material',
					'value'       => 'vc-material vc-material-cake',
					'settings'    => array(
						'emptyIcon'    => false,
						'type'         => 'material',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'material',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon Image' ),
					'description' => esc_html__( 'Upload icon image' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
					'value'       => '',
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon Image URL' ),
					'description' => esc_html__( 'Enter image URL' ),
					'type'        => 'textfield',
					'param_name'  => 'image_url',
					'value'       => '',
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Icon Style' ),
					'description' => esc_html__( 'Select icon style' ),
					'param_name'  => 'style',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Normal' ) => 'normal',
						esc_html__( 'Circle' ) => 'circle',
						esc_html__( 'Round' )  => 'round',
						esc_html__( 'None' )  => 'none',
					),
				),
				array(
					'heading'     => esc_html__( 'Title' ),
					'description' => esc_html__( 'The box title' ),
					'admin_label' => true,
					'param_name'  => 'title',
					'type'        => 'textfield',
					'value'       => esc_html__( 'I am Icon Box' ),
				),
				array(
					'heading'     => esc_html__( 'Content' ),
					'description' => esc_html__( 'The box title' ),
					'holder'      => 'div',
					'param_name'  => 'content',
					'type'        => 'textarea_html',
					'value'       => esc_html__( 'I am icon box. Click edit button to change this text.' ),
				),
				array(
                    'type' => 'dropdown',
                    'heading' => __('Text Align', 'blue-frog'),
                    'param_name' => 'text_align',
                    'value' => array(
                        __('Left', 'blue-frog') => 'left',
                        __('Center', 'blue-frog') => 'center',
                        __('Right', 'blue-frog') => 'right',
                    ),
                    'group' => 'Styles',
                ),
				array(
                    'type' => 'dropdown',
                    'heading' => __('Text Color', 'blue-frog'),
                    'param_name' => 'text_color',
                    'value' => array(
                        __('Primary Color', 'blue-frog') => 'primary-color',
                        __('Secondary Color', 'blue-frog') => 'secondary-color',
                        __('Accent Color', 'blue-frog') => 'accent-color',
						__('Tertiary Color', 'blue-frog') => 'tertiary-color',
                        __('Section Background', 'blue-frog') => 'section-bg',
                        __('White', 'blue-frog') => 'white',
                    ),
                    'group' => 'Styles',
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Background Color', 'blue-frog'),
                    'param_name' => 'bg_gradient',
                    'value' => array(
                        __('Primary Color', 'blue-frog') => 'primary-color',
                        __('Secondary Color', 'blue-frog') => 'secondary-color',
                        __('Accent Color', 'blue-frog') => 'accent-color',
						__('Tertiary Color', 'blue-frog') => 'tertiary-color',
                        __('Section Background', 'blue-frog') => 'section-bg',
                        __('White', 'blue-frog') => 'white',
                    ),
                    'group' => 'Styles',
                ),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Pricing Table
		vc_map( array(
			'name'        => esc_html__( 'Pricing Table' ),
			'description' => esc_html__( 'Eye catching pricing table' ),
			'base'        => 'bluefrog_pricing_table',
			'icon'        => $this->get_icon( 'pricing-table.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Plan Name' ),
					'admin_label' => true,
					'param_name'  => 'name',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Price' ),
					'description' => esc_html__( 'Plan pricing' ),
					'param_name'  => 'price',
					'type'        => 'textfield',
				),
				array(
					'heading'     => esc_html__( 'Currency' ),
					'description' => esc_html__( 'Price currency' ),
					'param_name'  => 'currency',
					'type'        => 'textfield',
					'value'       => '$',
				),
				array(
					'heading'     => esc_html__( 'Recurrence' ),
					'description' => esc_html__( 'Recurring payment unit' ),
					'param_name'  => 'recurrence',
					'type'        => 'textfield',
					'value'       => esc_html__( 'Per Month' ),
				),
				array(
					'heading'     => esc_html__( 'Features' ),
					'description' => esc_html__( 'Feature list of this plan. Click to arrow button to edit.' ),
					'param_name'  => 'features',
					'type'        => 'param_group',
					'params'      => array(
						array(
							'heading'    => esc_html__( 'Feature name' ),
							'param_name' => 'name',
							'type'       => 'textfield',
						),
						array(
							'heading'    => esc_html__( 'Feature value' ),
							'param_name' => 'value',
							'type'       => 'textfield',
						),
					),
				),
				array(
					'heading'    => esc_html__( 'Button Text' ),
					'param_name' => 'button_text',
					'type'       => 'textfield',
					'value'      => esc_html__( 'Get Started' ),
				),
				array(
					'heading'    => esc_html__( 'Button Link' ),
					'param_name' => 'button_link',
					'type'       => 'vc_link',
					'value'      => esc_html__( 'Get Started' ),
				),
				array(
					'heading'     => esc_html__( 'Table color' ),
					'description' => esc_html__( 'Pick color scheme for this table. It will be applied to table header and button.' ),
					'param_name'  => 'color',
					'type'        => 'colorpicker',
					'value'       => '#6dcff6',
				),
				vc_map_add_css_animation(),
				array(
					'type'        => 'textfield',
					'heading'     => esc_html__( 'Extra class name' ),
					'param_name'  => 'el_class',
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
				),
			),
		) );

		// Google Map
		vc_map( array(
			'name'        => esc_html__( 'Bluefrog Maps' ),
			'description' => esc_html__( 'Google maps in style' ),
			'base'        => 'bluefrog_map',
			'icon'        => $this->get_icon( 'map.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'API Key' ),
					'description' => esc_html__( 'Google requires an API key to work.' ),
					'type'        => 'textfield',
					'param_name'  => 'api_key',
				),
				array(
					'heading'     => esc_html__( 'Address' ),
					'description' => esc_html__( 'Enter address for map marker. If this option does not work correctly, use the Latitude and Longitude options bellow.' ),
					'type'        => 'textfield',
					'param_name'  => 'address',
					'admin_label' => true,
				),
				array(
					'heading'          => esc_html__( 'Latitude' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-xs-6',
					'param_name'       => 'lat',
					'admin_label'      => true,
				),
				array(
					'heading'          => esc_html__( 'Longitude' ),
					'type'             => 'textfield',
					'param_name'       => 'lng',
					'edit_field_class' => 'vc_col-xs-6',
					'admin_label'      => true,
				),
				array(
					'heading'     => esc_html__( 'Marker' ),
					'description' => esc_html__( 'Upload custom marker icon or leave this to use default marker.' ),
					'param_name'  => 'marker',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Width' ),
					'description' => esc_html__( 'Map width in pixel or percentage.' ),
					'param_name'  => 'width',
					'type'        => 'textfield',
					'value'       => '100%',
				),
				array(
					'heading'     => esc_html__( 'Height' ),
					'description' => esc_html__( 'Map height in pixel.' ),
					'type'        => 'textfield',
					'param_name'  => 'height',
					'value'       => '625px',
				),
				array(
					'heading'     => esc_html__( 'Zoom' ),
					'description' => esc_html__( 'Enter zoom level. The value is between 1 and 20.' ),
					'param_name'  => 'zoom',
					'type'        => 'textfield',
					'value'       => '15',
				),
				array(
					'heading'          => esc_html__( 'Color' ),
					'description'      => esc_html__( 'Select map color style' ),
					'edit_field_class' => 'vc_col-xs-12 vc_btn3-colored-dropdown vc_colored-dropdown',
					'param_name'       => 'color',
					'type'             => 'dropdown',
					'value'            => array(
						esc_html__( 'Default' )       => '',
						esc_html__( 'Grey' )          => 'grey',
						esc_html__( 'Classic Black' ) => 'inverse',
						esc_html__( 'Vista Blue' )    => 'vista-blue',
					),
				),
				array(
					'heading'     => esc_html__( 'Content' ),
					'description' => esc_html__( 'Enter content of info window.' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
					'holder'      => 'div',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Open Street Map
		vc_map( array(
			'name'        => esc_html__( 'Bluefrog Maps 2' ),
			'description' => esc_html__( 'Open Street Map in style' ),
			'base'        => 'bluefrog_map2',
			'icon'        => $this->get_icon( 'map.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Address' ),
					'description' => esc_html__( 'Enter address for map marker.' ),
					'type'        => 'textfield',
					'param_name'  => 'address',
					'admin_label' => true,
				),
				array(
					'heading'          => esc_html__( 'Latitude' ),
					'type'             => 'textfield',
					'edit_field_class' => 'vc_col-xs-6',
					'param_name'       => 'lat',
					'admin_label'      => true,
				),
				array(
					'heading'          => esc_html__( 'Longitude' ),
					'type'             => 'textfield',
					'param_name'       => 'lng',
					'edit_field_class' => 'vc_col-xs-6',
					'admin_label'      => true,
				),
				array(
					'heading'     => esc_html__( 'Height' ),
					'description' => esc_html__( 'Map height in pixel.' ),
					'type'        => 'textfield',
					'param_name'  => 'height',
					'value'       => '625px',
				),
				array(
					'heading'     => esc_html__( 'Zoom' ),
					'description' => esc_html__( 'Enter zoom level. The value is between 1 and 20.' ),
					'param_name'  => 'zoom',
					'type'        => 'textfield',
					'value'       => '15',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Testimonial
		vc_map( array(
			'name'        => esc_html__( 'Testimonial' ),
			'description' => esc_html__( 'Written review from a satisfied customer' ),
			'base'        => 'bluefrog_testimonial',
			'icon'        => $this->get_icon( 'testimonial.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Photo' ),
					'description' => esc_html__( 'Author photo or avatar. Recommend 160x160 in dimension.' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
				),
				array(
					'heading'     => esc_html__( 'Name' ),
					'description' => esc_html__( 'Enter full name of the author' ),
					'type'        => 'textfield',
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Company' ),
					'description' => esc_html__( 'Enter company name of author' ),
					'param_name'  => 'company',
					'type'        => 'textfield',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Alignment' ),
					'description' => esc_html__( 'Select testimonial alignment' ),
					'param_name'  => 'align',
					'type'        => 'dropdown',
					'value'       => array(
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Right' )  => 'right',
					),
				),
				array(
					'heading'     => esc_html__( 'Content' ),
					'description' => esc_html__( 'Testimonial content' ),
					'type'        => 'textarea_html',
					'param_name'  => 'content',
					'holder'      => 'div',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Partners
		vc_map( array(
			'name'        => esc_html__( 'Partner Logos' ),
			'description' => esc_html__( 'Show list of partner logo' ),
			'base'        => 'bluefrog_partners',
			'icon'        => $this->get_icon( 'partners.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image source' ),
					'description' => esc_html__( 'Select images source' ),
					'type'        => 'dropdown',
					'param_name'  => 'source',
					'value'       => array(
						esc_html__( 'Media library' )  => 'media_library',
						esc_html__( 'External Links' ) => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Images' ),
					'description' => esc_html__( 'Select images from media library' ),
					'type'        => 'attach_images',
					'param_name'  => 'images',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading'     => esc_html__( 'External links' ),
					'description' => esc_html__( 'Enter external links for partner logos (Note: divide links with linebreaks (Enter)).' ),
					'type'        => 'exploded_textarea_safe',
					'param_name'  => 'custom_srcs',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size in pixels. Example: 200x100 (Width x Height).' ),
					'type'        => 'textfield',
					'param_name'  => 'external_img_size',
					'dependency'  => array(
						'element' => 'source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Custom links' ),
					'description' => esc_html__( 'Enter links for each image here. Divide links with linebreaks (Enter).' ),
					'type'        => 'exploded_textarea_safe',
					'param_name'  => 'custom_links',
				),
				array(
					'heading'     => esc_html__( 'Custom link target' ),
					'description' => esc_html__( 'Select where to open custom links.' ),
					'type'        => 'dropdown',
					'param_name'  => 'custom_links_target',
					'value'       => array(
						esc_html__( 'Same window' ) => '_self',
						esc_html__( 'New window' )  => '_blank',
					),
				),
				array(
					'heading'     => esc_html__( 'Layout' ),
					'description' => esc_html__( 'Select the layout images source' ),
					'type'        => 'dropdown',
					'param_name'  => 'layout',
					'value'       => array(
						esc_html__( 'Bordered' ) => 'bordered',
						esc_html__( 'Plain' )    => 'plain',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Contact Box
		vc_map( array(
			'name'        => esc_html__( 'Contact Box' ),
			'description' => esc_html__( 'Contact information' ),
			'base'        => 'bluefrog_contact_box',
			'icon'        => $this->get_icon( 'contact.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Address' ),
					'description' => esc_html__( 'The office address' ),
					'type'        => 'textfield',
					'param_name'  => 'address',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Phone' ),
					'description' => esc_html__( 'The phone number' ),
					'type'        => 'textfield',
					'param_name'  => 'phone',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Fax' ),
					'description' => esc_html__( 'The fax number' ),
					'type'        => 'textfield',
					'param_name'  => 'fax',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Email' ),
					'description' => esc_html__( 'The email adress' ),
					'type'        => 'textfield',
					'param_name'  => 'email',
					'holder'      => 'p',
				),
				array(
					'heading'     => esc_html__( 'Website' ),
					'description' => esc_html__( 'The phone number' ),
					'type'        => 'textfield',
					'param_name'  => 'website',
					'holder'      => 'p',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// Info List
		vc_map( array(
			'name'        => esc_html__( 'Info List' ),
			'description' => esc_html__( 'List of information' ),
			'base'        => 'bluefrog_info_list',
			'icon'        => $this->get_icon( 'info-list.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Information' ),
					'description' => esc_html__( 'Enter information' ),
					'type'        => 'param_group',
					'param_name'  => 'info',
					'value'       => urlencode( json_encode( array(
						array(
							'icon'  => 'fas fa-map-marker-alt',
							'label' => esc_html__( 'Address' ),
							'value' => '9606 North MoPac Expressway',
						),
						array(
							'icon'  => 'fas fa-phone-alt',
							'label' => esc_html__( 'Phone' ),
							'value' => '+1 248-785-8545',
						),
						array(
							'icon'  => 'fas fa-fax',
							'label' => esc_html__( 'Fax' ),
							'value' => '123123123',
						),
						array(
							'icon'  => 'far fa-envelope',
							'label' => esc_html__( 'Email' ),
							'value' => 'bluefrog@uix.store',
						),
						array(
							'icon'  => 'fas fa-globe',
							'label' => esc_html__( 'Website' ),
							'value' => 'http://uix.store',
						),
					) ) ),
					'params'      => array(
						array(
							'type'       => 'iconpicker',
							'heading'    => esc_html__( 'Icon' ),
							'param_name' => 'icon',
							'settings'   => array(
								'emptyIcon'    => false,
								'iconsPerPage' => 4000,
							),
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Label' ),
							'param_name'  => 'label',
							'admin_label' => true,
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Value' ),
							'param_name'  => 'value',
							'admin_label' => true,
						),
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );

		// FAQ
		vc_map( array(
			'name'        => esc_html__( 'FAQ' ),
			'description' => esc_html__( 'Question and answer toggle' ),
			'base'        => 'bluefrog_faq',
			'icon'        => $this->get_icon( 'faq.png' ),
			'category'    => esc_html__( 'Bluefrog', 'bluefrog' ),
			'js_view'     => 'VcToggleView',
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Question' ),
					'description' => esc_html__( 'Enter title of toggle block.' ),
					'type'        => 'textfield',
					'holder'      => 'h4',
					'class'       => 'vc_toggle_title wpb_element_title',
					'param_name'  => 'title',
					'value'       => esc_html__( 'Question content goes here' ),
				),
				array(
					'heading'     => esc_html__( 'Answer' ),
					'description' => esc_html__( 'Toggle block content.' ),
					'type'        => 'textarea_html',
					'holder'      => 'div',
					'class'       => 'vc_toggle_content',
					'param_name'  => 'content',
					'value'       => esc_html__( 'Answer content goes here, click edit button to change this text.' ),
				),
				array(
					'heading'     => esc_html__( 'Default state' ),
					'description' => esc_html__( 'Select "Open" if you want toggle to be open by default.' ),
					'type'        => 'dropdown',
					'param_name'  => 'open',
					'value'       => array(
						esc_html__( 'Closed' ) => 'false',
						esc_html__( 'Open' )   => 'true',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Team Member
		vc_map( array(
			'name'        => esc_html__( 'Team Member' ),
			'description' => esc_html__( 'Single team member information' ),
			'base'        => 'bluefrog_team_member',
			'icon'        => $this->get_icon( 'member.png' ),
			'category'    => esc_html__( 'Bluefrog', 'bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Member photo' ),
					'param_name'  => 'image',
					'type'        => 'attach_image',
				),
				array(
					'heading'     => esc_html__( 'Image Size' ),
					'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => 'full',
				),
				array(
					'heading'     => esc_html__( 'Full Name' ),
					'description' => esc_html__( 'Member name' ),
					'type'        => 'textfield',
					'param_name'  => 'name',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Job' ),
					'description' => esc_html__( 'The job/position name of member in your team' ),
					'param_name'  => 'job',
					'type'        => 'textfield',
					'admin_label' => true,
				),
				array(
					'heading'    => esc_html__( 'Facebook' ),
					'type'       => 'textfield',
					'param_name' => 'facebook',
				),
				array(
					'heading'    => esc_html__( 'Twitter' ),
					'type'       => 'textfield',
					'param_name' => 'twitter',
				),
				array(
					'heading'    => esc_html__( 'Pinterest' ),
					'type'       => 'textfield',
					'param_name' => 'pinterest',
				),
				array(
					'heading'    => esc_html__( 'Linkedin' ),
					'type'       => 'textfield',
					'param_name' => 'linkedin',
				),
				array(
					'heading'    => esc_html__( 'Youtube' ),
					'type'       => 'textfield',
					'param_name' => 'youtube',
				),
				array(
					'heading'    => esc_html__( 'Instagram' ),
					'type'       => 'textfield',
					'param_name' => 'instagram',
				),
				array(
					'heading'    => esc_html__( 'Email' ),
					'type'       => 'textfield',
					'param_name' => 'email',
				),
				array(
					'heading'    => esc_html__('Phone'),
					'type'       => 'textfield',
					'param_name' => 'phone',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
				),
			),
		) );

		// Subscribe Box.
		$forms = get_posts( array( 'post_type' => 'mc4wp-form', 'numberposts' => -1 ));

		if ( $forms ) {
			$options = array();

			foreach( $forms as $form ) {
				$options[$form->post_title . " - ID: $form->ID"] = $form->ID;
			}

			vc_map(array(
				'name' => esc_html__('Subscribe Box'),
				'description' => esc_html__('MailChimp subscribe form'),
				'base' => 'bluefrog_subscribe_box',
				'icon' => $this->get_icon('mail.png'),
				'category' => esc_html__( 'Bluefrog', 'bluefrog' ),
				'params' => array(
					array(
						'heading' => esc_html__( 'Title' ),
						'admin_label' => true,
						'type' => 'textfield',
						'param_name' => 'title',
					),
					array(
						'heading' => esc_html__( 'Description' ),
						'admin_label' => true,
						'type' => 'textarea',
						'param_name' => 'content',
					),
					array(
						'heading' => esc_html__( 'Form' ),
						'description' => esc_html__( 'Select the MailChimp form' ),
						'param_name' => 'form_id',
						'type' => 'dropdown',
						'value' => $options,
					),
					array(
						'heading' => esc_html__( 'Form Style' ),
						'description' => esc_html__( 'Select the style for this form' ),
						'param_name' => 'form_style',
						'type' => 'dropdown',
						'std' => 'default',
						'value' => array(
							esc_html__( 'Default' ) => 'default',
							esc_html__( 'Inline' ) => 'inline',
						),
					),
					vc_map_add_css_animation(),
					array(
						'heading' => esc_html__( 'Extra class name' ),
						'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
						'param_name' => 'el_class',
						'type' => 'textfield',
						'value' => '',
					),
					array(
						'type' => 'css_editor',
						'heading' => esc_html__( 'CSS box' ),
						'param_name' => 'css',
						'group' => esc_html__( 'Design Options' ),
					),
				),
			));
		}

		// Banner Simple
		vc_map( array(
			'name'        => esc_html__( 'Simple Banner' ),
			'description' => esc_html__( 'Simple banner image with text bellow' ),
			'base'        => 'bluefrog_banner_simple',
			'icon'        => $this->get_icon( 'banner.png' ),
			'category'    => esc_html__( 'Bluefrog', 'bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Image Source' ),
					'description' => esc_html__( 'Select image source.' ),
					'param_name'  => 'image_source',
					'type'        => 'dropdown',
					'std'         => 'media_library',
					'value'       => array(
						esc_html__( 'Media library' )  => 'media_library',
						esc_html__( 'External Links' ) => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Image' ),
					'description' => esc_html__( 'Select image from media library' ),
					'type'        => 'attach_image',
					'param_name'  => 'image',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading'     => esc_html__( 'Image size' ),
					'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_size',
					'value'       => 'full',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'media_library',
					),
				),
				array(
					'heading'     => esc_html__( 'External link' ),
					'description' => esc_html__( 'Enter external link of the image.' ),
					'type'        => 'textfield',
					'param_name'  => 'image_url',
					'dependency'  => array(
						'element' => 'image_source',
						'value'   => 'external_link',
					),
				),
				array(
					'heading'     => esc_html__( 'Text' ),
					'description' => esc_html__( 'Enter the banner text' ),
					'type'        => 'textfield',
					'param_name'  => 'text',
					'admin_label' => true,
				),
				array(
					'heading'     => esc_html__( 'Alignment' ),
					'description' => esc_html__( 'Select image & text alignment' ),
					'type'        => 'dropdown',
					'param_name'  => 'text_position',
					'std'         => 'center',
					'value'       => array(
						esc_html__( 'Left' )   => 'left',
						esc_html__( 'Center' ) => 'center',
						esc_html__( 'Right' )  => 'right',
					),
				),


				array(
					'heading'    => esc_html__( 'Link (URL)' ),
					'type'       => 'vc_link',
					'param_name' => 'link',
				),
				array(
					'heading'     => esc_html__( 'Button Text (optional)' ),
					'description' => esc_html__( 'Display a button at bottom' ),
					'param_name'  => 'button_text',
					'type'        => 'textfield',
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
				array(
					'heading'    => esc_html__( 'CSS box' ),
					'type'       => 'css_editor',
					'param_name' => 'css',
					'group'      => esc_html__( 'Design Options' ),
				),
			),
		) );

		// Empty Space.
		vc_map(array(
			'name' => esc_html__('Empty Space Advanced'),
			'description' => esc_html__('Empty spacing with resposive options'),
			'base' => 'bluefrog_empty_space',
			'icon' => $this->get_icon('empty.png'),
			'category'    => esc_html__( 'Bluefrog', 'bluefrog' ),
			'params' => array(
				array(
					'heading' => esc_html__('Height'),
					'admin_label' => true,
					'type' => 'textfield',
					'param_name' => 'height',
					'value' => '32px',
				),
				array(
					'heading' => esc_html__('Extra class name'),
					'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.'),
					'param_name' => 'el_class',
					'type' => 'textfield',
					'value' => '',
				),
				array(
					'heading'          => esc_html__('Desktop'),
					'type'             => 'textfield',
					'param_name'       => 'height_lg',
					'edit_field_class' => 'vc_col-xs-10',
					'group'            => esc_html__('Responsive Options'),
				),
				array(
					'heading'          => esc_html__('Hide'),
					'type'             => 'checkbox',
					'value'            => array( '' => 'yes' ),
					'param_name'       => 'hidden_lg',
					'edit_field_class' => 'vc_col-xs-2',
					'group'            => esc_html__('Responsive Options'),
				),
				array(
					'heading'          => esc_html__('Tablet'),
					'type'             => 'textfield',
					'param_name'       => 'height_md',
					'edit_field_class' => 'vc_col-xs-10',
					'group'            => esc_html__('Responsive Options'),
				),
				array(
					'heading'          => esc_html__('Hide'),
					'type'             => 'checkbox',
					'value'            => array( '' => 'yes' ),
					'param_name'       => 'hidden_md',
					'edit_field_class' => 'vc_col-xs-2',
					'group'            => esc_html__('Responsive Options'),
				),
				array(
					'heading'          => esc_html__('Mobile'),
					'type'             => 'textfield',
					'param_name'       => 'height_xs',
					'edit_field_class' => 'vc_col-xs-10',
					'group'            => esc_html__('Responsive Options'),
				),
				array(
					'heading'          => esc_html__('Hide'),
					'type'             => 'checkbox',
					'value'            => array( '' => 'yes' ),
					'param_name'       => 'hidden_xs',
					'edit_field_class' => 'vc_col-xs-2',
					'group'            => esc_html__('Responsive Options'),
				),
			),
		));

		// Collection Carousel
		vc_map( array(
			'name'        => esc_html__( 'Collection Carousel' ),
			'description' => esc_html__( 'Image carousel' ),
			'base'        => 'bluefrog_collection_carousel',
			'icon'        => $this->get_icon( 'collection-carousel.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'heading'     => esc_html__( 'Information' ),
					'type'        => 'param_group',
					'param_name'  => 'collections',
					'params'      => array(
						array(
							'heading'     => esc_html__( 'Image' ),
							'description' => esc_html__( 'Select image from media library' ),
							'type'        => 'attach_image',
							'param_name'  => 'image',
							'dependency'  => array(
								'element' => 'image_source',
								'value'   => 'media_library',
							),
						),
						array(
							'heading'     => esc_html__( 'Image size' ),
							'description' => esc_html__( 'Enter image size (Example: "thumbnail", "medium", "large", "full" or other sizes defined by theme). Alternatively enter size in pixels (Example: 200x100 (Width x Height)). Leave empty to use "thumbnail" size.' ),
							'type'        => 'textfield',
							'param_name'  => 'image_size',
							'value'       => 'thumbnail',
							'dependency'  => array(
								'element' => 'image_source',
								'value'   => 'media_library',
							),
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Title' ),
							'param_name'  => 'title',
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'Button Text' ),
							'param_name'  => 'button_text',
						),
						array(
							'type'        => 'textfield',
							'heading'     => esc_html__( 'URL' ),
							'param_name'  => 'url',
						),
					),
				),
				array(
					'heading'     => esc_html__( 'Auto Play' ),
					'description' => esc_html__( 'Auto play speed in miliseconds. Enter "0" to disable auto play.' ),
					'type'        => 'textfield',
					'param_name'  => 'autoplay',
					'value'       => 5000,
				),
				array(
					'heading'     => esc_html__('Slides Visible'),
					'description' => esc_html__('How many slides are visible.'),
					'type'        => 'textfield',
					'param_name'  => 'items',
					'std'         => 5,
					'value'       => 5,
				),
				array(
					'heading'    => esc_html__( 'Loop' ),
					'type'       => 'checkbox',
					'param_name' => 'loop',
					'std'        => 'yes',
					'value'      => array( esc_html__( 'Yes' ) => 'yes' ),
				),
				array(
					'heading'     => esc_html__( 'Free mode' ),
					'description' => esc_html__( 'Display images in their width and also make neighbour slides visible' ),
					'type'        => 'checkbox',
					'param_name'  => 'freemode',
					'value'       => array( esc_html__( 'Yes' ) => 'yes' ),
				),
				array(
					'heading'    => esc_html__( 'Navigation' ),
					'type'       => 'dropdown',
					'param_name' => 'navigation',
					'std'        => 'arrows',
					'value'      => array(
						esc_html__( 'None' )            => '',
						esc_html__( 'Arrows' )          => 'arrows',
						esc_html__( 'Dots' )            => 'dots',
						esc_html__( 'Arrows and Dots' ) => 'arrows_and_dots',
					),
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'type'        => 'textfield',
					'param_name'  => 'el_class',
				),
			),
		) );
		// Portfolio Grid
		vc_map( array(
			'name'        => esc_html__( 'Portfolio Grid' ),
			'description' => esc_html__( 'Display portfolio in grid' ),
			'base'        => 'bluefrog_portfolio_grid',
			'icon'        => $this->get_icon( 'product-grid.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'description' => esc_html__( 'Number of portfolio you want to show' ),
					'heading'     => esc_html__( 'Number of portfolio' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 9,
				),
				array(
					'heading'     => esc_html__( 'Filter' ),
					'description' => esc_html__( 'Show Filter' ),
					'param_name'  => 'filter',
					'type'        => 'checkbox',
					'value'       => array(
						esc_html__( 'Yes' ) => 'yes',
					),
					'std' => '1'
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Portfolio Masonry
		vc_map( array(
			'name'        => esc_html__( 'Portfolio Masonry' ),
			'description' => esc_html__( 'Display portfolio in masonry' ),
			'base'        => 'bluefrog_portfolio_masonry',
			'icon'        => $this->get_icon( 'banner-grid-5.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'description' => esc_html__( 'Number of portfolio you want to show' ),
					'heading'     => esc_html__( 'Number of portfolio' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 8,
				),
				array(
					'heading'     => esc_html__( 'Filter' ),
					'description' => esc_html__( 'Show Filter' ),
					'param_name'  => 'filter',
					'type'        => 'checkbox',
					'value'       => array(
						esc_html__( 'Yes' ) => 'yes',
					),
					'std' => '1'
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );

		// Portfolio Metro
		vc_map( array(
			'name'        => esc_html__( 'Portfolio Metro' ),
			'description' => esc_html__( 'Display portfolio in metro' ),
			'base'        => 'bluefrog_portfolio_metro',
			'icon'        => $this->get_icon( 'banner-grid-4.png' ),
			'category'    => esc_html__( 'Bluefrog' ),
			'params'      => array(
				array(
					'description' => esc_html__( 'Number of portfolio you want to show' ),
					'heading'     => esc_html__( 'Number of portfolio' ),
					'param_name'  => 'per_page',
					'type'        => 'textfield',
					'value'       => 8,
				),
				array(
					'heading'     => esc_html__( 'Filter' ),
					'description' => esc_html__( 'Show Filter' ),
					'param_name'  => 'filter',
					'type'        => 'checkbox',
					'value'       => array(
						esc_html__( 'Yes' ) => 'yes',
					),
					'std' => '1'
				),
				vc_map_add_css_animation(),
				array(
					'heading'     => esc_html__( 'Extra class name' ),
					'description' => esc_html__( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.' ),
					'param_name'  => 'el_class',
					'type'        => 'textfield',
					'value'       => '',
				),
			),
		) );
	}

	/**
	 * Get Icon URL
	 *
	 * @param string $file_name The icon file name with extension
	 *
	 * @return string Full URL of icon image
	 */
	protected function get_icon( $file_name ) {

		if ( file_exists( BLUEFROG_ADDONS_DIR . 'assets/icons/' . $file_name ) ) {
			$url = BLUEFROG_ADDONS_URL . 'assets/icons/' . $file_name;
		} else {
			$url = BLUEFROG_ADDONS_URL . 'assets/icons/default.png';
		}

		return $url;
	}

	/**
	 * Get category for auto complete field
	 *
	 * @param string $taxonomy Taxnomy to get terms
	 *
	 * @return array
	 */
	public function get_terms( $taxonomy = 'product_cat' ) {
		// We don't want to query all terms again
		if ( isset( $this->terms[ $taxonomy ] ) ) {
			return $this->terms[ $taxonomy ];
		}

		$cats = get_terms( $taxonomy );
		if ( ! $cats || is_wp_error( $cats ) ) {
			return array();
		}

		$categories = array();
		foreach ( $cats as $cat ) {
			$categories[] = array(
				'label' => $cat->name,
				'value' => $cat->slug,
				'group' => 'category',
			);
		}

		// Store this in order to avoid double query this
		$this->terms[ $taxonomy ] = $categories;

		return $categories;
	}

	/**
	 * Add new fonts into Google font list
	 *
	 * @param array $fonts Array of objects
	 *
	 * @return array
	 */
	public function add_google_fonts( $fonts ) {
		$fonts[] = (object) array(
			'font_family' => 'Amatic SC',
			'font_styles' => '400,700',
			'font_types'  => '400 regular:400:normal,700 regular:700:normal',
		);

		$fonts[] = (object) array(
			'font_family' => 'Montez',
			'font_styles' => '400',
			'font_types'  => '400 regular:400:normal',
		);

		usort( $fonts, array( $this, 'sort_fonts' ) );

		return $fonts;
	}

	/**
	 * Sort fonts base on name
	 *
	 * @param object $a
	 * @param object $b
	 *
	 * @return int
	 */
	private function sort_fonts( $a, $b ) {
		return strcmp( $a->font_family, $b->font_family );
	}
}

