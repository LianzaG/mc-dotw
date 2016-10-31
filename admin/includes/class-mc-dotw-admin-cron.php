<?php
/**
 * Cron class performing admin tasks to keep deals
 * product references and wc catalogue in sync.
 *
 * @package   MC_Dotw
 * @author    Julien Bosuma <jbosuma@gmail.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Julien Bosuma
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

Class MC_Dotw_Admin_Cron
{
	/**
	 * Refers to the current week's number.
	 *
	 * @since    1.0.0
	 *
	 * @var      integer
	 */
	protected $cur_week = 0;

	/**
	 * Refers to the single class instance.
	 *
	 * @since    1.0.0
	 *
	 * @var      MC_Dotw_Admin_Cron
	 */
	protected static $_instance;

	/**
	 * Constructor for the cron class.
	 */
	protected function __construct()
	{
		$this->cur_week = date( 'W' );
		$this->register_hooks();
	}

	/**
    * Create the single MC_Dotw_Admin_Cron class instance if it doesn't exist
    * and return it.
    *
    * @since    1.0.0
    *
    * @param    void
    *
    * @return   MC_Dotw_Admin_Cron
    */
   public static function getInstance()
   {
     if ( is_null(self::$_instance) ) {
       self::$_instance = new MC_Dotw_Admin_Cron();
     }

     return self::$_instance;
   }

	/**
	 * Register class actions/filters hooks.
	 *
	 * @since    1.0.0
	 *
	 * @param    void
	 *
	 * @return   void
	 */
	protected function register_hooks()
	{
		add_action( 'woocommerce_scheduled_sales',      array( $this, 'scheduled_deals' ) );
		add_action( 'dotw_before_admin_page_display',   array( $this, 'scheduled_deals' ) );
		add_action( 'save_post',                        array( $this, 'sync_deal_to_saved_post' ) );
	}

	/**
	 * Run a daily cron job on deals status and switch to a new week's deal when needed,
	 * making sure to handle all required steps to maintain data integrity.
	 *
	 * @since    1.0.0
	 *
	 * @param    void
	 *
	 * @return   void
	 */
	public function scheduled_deals()
	{
		// MC_Dotw_Debugger::log();
		$options = MC_Dotw_Admin::get_instance()->get_options();
		$cur_deal = new MC_Dotw_Deal( $this->cur_week );

		if ( ! $cur_deal->is_active() ) {
			// Get stored Deals options, get previous deal.
			$prev_deal = new MC_Dotw_Deal( $this->cur_week-1 );

			// Previous deal.
			if ( $prev_deal->is_active() ) {
				// Restore the previous deal's backed up pricing data.
				$prev_deal->update_product( true );

				// Set options previous deal's status to inactive.
				$options['deals'][$this->cur_week-2]['is_active'] = false;
			}

			// Current deal.
			$cur_deal->set( 'is_active', true ); 	// Instance's 'is_active' flag is not persisted!
			$cur_deal->update_product();

			// Set options current deal's status to active.
			$options['deals'][$this->cur_week-1]['is_active'] = true; 	// This will persist in the options table.

			// Prepare options.
            $options['last_updated'] = time();
            // Update options.
			update_option( 'mc_dotw', $options );
		}
	}

	/**
	 * Update a deal's data after the associated wc product has been modified.
	 *
	 * @since    1.0.0
	 *
	 * @param    string  $post_id  (Required) Post ID of the modified product, passed by the save hook.
	 *
	 * @return   void
	 */
	public function sync_deal_to_saved_post( $post_id='' )
	{
		// If this is a revision, just leave.
		if ( wp_is_post_revision( $post_id ) )
			return;

		if ( 'product' === get_post_type( $post_id ) ) {
			$product   = new MC_Dotw_Product( $post_id );
			$meta 	   = $product->get( 'meta' );
			$deal_nums = $product->get_deal_nums();

			// If the product is associated to one or more deal(s) of the week.
			if ( count( $deal_nums ) ) {
				foreach ($deal_nums as $deal_num) {
					// 1. Get a class instance for each one of the linked deals.
					$deal = new MC_Dotw_Deal( (int) $deal_num );

					// 2. Set it's data to mirror the new product's state.
					$deal->set( 'title',         wc_get_product( $post_id )->get_title() );
					$deal->set( 'regular_price', $meta['_regular_price'][0] );

					// Preserve active deal's backed up data.
					if ( ! $deal->is_active() ) {
						// 2.2 To be updated aswell, for inactive deals:
						$deal->set( 'sale_price',    $meta['_sale_price'][0] );
						$deal->set( 'date_from',     $meta['_sale_price_dates_from'][0] );
						$deal->set( 'date_to',       $meta['_sale_price_date_from'][0] );
					}
					// 3. Save the updated linked deal.
					$deal->save();
				} // End foreach $deal_nums.
			} // End if $deal_nums.
		} // End if product.
	}
}
