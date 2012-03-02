<?php
include dirname(dirname(dirname(__FILE__))) . '/DataQuality/CdynePav.php';
/**
 * Overload CdynePav to test protected method(s).
 *
 * @package Tests.DataQuality
 * @author Jad Bitar
 */
class TestCdynePav extends CdynePav {
  public function argsToData() {
    $args = func_get_args();
    $operation = array_shift($args);
    return $this->_argsToData($operation, $args);
  }
}
/**
 * CdynePav Tests.
 *
 * @package Tests.DataQuality
 * @author Jad Bitar
 */
class CdynePavTest extends PHPUnit_Framework_TestCase {
  /**
   * Set up tests.
   *
   * @return void
   */
	public function setUp() {
		$this->Pav = new TestCdynePav();
	}
  /**
   * Tears down tests.
   *
   * @return void
   */
	public function tearDown() {
		unset($this->Pav);
	}
  /**
   * Skips test if license key (`CDYNE_PAV_KEY`) is not defined in `Tests/constants.php`.
   *
   * @return void
   */
  public function skipTestWithNoKey() {
    if (!defined('CDYNE_PAV_KEY')) {
      @include dirname(dirname(__FILE__)) . '/constants.php';
      if (!defined('CDYNE_PAV_KEY')) {
        $this->markTestSkipped("The CDYNE_PAV_KEY constant is not defined.");
      }
    }
    $this->Pav->licenseKey = CDYNE_PAV_KEY;
  }
  /**
   * Tests correct instance of class.
   *
   * @covers CdynePav::__construct()
   * @group cdyne
   * @group data_quality
   */
  public function testInstanceOf() {
    $this->assertTrue(is_a($this->Pav, 'CdynePav'));
  }
  /**
   * Tests correct exception thrown when calling an undefined service operation.
   *
   * @covers CdynePav::__call()
   * @group cdyne
   * @group data_quality
   * @expectedException CdyneException
   * @expectedExceptionMessage Undefined operation.
   */
  public function testUndefinedOperation() {
    $this->Pav->undefinedOperation();
  }
  /**
   * Tests correct exception thrown when calling an operation with no $data.
   *
   * @covers CdynePav::__call()
   * @group cdyne
   * @group data_quality
   * @expectedException CdyneException
   * @expectedExceptionMessage Missing all required fields by the `GetCityNamesForZipCode` operation.
   */
  public function testGetCityNamesForZipCodeMissingAllRequiredFields() {
    $this->Pav->getCityNamesForZipCode();
  }
  /**
   * Tests correct exception thrown when calling an operation with no $data.
   *
   * @covers CdynePav::__call()
   * @group cdyne
   * @group data_quality
   * @expectedException CdyneException
   * @expectedExceptionMessage Missing some fields required by the `GetZipCodesForCityAndState` operation.
   */
  public function testGetZipCodesForCityAndStateMissingSomeRequiredFields() {
    $this->Pav->getZipCodesForCityAndState(array('City' => 'Chesapeake'));
  }
  /**
   * Tests correct zip codes are returned.
   *
   * @covers CdynePav::__call()
   * @group cdyne
   * @group data_quality
   */
  public function testGetZipCodesForCityAndState() {
    $this->skipTestWithNoKey();
    $expected = array('19801', '19802', '19803', '19804', '19805', '19806', '19807', '19808', '19809', '19810', '19850', '19880', '19884', '19885', '19886', '19890', '19891', '19892', '19893', '19894', '19895', '19896', '19897', '19898', '19899');
    $this->assertEquals($expected, $this->Pav->getZipCodesForCityAndState(array('City' => 'Wilmington', 'State' => 'DE')));
  }
  /**
   * Tests correct zip codes are returned.
   *
   * @covers CdynePav::__call()
   * @group cdyne
   * @group data_quality
   */
  public function testGetCongressionalDistrictByZip() {
    $this->skipTestWithNoKey();
    $expected = '04';
    $this->assertEquals($expected, $this->Pav->getCongressionalDistrictByZip('23320'));
  }
  /**
   * Tests `CdynePav::verifyAddress()`.
   *
   * @covers CdynePav::verifyAddress()
   * @group cdyne
   * @group data_quality
   */
  public function testVerifyAddress() {
    $this->skipTestWithNoKey();
    $data = array(
      'FirmOrRecipient' => 'CDYNE Corporation',
      'PrimaryAddressLine' => '505 Independence Pkwy Ste 300',
      'SecondaryAddressLine' => '',
      'Urbanization' => '',
      'CityName' => 'Chesapeake',
      'State' => 'Virginia',
      'ZipCode' => '23320'
    );
    $expected = array(
      'CityName' => 'CHESAPEAKE',
      'Country' => null,
      'County' => '',
      'FirmNameOrRecipient' => 'CDYNECORPORATION',
      'PrimaryAddressLine' => '505IndependencePkwySte300',
      'SecondaryAddressLine' => '',
      'StateAbbreviation' => 'VA',
      'Urbanization' => '',
      'ZipCode' => '23320'
    );
    $this->assertEquals($expected, $this->Pav->verifyAddress($data));

    $options = array('ReturnCityAbbreviation' => true);
    $expected = array(
      'CensusInfo' => null,
      'CityName' => 'CHESAPEAKE',
      'Country' => 'USA',
      'County' => 'CHESAPEAKE CITY',
      'CountyNum' => '550',
      'FinanceNumber' => '511750',
      'FirmOrRecipient' => 'CDYNE CORPORATION',
      'GeoLocationInfo' => null,
      'IntelligentMailBarcodeKey' => '6sueSNs9+F2jrRtjlIet7w==',
      'LegislativeInfo' => null,
      'MailingIndustryInfo' => null,
      'MultipleMatches' => null,
      'PMBDesignator' => '',
      'PMBNumber' => '',
      'PostDirectional' => '',
      'PostnetBarcode' => 'f233205178757f',
      'PreDirectional' => '',
      'PreferredCityName' => 'CHESAPEAKE',
      'Primary' => '505',
      'PrimaryDeliveryLine' => '505 INDEPENDENCE PKWY STE 300',
      'PrimaryEO' => 'O',
      'PrimaryHigh' => '505',
      'PrimaryLow' => '505',
      'ResidentialDeliveryIndicator' => null,
      'Secondary' => '300',
      'SecondaryAbbreviation' => 'STE',
      'SecondaryDeliveryLine' => '',
      'SecondaryEO' => 'E',
      'SecondaryHigh' => '300',
      'SecondaryLow' => '300',
      'StateAbbreviation' => 'VA',
      'StreetName' => 'INDEPENDENCE',
      'Suffix' => 'PKWY',
      'Urbanization' => '',
      'ZipCode' => '23320-5178',
    );
    $this->assertEquals($expected, $this->Pav->verifyAddress($data, $options));
  }
  /**
   * Tests conversion of $args into a valid $data array according to the $operation being called.
   *
   * @covers CdynePav::_argsToData()
   * @group cdyne
   * @group data_quality
   */
  public function testArgsToData() {
    $expected = array('ZipCode' => '23320');
    $this->assertEquals($expected, $this->Pav->argsToData('GetCityNamesForZipCode', '23320'));

    $expected = array('City' => 'Chesapeake', 'State' => 'Virginia');
    $this->assertEquals($expected, $this->Pav->argsToData('GetZipCodesForCityAndState', 'Chesapeake', 'Virginia'));

    $expected = array('City' => 'Chesapeake', 'State' => 'Virginia');
    $this->assertEquals($expected, $this->Pav->argsToData('GetZipCodesForCityAndState', array('Chesapeake', 'Virginia')));

    $expected = array('City' => 'Chesapeake', 'State' => 'Virginia');
    $this->assertEquals($expected, $this->Pav->argsToData('GetZipCodesForCityAndState', $expected));
  }
}
