<?php

function tb_event_post_admin_columns($columns, $post_type){
	if('tb-event'!=$post_type){
		return $columns;
	}

	unset($columns['date']);
	$columns['event_date'] = "Event Date";
	$columns['date'] = "Created On";

	return $columns;
}
add_filter('manage_posts_columns', 'tb_event_post_admin_columns', 10,2);

function tb_event_post_admin_columns_data($column, $post_id){
	if('event_date'==$column){
		$date        = get_post_meta($post_id, 'tb-event-date', true);
		$date        = date_create($date);
		$date        = date_format($date, "D, d M Y");
		echo $date;
	}
}
add_action('manage_posts_custom_column', 'tb_event_post_admin_columns_data', 10,2);


function tb_event_date_sortable_column($columns){
	$columns['event_date'] = 'sort_event_date';
	return $columns;
}
add_filter('manage_edit-tb-event_sortable_columns', 'tb_event_date_sortable_column');

function tb_event_sort_date_column_by_post_meta_date($wpquery){
	if(!is_admin()){
		return;
	}

	$orderby = $wpquery->get('orderby');
	if('sort_event_date'==$orderby){
		$wpquery->set('meta_key', 'tb-event-date');
		$wpquery->set('orderby', 'meta_value');
	}

}
add_action('pre_get_posts', 'tb_event_sort_date_column_by_post_meta_date');