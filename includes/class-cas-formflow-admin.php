<?php
/**
 * Admin submissions list page.
 *
 * @package CAS_FormFlow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class CAS_FormFlow_Admin {

	private const MENU_SLUG = 'cas-formflow-submissions';

	/**
	 * Register admin hooks.
	 *
	 * @return void
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	/**
	 * Add plugin page to the WordPress admin sidebar.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'CAS FormFlow Submissions', 'cas-formflow' ),
			__( 'CAS FormFlow', 'cas-formflow' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_submissions_page' ),
			'dashicons-feedback',
			26
		);
	}

	/**
	 * Render submissions list page.
	 *
	 * @return void
	 */
	public function render_submissions_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'cas-formflow' ) );
		}

		$list_table = new CAS_FormFlow_Submissions_List_Table();
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'CAS FormFlow Submissions', 'cas-formflow' ); ?></h1>
			<hr class="wp-header-end">

			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_attr( self::MENU_SLUG ); ?>">
				<?php
				$list_table->display();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Get full submissions table name.
	 *
	 * @return string
	 */
	public static function get_table_name(): string {
		return CAS_FormFlow_Database::get_submissions_table_name();
	}
}

class CAS_FormFlow_Submissions_List_Table extends WP_List_Table {

	private const ITEMS_PER_PAGE = 20;

	/**
	 * Set list table labels.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'cas_formflow_submission',
				'plural'   => 'cas_formflow_submissions',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Prepare data for display.
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns(),
			$this->get_primary_column_name(),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$this->items = array();
			return;
		}

		$per_page     = $this->get_items_per_page( 'cas_formflow_submissions_per_page', self::ITEMS_PER_PAGE );
		$current_page = $this->get_pagenum();
		$total_items  = $this->get_total_items();

		$this->items = $this->get_submissions( $per_page, $current_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => (int) ceil( $total_items / $per_page ),
			)
		);
	}

	/**
	 * Define table columns.
	 *
	 * @return array<string, string>
	 */
	public function get_columns(): array {
		return array(
			'created_at' => __( 'Submitted at', 'cas-formflow' ),
			'name'       => __( 'Name', 'cas-formflow' ),
			'email'      => __( 'Email', 'cas-formflow' ),
			'phone'      => __( 'Phone', 'cas-formflow' ),
		);
	}

	/**
	 * Define sortable table columns.
	 *
	 * @return array<string, array<int, string|bool>>
	 */
	protected function get_sortable_columns(): array {
		return array(
			'created_at' => array( 'created_at', true ),
			'name'       => array( 'first_name', false ),
			'email'      => array( 'email', false ),
			'phone'      => array( 'phone', false ),
		);
	}

	/**
	 * Render default column values.
	 *
	 * @param array<string, mixed> $item Submission row.
	 * @param string              $column_name Column key.
	 * @return string
	 */
	protected function column_default( $item, $column_name ): string {
		switch ( $column_name ) {
			case 'created_at':
				return esc_html( $this->format_submission_date( (string) $item['created_at'] ) );

			case 'name':
				return esc_html( $this->format_submission_name( $item ) );

			case 'email':
				return esc_html( (string) $item['email'] );

			case 'phone':
				return esc_html( (string) $item['phone'] );

			default:
				return '';
		}
	}

	/**
	 * Message shown when there are no submissions.
	 *
	 * @return void
	 */
	public function no_items(): void {
		esc_html_e( 'No submissions found.', 'cas-formflow' );
	}

	/**
	 * Count saved submissions.
	 *
	 * @return int
	 */
	private function get_total_items(): int {
		global $wpdb;

		if ( ! current_user_can( 'manage_options' ) ) {
			return 0;
		}

		$table_name = CAS_FormFlow_Database::get_escaped_submissions_table_name();

		if ( '' === $table_name ) {
			return 0;
		}

		return (int) $wpdb->get_var( "SELECT COUNT(id) FROM {$table_name}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Fetch submissions for the current page.
	 *
	 * @param int $per_page Items per page.
	 * @param int $current_page Current page number.
	 * @return array<int, array<string, mixed>>
	 */
	private function get_submissions( int $per_page, int $current_page ): array {
		global $wpdb;

		if ( ! current_user_can( 'manage_options' ) ) {
			return array();
		}

		$table_name = CAS_FormFlow_Database::get_escaped_submissions_table_name();
		$orderby   = $this->get_orderby();
		$order     = $this->get_order();
		$offset    = ( $current_page - 1 ) * $per_page;

		if ( '' === $table_name ) {
			return array();
		}

		$query = $wpdb->prepare(
			"SELECT id, first_name, phone, email, description, created_at FROM {$table_name} ORDER BY `{$orderby}` {$order} LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$per_page,
			$offset
		);

		$submissions = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! is_array( $submissions ) ) {
			return array();
		}

		return $submissions;
	}

	/**
	 * Get a safe orderby value from the request.
	 *
	 * @return string
	 */
	private function get_orderby(): string {
		$allowed_orderby = array(
			'created_at',
			'first_name',
			'email',
			'phone',
		);

		$orderby = $this->get_request_key( 'orderby', 'created_at' );

		if ( ! in_array( $orderby, $allowed_orderby, true ) ) {
			return 'created_at';
		}

		return $orderby;
	}

	/**
	 * Get a safe order direction from the request.
	 *
	 * @return string
	 */
	private function get_order(): string {
		$order = strtolower( $this->get_request_key( 'order', 'desc' ) );

		if ( ! in_array( $order, array( 'asc', 'desc' ), true ) ) {
			return 'desc';
		}

		return strtoupper( $order );
	}

	/**
	 * Get a scalar sanitized key from the request.
	 *
	 * @param string $key Request key.
	 * @param string $default Default value.
	 * @return string
	 */
	private function get_request_key( string $key, string $default ): string {
		if ( ! isset( $_GET[ $key ] ) || is_array( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $default;
		}

		return sanitize_key( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Format submission date using WordPress date/time settings.
	 *
	 * @param string $created_at Stored date.
	 * @return string
	 */
	private function format_submission_date( string $created_at ): string {
		if ( '' === $created_at ) {
			return '-';
		}

		$formatted = mysql2date(
			get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			$created_at
		);

		return '' !== $formatted ? $formatted : '-';
	}

	/**
	 * Format the saved first and last name.
	 *
	 * @param array<string, mixed> $item Submission row.
	 * @return string
	 */
	private function format_submission_name( array $item ): string {
		$description = $this->decode_description( (string) $item['description'] );
		$first_name  = trim( (string) $item['first_name'] );
		$last_name   = isset( $description['last_name'] ) ? trim( (string) $description['last_name'] ) : '';
		$full_name   = trim( $first_name . ' ' . $last_name );

		return '' !== $full_name ? $full_name : '-';
	}

	/**
	 * Decode JSON description data.
	 *
	 * @param string $description Saved JSON description.
	 * @return array<string, mixed>
	 */
	private function decode_description( string $description ): array {
		if ( '' === $description ) {
			return array();
		}

		$decoded = json_decode( $description, true );

		if ( ! is_array( $decoded ) ) {
			return array();
		}

		return $decoded;
	}
}
