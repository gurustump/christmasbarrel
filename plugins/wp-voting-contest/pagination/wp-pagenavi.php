<?php
include dirname( __FILE__ ) . '/scb/load.php';

function _votingpagenavi_init() {
	//load_plugin_textdomain( 'voting-contest', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );

	require_once dirname( __FILE__ ) . '/core.php';

	$options = new scbOptions( 'contestpagenavi_options', __FILE__, array(
		'pages_text'    => __( 'Page %CURRENT_PAGE% of %TOTAL_PAGES%', 'voting-contest' ),
		'current_text'  => '%PAGE_NUMBER%',
		'page_text'     => '%PAGE_NUMBER%',
		'first_text'    => __( '&laquo; First', 'voting-contest' ),
		'last_text'     => __( 'Last &raquo;', 'voting-contest' ),
		'prev_text'     => __( '&laquo;', 'voting-contest' ),
		'next_text'     => __( '&raquo;', 'voting-contest' ),
		'dotleft_text'  => __( '...', 'voting-contest' ),
		'dotright_text' => __( '...', 'voting-contest' ),
		'num_pages' => 5,
		'num_larger_page_numbers' => 3,
		'larger_page_numbers_multiple' => 10,
		'always_show' => false,
		'use_pagenavi_css' => true,
		'style' => 1,
	) );

	Voting_PageNavi_Core::init( $options );

	if ( is_admin() ) {
		require_once dirname( __FILE__ ) . '/admin.php';
		new Voting_PageNavi_Options_Page( __FILE__, $options );
	}
}
scb_init( '_votingpagenavi_init' );

