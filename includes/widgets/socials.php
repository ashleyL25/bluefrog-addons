<?php
/**
 * Social Widget
 */

if ( ! class_exists( 'Bluefrog_Social_Links_Widget' ) ) :
/**
 * Social Widget class.
 */
class Bluefrog_Social_Links_Widget extends WP_Widget {
	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $default;

	/**
	 * List of supported socials
	 *
	 * @var array
	 */
	protected $socials;

	/**
	 * Constructor
	 */
	function __construct() {
		$socials = array(
			'facebook'    => esc_html__( 'Facebook', 'bluefrog-addons' ),
			'twitter'     => esc_html__( 'Twitter', 'bluefrog-addons' ),
			'tumblr'      => esc_html__( 'Tumblr', 'bluefrog-addons' ),
			'linkedin'    => esc_html__( 'Linkedin', 'bluefrog-addons' ),
			'pinterest'   => esc_html__( 'Pinterest', 'bluefrog-addons' ),
			'flickr'      => esc_html__( 'Flickr', 'bluefrog-addons' ),
			'instagram'   => esc_html__( 'Instagram', 'bluefrog-addons' ),
			'dribbble'    => esc_html__( 'Dribbble', 'bluefrog-addons' ),
			'stumbleupon' => esc_html__( 'StumbleUpon', 'bluefrog-addons' ),
			'github'      => esc_html__( 'Github', 'bluefrog-addons' ),
			'youtube'     => esc_html__( 'Youtube', 'bluefrog-addons' ),
			'vimeo'       => esc_html__( 'Youtube', 'bluefrog-addons' ),
			'houzz'       => esc_html__( 'Houzz', 'bluefrog-addons' ),
			'rss'         => esc_html__( 'RSS', 'bluefrog-addons' ),
		);

		$this->socials = apply_filters( 'bluefrog_social_media', $socials );
		$this->default = array(
			'title' => '',
		);
		foreach ( $this->socials as $k => $v ) {
			$this->default["{$k}_title"] = $v;
			$this->default["{$k}_url"]   = '';
		}

		parent::__construct(
			'social-links-widget',
			esc_html__( 'Bluefrog - Social Links', 'bluefrog-addons' ),
			array(
				'classname'   => 'social-links-widget social-links',
				'description' => esc_html__( 'Display links to social media networks.', 'bluefrog-addons' ),
			),
			array( 'width' => 600 )
		);
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme
	 * @param array $instance An array of settings for this widget instance
	 *
	 * @return void Echoes it's output
	 */
	function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->default );

		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		foreach ( $this->socials as $social => $label ) {
			if ( 'google-plus' == $social ) {
				continue;
			}

			if ( ! empty( $instance[$social . '_url'] ) ) {
				$icon = 'youtube' == $social ? 'youtube-play' : $social;

				printf(
					'<a href="%s" class="share-%s tooltip-enable social" rel="nofollow" title="%s" data-toggle="tooltip" data-placement="top" target="_blank"><i class="fa fa-%s"></i></a>',
					esc_url( $instance[$social . '_url'] ),
					esc_attr( $social ),
					esc_attr( $instance[$social . '_title'] ),
					esc_attr( $icon )
				);
			}
		}

		echo $args['after_widget'];
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	function form( $instance ) {
		$instance = wp_parse_args( $instance, $this->default );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'bluefrog-addons' ); ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<?php
		foreach ( $this->socials as $social => $label ) {
			printf(
				'<div style="width: 280px; float: left; margin-right: 10px;">
					<label>%s</label>
					<p><input type="text" class="widefat" name="%s" placeholder="%s" value="%s"></p>
				</div>',
				$label,
				$this->get_field_name( $social . '_url' ),
				esc_html__( 'URL', 'bluefrog-addons' ),
				$instance[$social . '_url']
			);
		}
	}
}
endif;