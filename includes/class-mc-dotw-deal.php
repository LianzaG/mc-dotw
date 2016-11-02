<?php
/**
 * "Deal of the Week" object.
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

Class MC_Dotw_Deal {
	/**
	 * Stores the week number.
	 *
	 * @since   1.0.0
	 *
	 * @var int
	 */
	protected $_week_num = 0;

	/**
	 * Stores the deal's data.
	 *
	 * @since   1.0.0
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * Stores a MC_Dotw_Product instance referencing the deal's product.
	 *
	 * @since   1.0.0
	 *
	 * @var MC_Dotw_Product
	 */
	protected $product = null;

	/**
	 * Constructor for the deal class.
	 *
	 * @since   1.0.0
	 *
	 * @param     integer    $week_num    (Optional) A number indicating which
	 *                                    week of the year the deal relates to.
	 *                                    Default to the current week's number.
	 *
	 * @return    void
	 */
	public function __construct( $week_num=0 )
	{
		$week_num = (0 == $week_num) ? date( 'W' ) : $week_num;
		$week_num = $this->validate( 'week_num', $week_num );

		if ( $week_num ) {
			$this->_week_num = $week_num;
			$this->_set_data();
			$this->_set_product();
		}
	}

	/**
	 * Load stored data from dotw options.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return  void
	 */
	private function _set_data()
	{
		$options = MC_Dotw_Admin::get_instance()->get_options();

		// Get requested week's deal from plugin's options or get a default dataset if
		// this week number is not yet set in the options (install?).
		$option      = $options['deals'][$this->_week_num-1];
		$this->_data = $option ? $option : self::get_default_dataset( $this->_week_num );

		// If the deal is set to active but it's end date is already in the past, deactivate it.
		if ( $option && $this->get( 'is_active' ) ) {
			$calendR_objs = $this->get_calendar_objects();

			if ( $calendR_objs['end']->getTimestamp() < time() ) {
				$this->_data['is_active'] = false ;
			}
		}
	}

	/**
	 * Load a MC_Dotw_Product object referencing the associated product.
	 *
	 * @since   1.0.0
	 *
	 * @param   integer    $id    (Optional) Product post id. If provided,
	 *                            this will override any product set for
	 *                            the deal with the one passed as argument.
	 *                            Default to the deal's product if it has one.
	 *
	 * @return  void
	 */
	private function _set_product( $id = 0 )
	{
		$id = (int) $id ? : $this->get( 'product_id' );

		if ( $id  || ! $this->is_blank() ) {
			$this->product = MC_Dotw_Product::get_product_by_id( $id );
		}
	}

	/**
	 * Set method for a single data element.
	 *
	 * @since   1.0.0
	 *
	 * @param  string  $key    (Required) Array key of the data element to set.
	 * @param  mixed   $value  (Required) New value to store for the data element $key.
	 *
	 * @return boolean         False if the input is invalid and could not be set. True otherwise.
	 */
	public function set( $key='', $value )
	{
		$valid_input = $this->validate( $key, $value );

		if ( $valid_input ) {
			if ( in_array( $key, array( 'regular_price', 'sale_price', 'date_from', 'date_to' ) ) ) {
				$this->_data['backup'][$key] = $valid_input;
			} elseif ( ! strcmp( $key, 'product' ) ) {
				$this->product = $valid_input;
			} else {
				$this->_data[$key] = $valid_input;
			}
			return true;
		}
		return false;
	}

	/**
	 * Set method for the whole data set.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $atts    (Optional) Associative array of deal data elements about to be mapped to
	 *                          the deal object's [$_data] attribute. Defaults to setting a default dataset.
	 *
	 * @return  bool 			True if the dataset was valid and has been set. False otherwise.
	 */
	public function set_data( $atts=array() )
	{
		$atts = shortcode_atts( $this->_get_default_dataset( $this->get( 'week_num' ) ), $atts );

		$is_valid = true;

		foreach ($atts as $key => $value) {
			$atts[$key] = $this->validate( $key, $value );

			// Check validation result, making an exception for the boolean 'is_active'.
			// $is_valid = ( 'is_active' !== $key && false !== $atts[$key] ) || $key === 'is_active'
			// 	? $is_valid
			// 	: false;
		}

		if ( $is_valid ) {
			$this->_before_set_data( $atts );
			$this->_data = $atts;
			$this->_set_product();

			return true;
		}

		return false;
	}

	/**
	 * Before inserting a new dataset into the deal's $_data property,
	 * restore any previously associated product's sale's pricing backed up
	 * data and clear it's 'dotw_wc_product_deal_num' meta flag.
	 *
	 * @since   1.0.0
	 *
	 * @param   array 	$dataset 	(Required) The set of data about to be processed by set_data().
	 *
	 * @return  void
	 */
	protected function _before_set_data( $dataset )
	{
		// If the deal already points to a product which is different than the one about to be set.
		if ( ! $this->is_blank() && strcmp( $dataset['title'], $this->get( 'title' ) ) ) {
			// Unset the dotw metadata flag for this deal on the (soon) previously linked product.
			$product_deal_nums = $this->get( 'product_deal_nums' );
			$unset_indexes     = array_keys( $product_deal_nums, $this->get( 'week_num' ) );

			foreach ($unset_indexes as $unset_ind) {
				unset( $product_deal_nums[$unset_ind] );
			}
			update_post_meta( $this->get( 'product_id' ), 'dotw_wc_product_deal_nums', $product_deal_nums );

			if ( $this->is_active() ) {
				// Restore backed up sale's pricing infos if new deal is about another product than the previously linked one.
				$this->update_product( true );
			}
			// Reinitialize the object's dataset.
			$this->_data = $this->_get_default_dataset( $this->_week_num );
		} // End if not blank and not the same.
	}

	/**
	 * Get method.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $key  (Required) The key of the value to retrieve from the
	 *                        [MC_Dotw_Deal->$_data] associative array.
	 *
	 * @return  mixed         The value of $key.
	 */
	public function get( $key )
	{
		if ( in_array( $key, array( 'regular_price', 'sale_price', 'date_from', 'date_to') ) ) {
			return $this->_data['backup'][$key];

		} elseif ( ! strcmp( $key, 'product_brand' ) ) {
			return $this->get_product_brand();

		} elseif ( ! strcmp( $key, 'product_deal_nums' ) ) {
			return self::get_product_deal_nums();

		} elseif ( isset( $this->_data[$key] ) ){
			return $this->_data[$key];
		}
	}

	/**
	 * Get the deal's entire data set.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return  array  Associative array containing all the deal's data.
	 */
	public function get_data()
	{
		return $this->_data;
	}

	/**
	 * Get a (blank) default deal's data set to populate empty deals.
	 *
	 * @since   1.0.0
	 *
	 * @param   int     $week_num  (Required) The week number of the deal for which a blank dataset is created.
	 *
	 * @return  array              Associative array. The default dataset for the requested $week_num.
	 */
	public static function get_default_dataset( $week_num )
	{
		return array(
			'week_num' 	 => $week_num,
			'product_id' => 0,
			'title' 	 => '',
			'hot_price'  => '',
			'is_active'  => false,
			'backup'     => array(
				'regular_price' => '',
				'sale_price'    => '',
				'date_from'     => '',
				'date_to'       => '',
				),
			);
	}

	/**
	 * Non-static proxy to self::get_default_dataset()
	 *
	 * @since   1.0.0
	 *
	 * @param   int     $week_num  (Optional) The week number of the deal for which a blank dataset is created.
	 *
	 * @return  array              Associative array. The default dataset for the requested $week_num.
	 */
	public function _get_default_dataset( $week_num=0 )
	{
		$week_num = (0 === $week_num) ? $this->get( 'week_num' ) : $week_num;

		return self::get_default_dataset( $week_num );
	}

	/**
	 * Get default Deal datasets for a whole year.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return  array 	$datasets   Array of default deals datasets.
	 */
	public static function get_default_datasets()
	{
		$datasets = array();

		for ($i=0; $i < 53; $i++) {
			$datasets[] = self::get_default_dataset( $i+1 );
		}

		return $datasets;
	}

	/**
	 * Get Deal datasets for a whole year, populated with the data already saved.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return  array 	$datasets   Array of populated deals datasets.
	 */
	public static function get_populated_datasets()
	{
		$options = MC_Dotw_Admin::get_instance()->get_options();

		$saved_deals = $options['deals'];
		$datasets  	 = self::get_default_datasets();

		foreach ($saved_deals as $i => $saved_deal) {
			if ( ! empty( $saved_deal['title'] ) ) {
				$datasets[$i] = $saved_deal;
			}
		}

		return $datasets;
	}

	/**
	 * Get a MC_Dotw_Product instance for product this deal promotes.
	 *
	 * @since   1.0.0
	 *
	 * @param 	void.
	 *
	 * @return  MC_Dotw_Product  The promoted product's MC_Dotw_Product instance.
	 */
	public function get_the_product()
	{
		return $this->product;
	}

	/**
	 * Get a product's meta data.
	 *
	 * @since   1.0.0
	 *
	 * @param   int     $pid  (Optional) Product's unique ID. Defaults to the id of
	 *                        the product linked to the current MC_Dotw_Deal instance.
	 *
	 * @return  array         All the meta keys stored for the product.
	 */
	public function get_product_meta( $pid=0 )
	{
		$id = (int) $pid ? : $this->get( 'product_id' );

		return get_post_meta( $id );
	}

	/**
	 * Get the term from the 'product_brand' taxonomy associated
	 * with the deal's product.
	 *
	 * @since   1.0.0
	 *
	 * @param void
	 *
	 * @return WP_Term A WP_Term from the 'product_brand' taxonomy.
	 */
	protected function get_product_brand()
	{
		return wp_get_post_terms( $this->get( 'product_id' ), 'product_brand' )[0];
	}

	/**
	 * Get a CalendR\Calendar\Week object for the deal's active week,
	 * aswell as 2 dateTime objects corresponding to the deal's
	 * start/end timestamps.
	 *
	 * @since   1.0.0
	 *
	 * @param   int     $_week_num  (Optional) The week number of the week for which the
	 *                              object will be constructed. Defaults to the current MC_Dotw_Deal
	 *                              object's week number.
	 *
	 * @return  array               Associative array containing the dates, referenced by these keys:
	 *                                          (CalendR\Calendar\Week) 'week'
	 *                                          (DateTime)				'start'
	 *                                          (DateTime)				'end'
	 */
	public function get_calendar_objects( $_week_num=0 )
	{
		$calendar_factory = new CalendR\Calendar;
		$options = MC_Dotw_Admin::get_instance()->get_options();

		$week_num = ( 0 === $_week_num ) ? $this->get( 'week_num' ) : (int) $_week_num;

		$week       = $calendar_factory->getWeek( date('Y'), $week_num );
		$week_start = $week->getBegin();
        $week_end   = $week->getEnd();

        /*
	     * From Monday to <endofweek_offset> option:
	     * Apply the option to remove a few days in order to avoid mentioning days-off (sunday)
	     * or overlapping on the next week's first day.
	     */
	    $week_end->sub(
	    	date_interval_create_from_date_string(
	    		$options['settings']['endofweek_offset'] . ' days'
	    	)
	    );

	    return array(
	    	'week'  => $week,
	    	'start' => $week_start,
	    	'end'   => $week_end
	    );
	}

	/**
	 * Get the week numbers of all the deals promoting this deal's product.
	 *
	 * @since   1.0.0
	 *
	 * @param   int     $pid  (Not used) Product id. Default $this->_data['product_id']
	 *
	 * @return  array         Array of week numbers.
	 */
	public function get_product_deal_nums( $pid=0 )
	{
		return $this->product ? $this->product->get_deal_nums() : array();
	}

	/**
	 * Get the sale dates displayed in the deal's admin panel form fields.
	 *
	 * @since   1.0.0
	 *
	 * @return  array  Associative array. Array keys are the following:
	 *                             'date_from' & 'date_to' => values to display
	 *                             'is_backup'
	 */
	public function get_optionsform_datefields_val( $force_fromMeta = true )
	{
		$date_from__from_deal   = strtotime( $this->get( 'date_from' ) );
		$date_from__from_wcmeta = $this->get_product_meta()['_sale_price_dates_from'][0];

		$date_to__from_deal     = strtotime( $this->get( 'date_to' ) );
		$date_to__from_wcmeta   = $this->get_product_meta()['_sale_price_dates_to'][0];

		$date_from = $date_from__from_wcmeta;
		$date_to   = $date_to__from_wcmeta;

		/**
		 * Add or remove FALSE to this conditional in order display hide or show the deal's
		 * backed up original sale dates.
		 */
		if ( ! $force_fromMeta && $this->has_product_active_elsewhere() ) {
			$date_from = $date_from__from_deal;
			$date_to   = $date_to__from_deal;
		}

		$date_from = $date_from ? strftime( '%Y-%m-%d', $date_from ) : '';
		$date_to   = $date_to   ? strftime( '%Y-%m-%d', $date_to )   : '';

		return array(
			'date_from' => $date_from,
			'date_to'   => $date_to,
			'is_backup' => $this->has_activity()
			);
	}

	/**
	 * Check if the deal is empty.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return bool True if blank.
	 */
	public function is_blank()
	{
		return empty( $this->get( 'title' ) );
	}

	/**
	 * Check if the deal is active.
	 * An activated deal is one who's week is the current one
	 * AND who's been set on sale for that week's period thanks to MC_Dotw_Cron.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return bool True if active.
	 */
	public function is_active()
	{
		// This status flag is set and handled by MC_Dotw_Cron->scheduled_deals().
		return $this->get( 'is_active' ) === true || ! strcmp( $this->get( 'is_active' ), 'true' );
	}

	public function has_product_active_elsewhere()
	{
		return $this->product
			? $this->product->has_active_deal() && $this->product->get( 'active_deal_num' ) != $this->get( 'week_num' )
			: false;
	}

	public function has_activity()
	{
		return $this->is_active() || $this->has_product_active_elsewhere();
	}

	/**
	 * Check if a deal is the one associated to the current week.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return  bool  True if the deal is the current week's deal.
	 */
	public function is_current()
	{
		return $this->_week_num == date( 'W' );
	}

	/**
	 * Check if the submitted deal dataset is empty or not.
	 *
	 * @since   1.0.0
	 *
	 * @param   array  $post_data  An associative MC_Dotw_Deal dataset array. Usualy, this function gets
	 *                             this array from the submitted options page admin form or from this
	 *                             plugin's CRON class. If not passed as argument, the post data is
	 *                             retrieved from the server's $_POST.
	 *
	 * @return  bool               True if all the keys of the $post_data array have empty values.
	 */
	protected function is_being_cleared( $post_data=array() )
	{
		if ( count( $post_data ) ) {
			$is_being_cleared = empty( $post_data['title'] )
				&& empty( $post_data['hot_price'] )
				&& empty( $post_data['backup']['regular_price'] )
				&& empty( $post_data['backup']['sale_price'] )
				&& empty( $post_data['backup']['date_from'] )
				&& empty( $post_data['backup']['date_to'] );
		} else {
			$is_being_cleared = empty( $_POST['dotw_deals_'.$this->get( 'week_num' )] )
				&& empty( $_POST['dotw_deals_'.$this->get( 'week_num' ).'_hot_price'] )
				&& empty( $_POST['dotw_deals_'.$this->get( 'week_num' ).'_regular_price'] )
				&& empty( $_POST['dotw_deals_'.$this->get( 'week_num' ).'_sale_price'] )
				&& empty( $_POST['dotw_deals_'.$this->get( 'week_num' ).'_date_from'] )
				&& empty( $_POST['dotw_deals_'.$this->get( 'week_num' ).'_date_to'] );
		}

		return $is_being_cleared;
	}

	/**
	 * Validate data input.
	 *
	 * @since   1.0.0
	 *
	 * @param   string  $key  (Required) The key from $this->_data to validate the input for.
	 * @param   mixed   $val  (Required) The value about to be validated.
	 *
	 * @return  mixed         False if input is not valid. The sanitized value ortherwise.
	 */
	protected function validate( $key, $val )
	{
		$valid_input = false;

		switch ($key) {
			case 'week_num':
				$valid_input = is_numeric( $val ) && (int) $val > 0 && (int) $val <= 53
					? (int) $val
					: $valid_input;
				break;
			case 'product_id':
				$valid_input = is_numeric( $val ) && (int) $val > 0
					? (int) $val
					: $valid_input;
				break;
			case 'title':
				$valid_input = is_string( $val )
					? trim( $val )
					: $valid_input;
				break;
			case 'hot_price':
				$valid_input = is_numeric( $val ) && (int) $val > 0
					? (int) $val
					: $valid_input;
				break;
			case 'is_active':
				$valid_input = true === $val || 'true' === $val
					? true
					: $valid_input;
				break;
			case 'backup':
				$valid_input = is_array( $val )
					? $val
					: $valid_input;

				// Validate array elements as well.
				if ( $valid_input ) {
					$valid_input = isset( $val['regular_price'] ) //&& is_numeric( $val['regular_price'] ) && (int) $val['regular_price'] >= 0
						? $val
						: false;
					$valid_input = isset( $val['sale_price'] ) //&& is_numeric( $val['sale_price'] ) && (int) $val['sale_price'] >= 0
						? $val
						: false;
					$valid_input = isset( $val['date_from'] )
						? $val
						: false;
					$valid_input = isset( $val['date_to'] )
						? $val
						: false;
				}
				break;
			case 'product':
				$valid_input = $val instanceof MC_Dotw_Product
					? $val
					: $valid_input;
				break;
		}

		return $valid_input;
	}

	/**
	 * Validate form submitted data related to the current deal instance OR the dataset passed in as
	 * the optional 1st argument (related to the deal of the week who's number is passed as a 2nd argument).
	 *
	 * Function calls should use 'none' or 'both' arguments. Avoid passing only one argument to this function
	 * as the validation would lack proper ways to ever return a positive result.
	 *
	 * @since   1.0.0
	 *
	 * @param 	array 	$dataset 	(Optional) An optional deal dataset to validate. If absent,
	 *                         		the function will parse the server's $_POST for
	 *                           	data submitted through the admin form.
	 * @param   int 	$week_num 	(Optional) When passing a dataset as first argument, specify
	 *                          	the related week's number using this second argument.
	 *
	 * @return  boolean  			True if all submitted data is valid, false if not.
	 */
	public function validate_post_data( $dataset=array(), $week_num=0 )
	{
		$is_valid_post = true;

		// Get data from the server's $_POST (form submition) if it was not passed as argument.
		if ( empty( $dataset ) ) {
			$post_data = array(
	            'title'      => $_POST['dotw_deals_'.$this->get( 'week_num' )],
				'product_id' => $_POST['dotw_deals_'.$this->get( 'week_num' ).'_product_id'],
	            'hot_price'  => $_POST['dotw_deals_'.$this->get( 'week_num' ).'_hot_price'],
	            'backup'     => array(
	            	'regular_price' => $_POST['dotw_deals_'.$this->get( 'week_num' ).'_regular_price'],
	                'sale_price'    => $_POST['dotw_deals_'.$this->get( 'week_num' ).'_sale_price'],
	                'date_from'     => $_POST['dotw_deals_'.$this->get( 'week_num' ).'_date_from'],
	                'date_to'       => $_POST['dotw_deals_'.$this->get( 'week_num' ).'_date_to'],
	            )
			);
		} else {
			$post_data = shortcode_atts( $this->_get_default_dataset(), $dataset );
		}

		$is_being_cleared = $this->is_being_cleared( $post_data );
		$post_data        = $this->parse_before_validate( $post_data, $is_being_cleared );

		if ( ! $is_being_cleared ){
	        // A product title was selected for the deal OR the deal is being cleared to all blank values.
	        $is_valid_post = $is_valid_post && ( ! empty( $post_data['title'] ) );

	        // A matching product was found in the wc catalogue.
	        $is_valid_post = $is_valid_post && ! empty( $post_data['product_id'] );

	        // Deal has, at least, a hot_price or a sale_price.
	        $is_valid_post = $is_valid_post
	            && (
	                ! empty( $post_data['hot_price'] )
	             || ! empty( $post_data['backup']['sale_price'] )
	            );

	        // If there's a sale price, it should be lower than the regular price.
	        $is_valid_post = $is_valid_post
	            && (
	                empty( $post_data['backup']['sale_price'] )
	                || $post_data['backup']['sale_price'] < $post_data['backup']['regular_price']
	            );

	        // If there's a hot price and a sale_price, hot_p should be lower than sale_p.
	        $is_valid_post = $is_valid_post
	            && (
	                (
	                    empty( $post_data['hot_price'] )
	                    || empty( $post_data['backup']['sale_price'] )
	                )
	                || $post_data['hot_price'] < $post_data['backup']['sale_price']
	            );

	        // If there's a hot price and no sale_price, it should be lower than the regular price.
	        $is_valid_post = $is_valid_post
	            && (
	                empty( $post_data['hot_price'] )
	                || $post_data['hot_price'] < $post_data['backup']['regular_price']
	            );

	        // If there's both date_from and date_to, date_to should be greater than date_from.
	        $is_valid_post = $is_valid_post
	            && (
	                (
	                    empty( $post_data['backup']['date_from'] )
	                    || empty( $post_data['backup']['date_to'] )
	                )
	                || strtotime($post_data['backup']['date_from']) < strtotime($post_data['backup']['date_to'])
	            );

	        // There should always be a regular price.
	        $is_valid_post = $is_valid_post
	            && ! empty( $post_data['backup']['regular_price'] ) ;
		}

        return $is_valid_post || $is_being_cleared ? $post_data : false;
	}

	/**
	 * Check whether or not preserved data  about a product needs to be used before
	 * validating data submitted to validate_post_data().
	 *
	 * An active deal's product is always on sale + it's sale_price/dateTimes have been
	 * temporarly overridden by the ones set by the deal of the week config.
	 * These values should not be used for original pricing backup purposes!
	 *
	 * The active deal's sale's pricing and dates submitted through the form will be
	 * discarded and the backed up data preserved by using it here instead of the $_POST data.
	 *
	 * @since   1.0.0
	 *
	 * @param array $post_data 			An associative array formatted and fit w/ all deal's dataset keys/values.
	 * @param bool  $is_being_cleared 	True if the submitted array is empty enough to imply that the deal
	 *                                  is being cleared of all it's previous values, back to a blank state.
	 *
	 * @return array 					The parsed deal dataset array.
	 */
	protected function parse_before_validate( $post_data, $is_being_cleared )
	{
		if ( ! $is_being_cleared && ( isset( $post_data['product_id'] ) && $post_data['product_id'] > 0 ) ) {
			$product = MC_Dotw_Product::get_product_by_id( $post_data['product_id'] );

			// Note: Here, we can't use $this->has_product_active_elsewhere() because
			// the submitted product doesn't yet belong to $this!!!
			$is_submitted_product_active_elsewhere = $product->get( 'active_deal_num' ) > 0
				&& $product->get( 'active_deal_num' ) != $this->get( 'week_num' );

			$is_active_and_not_blank = $this->is_active() && ! $this->is_blank();

			if ( $is_active_and_not_blank || $is_submitted_product_active_elsewhere ) {
				$preserved_data_src = $is_submitted_product_active_elsewhere
					? $product->get_active_deal()
					: $this->get_data();

				$post_data['backup']['regular_price'] = $preserved_data_src['backup']['regular_price'];
				$post_data['backup']['sale_price']    = $preserved_data_src['backup']['sale_price'];
				$post_data['backup']['date_from']     = $preserved_data_src['backup']['date_from'];
				$post_data['backup']['date_to']       = $preserved_data_src['backup']['date_to'];
			}
		}

		return $post_data;
	}

	/**
	 * Update wc product with the week's hot sale pricing data
	 * or to restore a product's backed up pricing infos.
	 *
	 * @since   1.0.0
	 *
	 * @param   bool   $reset_bkp  (Optional) True to restore sale pricing backup. Defaults to false.
	 *
	 * @return  void
	 */
	public function update_product( $reset_bkp=false )
	{
		$post_id = $this->get( 'product_id' );

		if ( $post_id ) {
			$update_data   = $this->_get_product_update_data( $reset_bkp );

			$regular_price = $update_data['regular_price'];
			$sale_price    = $update_data['sale_price'];
			$date_from     = $update_data['date_from'];
			$date_to       = $update_data['date_to'];
			update_post_meta( $post_id, '_regular_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
			update_post_meta( $post_id, '_sale_price', '' === $sale_price ? '' : wc_format_decimal( $sale_price ) );
			// Dates
			update_post_meta( $post_id, '_sale_price_dates_from', $date_from ? strtotime( $date_from ) : '' );
			update_post_meta( $post_id, '_sale_price_dates_to', $date_to ? strtotime( $date_to ) : '' );

			if ( $date_to && ! $date_from ) {
				$date_from = date( 'Y-m-d' );
				update_post_meta( $post_id, '_sale_price_dates_from', strtotime( $date_from ) );
			}

			// Update price if on sale
			if ( '' !== $sale_price && '' === $date_to && '' === $date_from ) {
				update_post_meta( $post_id, '_price', wc_format_decimal( $sale_price ) );
			} elseif ( '' !== $sale_price && $date_from && strtotime( $date_from ) <= strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				update_post_meta( $post_id, '_price', wc_format_decimal( $sale_price ) );
			} else {
				update_post_meta( $post_id, '_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
			}

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				update_post_meta( $post_id, '_price', '' === $regular_price ? '' : wc_format_decimal( $regular_price ) );
				update_post_meta( $post_id, '_sale_price', '' );
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );
			}

			if ( ! $reset_bkp ) {
				$this->_after_update_product();
			}
		} //End if $post_id
		else {}
	}

	 /**
	  * Get the appropriated values used to update a product.
	  * Dates of sale pricing will vary depending on wether
	  * or not the deal is the currently active one and
	  * wether or not it's linked to another deal that's currently active.
	  * Also allows to restore a product's backed up data.
	  *
	  * @since   1.0.0
	  *
	  * @param   bool    $reset_bkp (Optional) True to restore all backed up data. Defaults to false.
	  *
	  * @return  array              Associative array of product fields keys/values.
	  */
	protected function _get_product_update_data( $reset_bkp=false )
	{
		$options = MC_Dotw_Admin::get_instance()->get_options();

		$data = array();

		if ( $reset_bkp ) {
			// Restore the wc_product pricing data to the values it had before
			// the product being promoted/priced as deal of the week.
			$data['regular_price'] = (string) isset( $this->_data['backup']['regular_price'] )

				? wc_clean( $this->_data['backup']['regular_price'] )
				: '';

			$data['sale_price'] = (string) isset( $this->_data['backup']['sale_price'] )

				? wc_clean( $this->_data['backup']['sale_price'] )
				: '';

			$data['date_from'] = (string) isset( $this->_data['backup']['date_from'] )

				? wc_clean( $this->_data['backup']['date_from'] )
				: '';

			$data['date_to'] = (string) isset( $this->_data['backup']['date_to'] )

				? wc_clean( $this->_data['backup']['date_to'] )
				: '';
		} else {
			/*
			 * Get proper product sale dates and price when not in restore/reset backup mode.
			 *
			 * When submitting form data, wc_product's sale dates/price are only modified for a deal
			 * currently active or for a deal who's promoted product is also promoted by another deal
			 * that is currently active, leaving all other promotion dates in their usual state,
			 * untouched until necessary.
			 */

			// Get product sale start/end dates.
			//
			// Default to the product's usual sale dates/price, (backed up by any deal refering to it).
			$week_start = wc_clean( $this->get( 'date_from' ) );
			$week_end 	= wc_clean( $this->get( 'date_to' ) );
			$sale_price = wc_clean( $this->get( 'sale_price' ) );

			// When the product is currently promoted, either by this deal instance or by a remote deal:
			if ( $this->has_activity() ) {
				// If the referenced product's activity comes from this deal instance.
				if ( $this->is_active() ) {
					// Load the calendR objects from the current MC_Dotw_deal instance.
					$week_cal   = $this->get_calendar_objects();

					// If the deal has a hot price setting, sale price is the hot price.
					// Otherwise, sale price is the usual product sale price.
					$sale_price = $this->get( 'hot_price' )
						? wc_clean( $this->get( 'hot_price' ) )
						: $sale_price;

				} else {
					// If activity comes from another deal, get it's calendR objects and sale price instead.
					$remote_deal = new MC_Dotw_Deal( $this->product->get_active_deal()['week_num'] );
					$week_cal    = $remote_deal->get_calendar_objects();
					$sale_price  = $remote_deal->get( 'hot_price' )
						? wc_clean( $remote_deal->get( 'hot_price' ) )
						: $sale_price;
				}
				// Set the update data's sale dates using the properly set calendaR objects.
				$week_start = wc_clean( $week_cal['start']->format( 'Y-m-d' ) );
				$week_end   = wc_clean( $week_cal['end']->format( 'Y-m-d' ) );
			}

			// Set the update data.
			$data['sale_price']    = $sale_price;
			$data['date_from']     = (string) $week_start;
			$data['date_to']       = (string) $week_end;
			$data['regular_price'] = (string) $this->get( 'regular_price' )
				? wc_clean( $this->get( 'regular_price' ) )
				: '';
		}

		return $data;
	}

	/**
	 * After updating a linked product in non-reset mode ($reset_bkp=false),
	 * update the current Deal instance product attribute as well as the product's
	 * dotw meta key to flag the link and enable later syncing.
	 *
	 * @since   1.0.0
	 *
	 * @param   void
	 *
	 * @return  void
	 */
	protected function _after_update_product()
	{
		// update_post_meta( $this->_data['product_id'], 'dotw_wc_product_deal_num', $this->get('week_num') );
		$product_deal_nums  = $this->get( 'product_deal_nums' );
		$this_week_num      = $this->get( 'week_num' );
		$existing_refs_keys = array_keys( $product_deal_nums, $this_week_num );

		if ( empty( $existing_refs_keys ) ) {
			$product_deal_nums[] = $this_week_num;
		}

		update_post_meta( $this->get( 'product_id' ), 'dotw_wc_product_deal_nums', $product_deal_nums );
	}

	/**
	 * Save changes to the deal.
	 *
	 * @since   1.0.0
	 *
	 * @param   array   $_dataset   (Optional) New dataset containing the updated values. Defaults to the current
	 *                              MC_Dotw_Deal instance's [$_data] dataset.
	 * @param   bool    $validated  (Optional) Has the dataset already been through data validation checks.
	 *                              If not, the function will take care of sanitizing the data. Defaults to false.
	 * @param   bool 	$update_wc 	(Optional) Boolean flag triggering the associated product's update when set to true.
	 *                            	Defaults to false.
	 *
	 * @return  Void.
	 */
	public function save($_dataset=array(), $validated=false, $update_wc=false)
	{
		$options = MC_Dotw_Admin::get_instance()->get_options();
		$dataset = empty( $_dataset ) ? $this->get_data() : $_dataset;
		$dataset = $validated || empty( $_dataset ) ? $dataset : $this->validate_post_data( $dataset );

		// Append this deal's persisted 'is_active' status to the data if no override value is supplied.
        $dataset['is_active'] = isset( $dataset['is_active'] )
        	? $dataset['is_active']
        	: $this->is_active();

        // Populate our Deal Object '_data' property with the new data.
        if ( ! empty( $_dataset ) ) {
        	$this->set_data( $dataset );
        }

        // Update deals option clone with new values and update dotw's option.
        $options['deals'][$this->get( 'week_num' )-1] = $dataset;

        update_option( 'mc_dotw', $options );

        // Update selected wc product only if requested.
        if ( $update_wc ) {
        	// Log some debug/dev msg ?
        }
	}

	/**
	 * Get a new MC_Dotw_Deal instance.
	 *
	 * @since   1.0.0
	 *
	 * @param   int     	$_week_num  (Optional) Number between 1 and 53 identifying the week
	 *                                  of the year during which the deal will be applied. When
	 *                                  passed 0 (or less) as argument, the function returns the
	 *                                  current week's deal.
	 *
	 * @return  MC_Dotw_Deal        		MC_Dotw_Deal instance associated to the week [$week_num].
	 */
	public static function new_deal( $_week_num=0 )
	{
		$week_num = 0 == $_week_num
			? date( 'W' )
			: $_week_num;

		return new self( (int) $week_num );
	}
}
