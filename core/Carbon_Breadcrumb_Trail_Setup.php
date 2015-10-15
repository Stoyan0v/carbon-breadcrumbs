<?php
/**
 * The main breadcrumb trail setup class.
 * 
 * Generates and populates the default breadcrumb trail items.
 */
class Carbon_Breadcrumb_Trail_Setup {

	/**
	 * Breadcrumb trail.
	 *
	 * @access protected
	 * @var Carbon_Breadcrumb_Trail
	 */
	protected $trail;

	/**
	 * Constructor.
	 *
	 * Hooks all breadcrumb trail item population methods.
	 *
	 * @access public
	 *
	 * @param Carbon_Breadcrumb_Trail $trail Trail object to populate.
	 */
	public function __construct( Carbon_Breadcrumb_Trail $trail ) {
		$this->set_trail( $trail );

		$this->populate_db_object_items();
		$this->populate_date_archive_items();
		$this->populate_search_items();
		$this->populate_404_items();
		$this->populate_category_items();
		$this->populate_page_for_posts_items();
		$this->populate_home_items();
	}

	/**
	 * Populate DB object items (post types, terms, authors).
	 *
	 * @access public
	 */
	public function populate_db_object_items() {
		$locators = array(
			'post',
			'term',
			'user',
		);
		
		foreach ( $locators as $locator_name ) {
			$items = $this->generate_locator_items( $locator_name );
			$this->get_trail()->add_item( $items );	
		}
	}

	/**
	 * Populate date archives.
	 *
	 * @access public
	 */
	public function populate_date_archive_items() {
		if ( ! is_date() ) {
			return;
		}

		$locator = Carbon_Breadcrumb_Locator::factory( 'date' );
		$items = $locator->get_items( 700 );
		$this->get_trail()->add_item( $items );
	}

	/**
	 * Populate search items.
	 *
	 * @access public
	 */
	public function populate_search_items() {
		if ( ! is_search() ) {
			return;
		}

		$search_title = sprintf( __( 'Search results for: "%1$s"', 'carbon_breadcrumbs' ), get_search_query() );
		$this->get_trail()->add_custom_item( $search_title, '', 700 );
	}

	/**
	 * Populate 404 items.
	 *
	 * @access public
	 */
	public function populate_404_items() {
		if ( ! is_404() ) {
			return;
		}

		$not_found_title = __( 'Error 404 - Not Found', 'carbon_breadcrumbs' );
		$this->get_trail()->add_custom_item( $not_found_title, '', 700 );
	}

	/**
	 * Populate category hierarchy when on a single post.
	 *
	 * @access public
	 */
	public function populate_category_items() {
		if ( ! ( is_single() && 'post' == get_post_type() ) ) {
			return;
		}

		$taxonomy = 'category';
		$categories = wp_get_object_terms( get_the_ID(), $taxonomy, 'orderby=term_id' );
		$last_category = array_pop( $categories );
		$locator = Carbon_Breadcrumb_Locator::factory( 'term', $taxonomy );
		$items = $locator->get_items( 700, $last_category->term_id );
		$this->get_trail()->add_item( $items );
	}

	/**
	 * Populate page for posts item where necessary.
	 *
	 * @access public
	 */
	public function populate_page_for_posts_items() {
		$page_for_posts = get_option( 'page_for_posts' );
		if ( ! $page_for_posts ) {
			return;
		}

		if ( is_home() || is_category() || is_tag() || is_date() || is_author() || ( is_single() && 'post' == get_post_type() ) ) {
			$locator = Carbon_Breadcrumb_Locator::factory( 'post', 'page' );
			$items = $locator->get_items( 500, $page_for_posts );
			$this->get_trail()->add_item( $items );
		}
	}

	/**
	 * Populate home item.
	 *
	 * @access public
	 */
	public function populate_home_items() {
		$trail = $this->get_trail();
		$renderer = $trail->get_renderer();
		if ( ! $renderer->get_display_home_item() ) {
			return;
		}

		$home_title = $renderer->get_home_item_title();
		$home_link = home_url( '/' );
		$trail->add_custom_item( $home_title, $home_link, 10 );
	}

	/**
	 * Generate the items of a certain locator.
	 *
	 * @access protected
	 *
	 * @param string $locator_name Name of the locator.
	 * @return array $items Items generated by this locator.
	 */
	protected function generate_locator_items( $locator_name ) {
		$locator = Carbon_Breadcrumb_Locator::factory( $locator_name );
		$items = $locator->generate_items();

		if ( $items ) {
			return $items;
		}

		return array();
	}

	/**
	 * Retrieve the trail object.
	 *
	 * @access public
	 *
	 * @return Carbon_Breadcrumb_Trail $trail The trail object.
	 */
	public function get_trail() {
		return $this->trail;
	}

	/**
	 * Modify the trail object.
	 *
	 * @access public
	 *
	 * @param Carbon_Breadcrumb_Trail $trail The modified rendering object.
	 */
	public function set_trail( Carbon_Breadcrumb_Trail $trail ) {
		$this->trail = $trail;
	}

}