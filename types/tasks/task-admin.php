<?php

/**
 * Returns an array of data to be localized in the task edit page.
 *
 * Returns an array of data that gets passed to `wp_localize_script()` in
 * `go_enqueue_admin_scripts_and_styles()` which is then provided to a script that is enqueued on
 * the task edit page for admins. See the `wp_localize_script()` call that this function is used in
 * to find the slug of the script that will be provided with the data below.
 *
 * @since 2.6.1
 *
 * @see go_task_chain_get_id_by_task(), go_task_chain_is_final_task()
 * @global WP_Post $post The WP_Post object of the current post.
 *
 * @return array Array of task data to be localized. Contains the following:
 *
 *     array(
 *         'stages' => array(
 *             'is_stage_three_active' => boolean, // whether or not the task has three stages (false)
 *             'is_stage_five_active'  => boolean, // whether or not the task has five stages (false)
 *         ),
 *         'task_chains' => array(                 // an array of `task_chains` taxonomy data
 *             'in_chain'         => boolean,      // whether or not the task is in a chain (false)
 *             'chain_name'       => string,       // the term name of the associated chain ('')
 *             'is_last_in_chain' => boolean,      // whether or not the task is in the final position
 *         ),                                      // of a chain (false)
 *         'task_id' => int|null,                  // the post id of the task (null)
 *     )
 */
function go_localize_task_data() {
	global $post;

	$task_id = $post->ID;
	$custom_data = get_post_custom( $task_id );
	$tt_id = go_task_chain_get_id_by_task( $task_id );
	$chain_name = go_task_chain_get_name_by_id( $tt_id );

	$is_stage_three_active = false;
	$is_stage_five_active = false;
	$in_chain = false;
	$is_last_in_chain = go_task_chain_is_final_task( $task_id, $tt_id );

	if ( empty( $task_id ) || $task_id < 0 ) {
		$task_id = null;
	}

	if ( null !== $tt_id ) {
		$in_chain = true;
	}

	if ( ! empty( $custom_data['go_mta_three_stage_switch'][0] ) &&
			'on' === $custom_data['go_mta_three_stage_switch'][0] ) {
		$is_stage_three_active = true;
	}

	if ( ! empty( $custom_data['go_mta_five_stage_switch'][0] ) &&
			'on' === $custom_data['go_mta_five_stage_switch'][0] ) {
		$is_stage_five_active = true;
	}

	return array(
		'stages' => array(
			'is_stage_three_active' => $is_stage_three_active,
			'is_stage_five_active'  => $is_stage_five_active,
		),
		'task_chains' => array(
			'in_chain'         => $in_chain,
			'chain_name'       => $chain_name,
			'is_last_in_chain' => $is_last_in_chain,
		),
		'task_id' => $task_id,
	);
}
