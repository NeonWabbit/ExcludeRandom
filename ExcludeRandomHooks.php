<?php

use MediaWiki\MediaWikiServices;

class ExcludeRandomHooks {
	public static function onSpecialRandomGetRandomTitle( &$rand, &$isRedir, &$namespaces, &$extra, &$title ) {
		// Get the main config from MediaWikiServices
		$config = MediaWikiServices::getInstance()->getMainConfig();

		// Retrieve the array of pages to exclude from random selection
		$wgExcludeRandomPages = $config->get( 'ExcludeRandomPages' );
		if ( !is_array( $wgExcludeRandomPages ) || empty( $wgExcludeRandomPages ) ) {
			return true;
		}

		// Replaces deprecated wfGetDB()
		$lb = MediaWikiServices::getInstance()->getDBLoadBalancer();
		$db = $lb->getConnection( DB_REPLICA );

		foreach ( $wgExcludeRandomPages as $cond ) {
			$pattern = $db->strencode( $cond );
			$pattern = str_replace(
				[ '_', '%', ' ', '*' ],
				[ '\_', '\%', '\_', '%' ],
				$pattern
			);
			$extra[] = "`page_title` NOT LIKE '$pattern'";
		}

		return true;
	}
}
