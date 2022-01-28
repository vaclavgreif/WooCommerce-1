<?php
/**
 * Packeta shipping method class.
 * @package Packetery
 */

declare( strict_types=1 );

namespace Packetery\Module;

use Packetery\Module\Carrier\Repository;

/**
 * Packeta shipping method class.
 */
class ShippingMethod extends \WC_Shipping_Flat_Rate {

	public const PACKETERY_METHOD_ID = 'packetery_shipping_method';

	private $options;

	private $container;

	/**
	 * Checkout object.
	 * @var Checkout
	 */
	private $checkout;

	/**
	 * Constructor for Packeta shipping class
	 *
	 * @param int $instance_id Shipping method instance id.
	 */
	public function __construct( int $instance_id = 0 ) {
		parent::__construct();
		$this->id                 = self::PACKETERY_METHOD_ID;
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'Packeta Shipping Method', 'packetery' );
		$this->title              = __( 'Packeta Shipping Method', 'packetery' );
		$this->method_description = __( 'Description of Packeta shipping method', 'packetery' );
		$this->enabled            = 'yes'; // This can be added as an setting but for this example its forced enabled.
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',

		);
		$this->init();

		$this->container = CompatibilityBridge::getContainer();
		$this->checkout  = $this->container->getByType( Checkout::class );

		add_filter( 'woocommerce_shipping_' . $this->id . '_instance_settings_values', [ $this, 'save_custom_settings' ], 10, 2 );
		$this->options = get_option( sprintf( 'woocommerce_%s_%s_settings', $this->id, $this->instance_id ) );
	}

	/**
	 * Return admin options as a html string.
	 * @return string
	 */
	public function get_admin_options_html() {
		if ( $this->instance_id ) {
			$settings_html = $this->generate_settings_html( $this->get_instance_form_fields(), false );
		} else {
			$settings_html = $this->generate_settings_html( $this->get_form_fields(), false );
		}

		/** @var Repository $repo */
		$repo = $this->container->getByType( Repository::class );

		ob_start(); ?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_packetery_shipping_method_title">Shipping Method <span class="woocommerce-help-tip"></span></label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Název metody</span></legend>
						<select name="packetery_shipping_method" id="">
							<?php foreach ( $repo->getAllIncludingZpoints() as $item ) {
								var_dump( $this->options['packetery_shipping_method'], $item['id'] );
								?>
								<option value="<?php echo $item['id']; ?>" <?php selected( $this->options['packetery_shipping_method'], $item['id'] ) ?>><?php echo $item['name']; ?></option>
							<?php } ?>
						</select>
					</fieldset>
				</td>
			</tr>
			<?php echo $settings_html; ?>
		</table>
		<?php
		return ob_get_clean();
	}


	/**
	 * Function to calculate shipping fee.
	 * Triggered by cart contents change, country change.
	 *
	 * @param array $package Order information.
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = [] ): void {
		$customRates = $this->checkout->getShippingRates();
		foreach ( $customRates as $customRate ) {
			$this->add_rate( $customRate );
		}
	}

	public function save_custom_settings( $settings ) {
		$settings['packetery_shipping_method'] = $_POST['data']['packetery_shipping_method'];

		return $settings;
	}
}
