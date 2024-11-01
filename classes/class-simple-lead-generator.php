<?php
/**
 * Initialization
 *
 * @package Simple Lead Generator
 * @since 1.0.0
 */

if ( ! class_exists( 'Simple_Lead_Generator' ) ) :

	/**
	 * Simple Lead Generator
	 *
	 * @since 1.0.0
	 */
	class Simple_Lead_Generator {

		/**
		 * Instance
		 *
		 * @access private
		 * @var object Class Instance.
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.0.0
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			add_action( 'wp_ajax_simple_lead_generator_submit', array( $this, 'submit_form' ) );
			add_action( 'wp_ajax_nopriv_simple_lead_generator_submit', array( $this, 'submit_form' ) );
			add_shortcode( 'simple_lead_generator', array( $this, 'shortcode_markup' ) );
			add_action( 'init', array( $this, 'register_post_type' ) );
		}

		/**
		 * Registers Post type
		 *
		 * @since 1.0.0
		 * @return void
		 */
		function register_post_type() {

			$labels = array(
				'name'               => __( 'Customers', 'simple-lead-generator' ),
				'singular_name'      => __( 'Customer', 'simple-lead-generator' ),
				'add_new'            => __( 'Add New', 'simple-lead-generator' ),
				'add_new_item'       => __( 'Add New', 'simple-lead-generator' ),
				'edit_item'          => __( 'Edit Customer', 'simple-lead-generator' ),
				'new_item'           => __( 'New Customer', 'simple-lead-generator' ),
				'view_item'          => __( 'View Customer', 'simple-lead-generator' ),
				'search_items'       => __( 'Search Customers', 'simple-lead-generator' ),
				'not_found'          => __( 'No Customers found', 'simple-lead-generator' ),
				'not_found_in_trash' => __( 'No Customers found in Trash', 'simple-lead-generator' ),
				'parent_item_colon'  => __( 'Parent Customer:', 'simple-lead-generator' ),
				'menu_name'          => __( 'Customers', 'simple-lead-generator' ),
			);

			$args = array(
				'labels'              => $labels,
				'hierarchical'        => false,
				'description'         => 'description',
				'taxonomies'          => array(),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => null,
				'menu_icon'           => 'dashicons-id-alt',
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => false,
				'can_export'          => true,
				'rewrite'             => true,
				'capability_type'     => 'post',
				'supports'            => array(
					'title',
					'custom-fields',
				),
			);

			register_post_type( 'customer', $args );

			/**
			 * Register Categories.
			 */
			$tax_labels = array(
				'name'              => _x( 'Categories', 'taxonomy general name', 'simple-lead-generator' ),
				'singular_name'     => _x( 'Categories', 'taxonomy singular name', 'simple-lead-generator' ),
				'search_items'      => __( 'Search Categories', 'simple-lead-generator' ),
				'all_items'         => __( 'All Categories', 'simple-lead-generator' ),
				'parent_item'       => __( 'Parent Categories', 'simple-lead-generator' ),
				'parent_item_colon' => __( 'Parent Categories:', 'simple-lead-generator' ),
				'edit_item'         => __( 'Edit Categories', 'simple-lead-generator' ),
				'update_item'       => __( 'Update Categories', 'simple-lead-generator' ),
				'add_new_item'      => __( 'Add New Categories', 'simple-lead-generator' ),
				'new_item_name'     => __( 'New Categories Name', 'simple-lead-generator' ),
				'menu_name'         => __( 'Categories', 'simple-lead-generator' ),
			);

			$tax_args = array(
				'hierarchical'      => true,
				'labels'            => $tax_labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => false,
				'show_in_rest'      => false,
				'can_export'        => true,
			);

			register_taxonomy( 'customer-category', array( 'customer' ), $tax_args );

			/**
			 * Register Tags
			 */
			$tax_labels = array(
				'name'              => _x( 'Tags', 'taxonomy general name', 'simple-lead-generator' ),
				'singular_name'     => _x( 'Tags', 'taxonomy singular name', 'simple-lead-generator' ),
				'search_items'      => __( 'Search Tags', 'simple-lead-generator' ),
				'all_items'         => __( 'All Tags', 'simple-lead-generator' ),
				'parent_item'       => __( 'Parent Tags', 'simple-lead-generator' ),
				'parent_item_colon' => __( 'Parent Tags:', 'simple-lead-generator' ),
				'edit_item'         => __( 'Edit Tags', 'simple-lead-generator' ),
				'update_item'       => __( 'Update Tags', 'simple-lead-generator' ),
				'add_new_item'      => __( 'Add New Tags', 'simple-lead-generator' ),
				'new_item_name'     => __( 'New Tags Name', 'simple-lead-generator' ),
				'menu_name'         => __( 'Tags', 'simple-lead-generator' ),
			);

			$tax_args = array(
				'hierarchical'      => false,
				'labels'            => $tax_labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => false,
				'show_in_rest'      => false,
				'can_export'        => true,
			);

			register_taxonomy( 'customer-tag', array( 'customer' ), $tax_args );
		}

		/**
		 * Submit Form
		 *
		 * @since 1.0.0
		 */
		function submit_form() {

			check_ajax_referer( 'simple-lead-generator', '_ajax_nonce' );

			$fields     = isset( $_POST['fields'] ) ? $_POST['fields'] : array();
			$post_title = isset( $_POST['post_title'] ) ? sanitize_text_field( $_POST['post_title'] ) : '';

			if ( empty( $fields ) ) {
				wp_send_json_error( __( 'Missing form fields.', 'simple-lead-generator' ) );
			}

			$validated_fields = array();

			// Sanitize all fields.
			// @todo validate URL, file and other fields after adding support for them.
			foreach ( $fields as $key => $field ) {
				switch ( $field['type'] ) {
					case 'email':
						$validated_fields[ $field['name'] ] = sanitize_email( $field['value'] );
						break;
					case 'textarea':
						$validated_fields[ $field['name'] ] = sanitize_textarea_field( $field['value'] );
						break;
					default:
						$validated_fields[ $field['name'] ] = sanitize_text_field( $field['value'] );
						break;
				}
			}

			// Insert the record.
			$post_id = wp_insert_post(
				array(
					'post_title' => $post_title,
					'post_type'  => 'customer',
					'meta_input' => $validated_fields,
				)
			);

			if ( is_wp_error( $post_id ) ) {
				wp_send_json_error();
			}

			wp_send_json_success(
				array(
					'post_id' => $post_id,
					'fields'  => $fields,
				)
			);
		}

		/**
		 * Shortcode markup
		 *
		 * @since 1.0.0
		 * @param array  $atts Shortcode attributes.
		 * @param string $content Shortcode content.
		 *
		 * @return string HTML content to display the shortcode.
		 */
		function shortcode_markup( $atts = array(), $content = '' ) {

			wp_enqueue_script( 'simple-lead-generator' );

			$atts = shortcode_atts(
				array(
					'form-title'          => __( 'Fill Your Information', 'simple-lead-generator' ),
					'form-description'    => __( 'Send us your details and we\'ll get back to you!', 'simple-lead-generator' ),

					'name-label'          => __( 'Name', 'simple-lead-generator' ),
					'name-required'       => true,
					'name-placeholder'    => '',
					'name-maxlength'      => '',

					'phone-label'         => __( 'Phone', 'simple-lead-generator' ),
					'phone-required'      => true,
					'phone-placeholder'   => '',
					'phone-maxlength'     => '',

					'email-label'         => __( 'Email', 'simple-lead-generator' ),
					'email-required'      => true,
					'email-placeholder'   => '',
					'email-maxlength'     => '',

					'budget-label'        => __( 'Budget', 'simple-lead-generator' ),
					'budget-required'     => true,
					'budget-placeholder'  => '',
					'budget-maxlength'    => '',

					'message-label'       => __( 'Message', 'simple-lead-generator' ),
					'message-required'    => true,
					'message-placeholder' => '',
					'message-maxlength'   => '',
					'message-rows'        => 3,
					'message-cols'        => 30,

					'submit-text'         => __( 'Submit', 'simple-lead-generator' ),

				),
				$atts,
				'simple_lead_generator'
			);

			// @todo add filter support to add more fields support.
			$fields = array(
				'name'    => array(
					'label'       => $atts['name-label'],
					'required'    => $atts['name-required'],
					'placeholder' => $atts['name-placeholder'],
					'maxlength'   => $atts['name-maxlength'],
					'type'        => 'text',
				),
				'phone'   => array(
					'label'       => $atts['phone-label'],
					'required'    => $atts['phone-required'],
					'placeholder' => $atts['phone-placeholder'],
					'maxlength'   => $atts['phone-maxlength'],
					'type'        => 'text',
				),
				'email'   => array(
					'label'       => $atts['email-label'],
					'required'    => $atts['email-required'],
					'placeholder' => $atts['email-placeholder'],
					'maxlength'   => $atts['email-maxlength'],
					'type'        => 'email',
				),
				'budget'  => array(
					'label'       => $atts['budget-label'],
					'required'    => $atts['budget-required'],
					'placeholder' => $atts['budget-placeholder'],
					'maxlength'   => $atts['budget-maxlength'],
					'type'        => 'number',
				),
				'message' => array(
					'label'       => $atts['message-label'],
					'required'    => $atts['message-required'],
					'placeholder' => $atts['message-placeholder'],
					'maxlength'   => $atts['message-maxlength'],
					'rows'        => $atts['message-rows'],
					'cols'        => $atts['message-cols'],
					'type'        => 'textarea',
				),
			);

			ob_start();
			?>
			<form class="slg-form" method="post" >

				<?php if ( ! empty( $atts['form-title'] ) ) { ?>
					<h2 class="slg-form-title"><?php echo esc_html( $atts['form-title'] ); ?></h2>
				<?php } ?>

				<?php if ( ! empty( $atts['form-description'] ) ) { ?>
					<p class="slg-form-description"><?php echo esc_html( $atts['form-description'] ); ?></p>
				<?php } ?>

				<?php if ( ! empty( $fields ) && is_array( $fields ) ) { ?>
					<div class="slg-fields">
					<?php foreach ( $fields as $field_name => $field ) { ?>
						<div class="slg-field">
							<label class="slg-field-label"><?php echo esc_html( $field['label'] ); ?>

								<?php $this->required_label( $field['required'] ); ?>

								<?php if ( 'textarea' === $field['type'] ) { ?>
									<textarea class="slg-field-input"
										<?php $this->add_attribute( 'field-type', $field['type'] ); ?>
										<?php $this->add_attribute( 'name', $field_name ); ?>
										<?php $this->add_attribute( 'required', $field['required'] ); ?>
										<?php $this->add_attribute( 'placeholder', $field['placeholder'] ); ?>
										<?php $this->add_attribute( 'maxlength', $field['maxlength'] ); ?>
										<?php $this->add_attribute( 'rows', $field['rows'] ); ?>
										<?php $this->add_attribute( 'cols', $field['cols'] ); ?>
										/></textarea>
								<?php } else { ?>
									<input class="slg-field-input"
										type="<?php echo esc_attr( $field['type'] ); ?>"
										<?php $this->add_attribute( 'field-type', $field['type'] ); ?>
										<?php $this->add_attribute( 'name', $field_name ); ?>
										<?php $this->add_attribute( 'required', $field['required'] ); ?>
										<?php $this->add_attribute( 'placeholder', $field['placeholder'] ); ?>
										<?php $this->add_attribute( 'maxlength', $field['maxlength'] ); ?>
										/>
								<?php } ?>
							</label>
						</div>
					<?php } ?>
					<input class="slg-field-input" type="hidden" name="datatime" field-type="hidden" value="<?php echo esc_attr( gmdate( 'd F Y h:i a' ) ); ?>" />
					<input class="slg-field-submit" type="submit" value="<?php echo esc_attr( $atts['submit-text'] ); ?>" />
					</div><!-- .slg-fields -->
				<?php } ?>
			</form><!-- .slg-form -->
			<?php
			return ob_get_clean();
		}

		/**
		 * Required Label
		 *
		 * @since 1.0.0
		 * @param  boolean $required Required field status.
		 * @return void
		 */
		function required_label( $required = false ) {
			if ( $required ) {
				echo '<span class="slg-required-label">*</span>';
			}
		}

		/**
		 * Add attributes
		 *
		 * @since 1.0.0
		 * @param string $attribute Attribute.
		 * @param string $value     Value.
		 */
		function add_attribute( $attribute = '', $value = '' ) {
			if ( ! empty( $value ) ) {
				echo $attribute . '="' . esc_attr( $value ) . '"';
			}
		}

		/**
		 * Enqueue Assets.
		 *
		 * @version 1.0.0
		 * @return void
		 */
		function enqueue_assets() {

			wp_enqueue_style( 'simple-lead-generator', SIMPLE_LEAD_GENERATOR_URI . 'assets/css/style.css', null, SIMPLE_LEAD_GENERATOR_VER, 'all' );
			wp_register_script( 'simple-lead-generator', SIMPLE_LEAD_GENERATOR_URI . 'assets/js/script.js', array( 'jquery' ), SIMPLE_LEAD_GENERATOR_VER, true );

			$vars = array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'_ajax_nonce'     => wp_create_nonce( 'simple-lead-generator' ),

				// @todo Add fail message.
				// @todo Make a better translation ready string.
				'success-message' => __( '<p><b>Thank you for submission!</b></p><p>We appreciate you submission us. We will get back in touch with you soon!</p><p>Have a great day!</p>', 'simple-lead-generator' ),
			);
			wp_localize_script( 'simple-lead-generator', 'SimpleLeadGeneratorVars', $vars );
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Simple_Lead_Generator::get_instance();

endif;
