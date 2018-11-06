<?php


function mt_addon_scripts(){
	wp_enqueue_style('mt_addon_style', plugin_dir_url(__FILE__).'/css/style.css',null, '1.0');
}
add_action('wp_enqueue_scripts','mt_addon_scripts',99);



add_action('vc_before_init', 'bt_event_vc');
function bt_event_vc()
{
	vc_map(array(
		"name" => "Tb Event List",
		"base" => "tb-event-list",
		"class" => "",
		"category" => "Content",
		"params" => array(
			array(
				"type" => "textfield",
				"holder" => "div",
				"class" => "",
				"heading" =>"How many events?",
				"param_name" => "count",
				"value" => 5,
				"description" => __("Enter number of events to show", "tb-sean"),
			),

		)
	));
}

add_shortcode('tb-event-list', 'tb_evemt_shortcode');
function tb_evemt_shortcode($atts, $content)
{
	extract( shortcode_atts( array(
		'count' => 5,
	), $atts ) );


	$event_header = <<<EHEAD
<div class="lc_content_full lc_swp_boxed ">
<div class="vc_row wpb_row vc_row-fluid">
<div class="wpb_column vc_column_container vc_col-sm-12">
<div class="vc_column-inner ">
<div class="wpb_wrapper ">

<table class="event-list">
<thead>
<tr class="table-heading">
<th>date</th>
<th>performance</th>
<th>arrangement</th>
<th>location</th>
</thead>
</tr>
<tbody>

EHEAD;
	$html_body    = '';
	$cq           = new WP_Query(array(
		'post_type' => 'tb-event',
		'post_status' => 'publish',
		'posts_per_page' => $count,
		'meta_key'          => 'tb-event-date',
		'orderby'           => 'meta_value meta_value_num',
		'order'             => 'ASC'
	));

	while ($cq->have_posts()): $cq->the_post();
		$date        = get_post_meta(get_the_ID(), 'tb-event-date', true);
		$date        = date_create($date);
		$date        = date_format($date, "M.d-D");
		$performance = get_post_meta(get_the_ID(), 'tb-event-performance', true);
		$arrangement = get_post_meta(get_the_ID(), 'tb-event-arrangement', true);
		$location    = get_post_meta(get_the_ID(), 'tb-event-location', true);

		$html_body .= '<tr>';
		$html_body .= sprintf("<td>%s</td>", $date);
		$html_body .= sprintf("<td>%s</td>", $performance);
		$html_body .= sprintf("<td>%s</td>", $arrangement);
		$html_body .= sprintf("<td>%s</td>", $location);
		$html_body .= '</tr>';
	endwhile;

	wp_reset_postdata();
	$event_foter = <<<EFOOTER
    </tbody>
</table>
</div>
</div>
</div>
</div>
</div>
EFOOTER;
	return $event_header . $html_body . $event_foter;

}
