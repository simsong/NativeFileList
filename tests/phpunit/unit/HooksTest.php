<?php

namespace MediaWiki\Extension\NativeFileList\Tests;

use MediaWiki\Extension\NativeFileList\Hooks;

/**
 * @coversDefaultClass \MediaWiki\Extension\NativeFileList\Hooks
 */
class HooksTest extends \MediaWikiUnitTestCase {

	/**
	 * @covers ::onBeforePageDisplay
	 */
	public function testOnBeforePageDisplayVandalizeIsTrue() {
		$config = new \HashConfig( [
			'NativeFileListVandalizeEachPage' => true
		] );
		$outputPageMock = $this->getMockBuilder( \OutputPage::class )
			->disableOriginalConstructor()
			->getMock();
		$outputPageMock->method( 'getConfig' )
			->willReturn( $config );

		$outputPageMock->expects( $this->once() )
			->method( 'addHTML' )
			->with( '<p>NativeFileList was here</p>' );
		$outputPageMock->expects( $this->once() )
			->method( 'addModules' )
			->with( 'oojs-ui-core' );

		$skinMock = $this->getMockBuilder( \Skin::class )
			->disableOriginalConstructor()
			->getMock();

		Hooks::onBeforePageDisplay( $outputPageMock, $skinMock );
	}

	/**
	 * @covers ::onBeforePageDisplay
	 */
	public function testOnBeforePageDisplayVandalizeFalse() {
		$config = new \HashConfig( [
			'NativeFileListVandalizeEachPage' => false
		] );
		$outputPageMock = $this->getMockBuilder( \OutputPage::class )
			->disableOriginalConstructor()
			->getMock();
		$outputPageMock->method( 'getConfig' )
			->willReturn( $config );
		$outputPageMock->expects( $this->never() )
			->method( 'addHTML' );
		$outputPageMock->expects( $this->never() )
			->method( 'addModules' );
		$skinMock = $this->getMockBuilder( \Skin::class )
			->disableOriginalConstructor()
			->getMock();
		Hooks::onBeforePageDisplay( $outputPageMock, $skinMock );
	}

}
