<?php
/*
Plugin Name:  Tower Brass Events
Plugin URI:   https://www.mymentech.com
Description:  Basic WordPress Plugin.
Version:      1.0
Author:       MymenTech
Author URI:   https://www.mymentech.com
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  tb-sean
*/
include_once plugin_dir_path(__FILE__).'vc-addons.php';;
include_once plugin_dir_path(__FILE__).'customize-admin-column.php';;
class Tbsean
{
	private $screen = array(
		'tb-event',
	);
	private $meta_fields = array(
		array(
			'label' => 'Event Date',
			'id' => 'tb-event-date',
			'type' => 'date',
		),
		array(
			'label' => 'Performance',
			'id' => 'tb-event-performance',
			'type' => 'text',
		),
		array(
			'label' => 'Arrangement',
			'id' => 'tb-event-arrangement',
			'type' => 'text',
		),
		array(
			'label' => 'Location',
			'id' => 'tb-event-location',
			'default' => 'City',
			'type' => 'text',
		),
	);

	private $cpt_args = array(
		'labels' => array(
			'name' => 'Our Events', 'post type general name',
			'singular_name' => 'Over Event', 'post type singular name',
			'menu_name' => 'Our Events', 'admin menu',
			'name_admin_bar' => 'events', 'add new on admin bar',
			'add_new' => 'Add New Event', 'book',
			'add_new_item' => 'Add New Event',
			'new_item' => 'New Event',
			'edit_item' => 'Edit Event',
			'view_item' => 'View Event',
			'all_items' => 'All Event',
			'not_found' => 'No member found.',
		),

		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'query_var' => true,
		'rewrite' => array(
			'slug' => 'our-events'
		),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'menu_icon' => 'dashicons-admin-users',
		'supports' => array('title')
	);

	public function __construct()
	{
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_fields'));
		add_action('init', array($this,'tb_register_cpt_event'));



	}

	public function tb_register_cpt_event(){
		register_post_type('tb-event', $this->cpt_args);
	}

	public function add_meta_boxes()
	{
		foreach ($this->screen as $single_screen) {
			add_meta_box(
				'eventinfo',
				__('Event Info', 'tb-sean'),
				array($this, 'meta_box_callback'),
				$single_screen,
				'advanced',
				'default'
			);
		}
	}

	public function meta_box_callback($post)
	{
		wp_nonce_field('eventinfo_data', 'eventinfo_nonce');
		echo 'Event Information';
		$this->field_generator($post);
	}

	public function field_generator($post)
	{
		$output = '';
		foreach ($this->meta_fields as $meta_field) {
			$label      = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
			$meta_value = get_post_meta($post->ID, $meta_field['id'], true);
			if (empty($meta_value)) {
				$meta_value = $meta_field['default'];
			}
			switch ($meta_field['type']) {
				default:
					$input = sprintf(
						'<input %s id="%s" name="%s" type="%s" value="%s">',
						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value
					);
			}
			$output .= $this->format_rows($label, $input);
		}
		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
	}

	public function format_rows($label, $input)
	{
		return '<tr><th>' . $label . '</th><td>' . $input . '</td></tr>';
	}

	public function save_fields($post_id)
	{
		if (!isset($_POST['eventinfo_nonce']))
			return $post_id;
		$nonce = $_POST['eventinfo_nonce'];
		if (!wp_verify_nonce($nonce, 'eventinfo_data'))
			return $post_id;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return $post_id;
		foreach ($this->meta_fields as $meta_field) {
			if (isset($_POST[$meta_field['id']])) {
				switch ($meta_field['type']) {
					case 'email':
						$_POST[$meta_field['id']] = sanitize_email($_POST[$meta_field['id']]);
						break;
					case 'text':
						$_POST[$meta_field['id']] = sanitize_text_field($_POST[$meta_field['id']]);
						break;
				}
				update_post_meta($post_id, $meta_field['id'], $_POST[$meta_field['id']]);
			} else if ($meta_field['type'] === 'checkbox') {
				update_post_meta($post_id, $meta_field['id'], '0');
			}
		}
	}
}

if (class_exists('Tbsean')) {
	new Tbsean();
};
