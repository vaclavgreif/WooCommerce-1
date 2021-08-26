<?php
/**
 * Class PacketeryLatte_Engine_Factory
 *
 * @package Packetery
 */

declare( strict_types=1 );


namespace Packetery;

use PacketeryLatte\Engine;
use PacketeryNette\Bridges\FormsPacketeryLatte\FormMacros;

/**
 * Class PacketeryLatte_Engine_Factory
 *
 * @package Packetery
 */
class LatteEngineFactory {

	/**
	 * Creates latte engine factory
	 *
	 * @param string $temp_dir Temporary folder.
	 *
	 * @return Engine
	 */
	public function create( string $temp_dir ) {
		$engine = new Engine();
		$engine->setTempDirectory( $temp_dir );
		FormMacros::install( $engine->getCompiler() );
		return $engine;
	}
}
