<?php
/**
 * 'Deal of the Week' product object.
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

Class MC_Dotw_Product {

	/**
	 * The product (post) ID.
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Stores the deals featuring this product.
	 *
	 * @var array
	 */
	protected $deals = array();

	/**
	 * Stores week number of the associated deal currently activated,
	 * if there is any.
	 *
	 * @var int
	 */
	protected $active_deal_num = 0;

	/**
	 * Constructor get constructs the parent WC_Product class and sets deals data.
	 *
	 * @param int $post_id 	(Required) Unique id of the woocommerce product.
	 *
	 * @return  void
	 */
	public function __construct( $post_id=0 )
	{
		if ( is_numeric( $post_id ) && $post_id ) {
			$this->id = $post_id;
			$this->_set_meta();
			$this->_set_deals();
		}
	}

	protected function _set_meta()
	{
		$this->meta = get_post_meta( $this->get_id() );
	}

	/**
	 * Set $this->$deals, an array of all the deals featuring this product.
	 * Also, flag the activity status by storing potential active deal's week_num.
	 *
	 * @param   void
	 *
	 * @return  void
	 */
	private function _set_deals()
	{
		$options = MC_Dotw_Admin::get_instance()->get_options();

		// Get an array of linked deals week numbers.
		$product_deal_nums = unserialize( $this->get( 'meta' )['dotw_wc_product_deal_nums'][0] );

		// Set this object's 'delals'
		foreach ( $product_deal_nums as $deal_num ) {
			$this->deals[$deal_num] = $options['deals'][$deal_num-1];
		}

		foreach ( $this->get_deals() as $deal_num => $dataset ) {
			if ( $dataset['is_active'] === true || $dataset['is_active'] === 'true' ) {
				$this->active_deal_num = $deal_num;
			}
		}
	}

	public function get( $key, $args=array() )
	{
		if ( ! strcmp( $key, 'id' ) ) {
			return $this->get_id();

		} elseif ( ! strcmp( $key, 'meta' ) ) {
			return $this->meta;

		} elseif ( ! strcmp( $key, 'deals' ) ) {
			return $this->get_deals();

		} elseif ( ! strcmp( $key, 'deal_nums' ) ) {
			return $this->get_deal_nums();

		} elseif ( ! strcmp( $key, 'active_deal' ) ) {
			return $this->get_active_deal();

		} elseif ( ! strcmp( $key, 'active_deal_num' ) ) {
			return $this->active_deal_num;

		} elseif ( ! strcmp( $key, 'timefiltered_deals' ) ) {
			$future = isset( $args['future'] ) ? (bool) $args['future'] : null;
			return $this->get_timefiltered_deals( $future );
		}
	}

	public function get_id()
	{
		return $this->id;
	}

	public function get_deals()
	{
		return $this->deals;
	}

	public function get_deal_nums()
	{
		return array_keys( $this->get_deals() );
	}

	public function get_active_deal()
	{
		$active_deal_dataset = $this->get( 'active_deal_num' ) > 0
			? $this->deals[$this->get( 'active_deal_num' )]
			: array();

		return $active_deal_dataset;
	}

	public function has_active_deal()
	{
		return (bool) count( $this->get_active_deal() );
	}

	public function get_timefiltered_deals( $future=true )
	{
		$filtered = array();

		$deal_matches_condition = $future
			? (int) $deal['week_num'] > date( 'W' )
			: (int) $deal['week_num'] < date( 'W' );

		foreach ($this->get_deals() as $deal) {
			if ( $deal_matches_condition ) {
				$filtered[] = $deal;
			}
		}

		return $filtered;
	}

	/**
	 * Get a new MC_Dotw_Product instance.
	 *
	 * @param   int     		$post_id  (Required) Post id of the Woocommerce product to reference.
	 *
	 * @return  MC_Dotw_Product  	      MC_Dotw_Product instance.
	 */
	public static function get_product_by_id( $post_id=0 )
	{
		if ( is_numeric( $post_id ) && $post_id )
			return new self( (int) $post_id );

		return;
	}
}