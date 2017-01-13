<?php
/**
 * Works the magic
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function give_google_analytics_send_data( $payment, $give_receipt_args ) {

	if ( $give_receipt_args['payment_id'] ) {
		// Use a meta value so we only send the beacon once.
		if ( get_post_meta( $payment->ID, 'give_ga_beacon_sent', true ) ) {
			return;
		}

		$total = give_get_payment_amount( $payment->ID );
		$meta  = give_get_payment_meta( $payment->ID );
		$id    = give_get_payment_number( $payment->ID );

		?>
		<script type="text/javascript">

			ga('require', 'ecommerce', 'ecommerce.js');

			ga('ecommerce:addTransaction', {
				'id': '<?php echo esc_js( $id ); ?>', // Transaction ID. Required.
				'affiliation': '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>', // Affiliation or store name.
				'revenue': '<?php echo esc_js( $total ); ?>' // donation amount.
			});

			ga('ecommerce:addItem', {
				'id': '<?php echo esc_js( $id ); ?>',
				'name': '<?php echo give_get_payment_form_title( $meta ); ?>',

			})
			ga('ecommerce:send');

		<?php //TODO: add conditional check for give category and add it as a 'category' key to the addItem command ?>

		</script>
		<?php

		update_post_meta( $payment->ID, 'give_ga_beacon_sent', true );

	}
}

add_action( 'give_payment_receipt_after_table', 'give_google_analytics_send_data', 10, 2 );
