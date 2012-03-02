<?php
include dirname(dirname(__FILE__)) . '/Cdyne.php';
/**
 * Overloads Cdyne to test protected method(s) and fake a service.
 *
 * @package Tests
 * @author Jad Bitar
 */
class TestCdyne extends Cdyne {
  protected $_operations = array(
    'GetCityNamesForZipCode' => array('fields' => array('ZipCode'), 'responseKey' => 'CityNames'),
    'GetCongressionalDistrictByZip' => array('fields' => array('ZipCode'), 'responseKey' => 'CongressionalDistrict'),
    'GetIntelligentMailBarcode' => array('fields' => array('BarcodeIdentifier', 'ServiceTypeIdentifier', 'MailerIdentifier', 'SerialNumber', 'IntelligentMailBarcodeKey'), 'responseKey' => 'Barcode'),
    'GetUrbanizationListForZipCode' => array('fields' => array('ZipCode'), 'responseKey' => 'UrbanizationList'),
    'GetZipCodesForCityAndState' => array('fields' => array('City', 'State'), 'responseKey' => 'ZipCodes'),
    'GetZipCodesForFips' => array('fields' => array('Fips'), 'responseKey' => 'ZipCodes'),
    'GetZipCodesWithinDistance' => array('fields' => array('Latitude', 'Longitude', 'Radius'), 'responseKey' => 'ZipCodes'),
    'VerifyAddress' => array('fields' => array('FirmOrRecipient', 'PrimaryAddressLine', 'SecondaryAddressLine', 'Urbanization', 'CityName', 'State', 'ZipCode')),
    'VerifyAddressAdvanced' => array('method' => 'POST', 'fields' => array('FirmOrRecipient', 'PrimaryAddressLine', 'SecondaryAddressLine', 'Urbanization', 'CityName', 'State', 'ZipCode'))
  );
  public function buildUrl($operation, $data) {
    $this->_endpoint = 'http://pav3.cdyne.com/PavService.svc';
    return parent::_buildUrl($operation, $data);
  }
  public function checkFields($operation, $data) {
    return parent::_checkFields($operation, $data);
  }
  public function decodeResponse($operation, $response) {
    $this->_rawResponse = $response;
    return $this->_decodeResponse($operation);
  }
  public function mapData($data) {
    return parent::_mapData($data);
  }
}
/**
 * Cdyne Tests.
 *
 * @package Tests
 * @author Jad Bitar
 */
class CdyneTest extends PHPUnit_Framework_TestCase {
  /**
   * Set up tests.
   *
   * @return void
   */
	public function setUp() {
		$this->Cdyne = new TestCdyne('06ff735a-62ae-40b5-b545-d92f728c1715');
	}
  /**
   * Tears down tests.
   *
   * @return void
   */
	public function tearDown() {
		unset($this->Cdyne);
	}
  /**
   * Tests correct instance of class.
   *
   * @covers Cdyne::__construct()
   * @group cdyne
   */
  public function testInstanceOf() {
    $this->assertTrue(is_a($this->Cdyne, 'Cdyne'));
  }
  /**
   * Tests .
   *
   * @covers Cdyne::isPost()
   * @group cdyne
   */
  public function testIsPost() {
    $this->assertFalse($this->Cdyne->isPost('GetCityNamesForZipCode'));
    $this->assertTrue($this->Cdyne->isPost('VerifyAddressAdvanced'));
  }
  /**
   * Tests URL for GET operations are generated correctly.
   *
   * @covers Cdyne::_buildUrl()
   * @group cdyne
   */
  public function testBuildUrlGet() {
    $data = array('ZipCode' => '23320', 'LicenseKey' => '0000-0000');
    $expected = 'http://pav3.cdyne.com/PavService.svc/GetCityNamesForZipCode?ZipCode=23320&LicenseKey=0000-0000';
    $this->assertEquals($expected, $this->Cdyne->buildUrl('GetCityNamesForZipCode', $data));
  }
  /**
   * Tests URL for POST operations are generated correctly.
   *
   * @covers Cdyne::_buildUrl()
   * @group cdyne
   */
  public function testBuildUrlPost() {
    $data = array('ZipCode' => '23320', 'LicenseKey' => '0000-0000');
    $expected = 'http://pav3.cdyne.com/PavService.svc/VerifyAddressAdvanced';
    $this->assertEquals($expected, $this->Cdyne->buildUrl('VerifyAddressAdvanced', $data));
  }
  /**
   * undocumented function
   *
   * @covers Cdyne::_checkFields()
   * @group cdyne
   */
  public function testCheckFields() {
    $data = array('ZipCode' => '12345');
    $this->assertTrue($this->Cdyne->checkFields('GetCityNamesForZipCode', $data));
    $this->assertFalse($this->Cdyne->checkFields('VerifyAddress', $data));

    $data = array(
      'FirmOrRecipient' => 'CDYNE Corporation',
      'PrimaryAddressLine' => '505 Independence Pkwy Ste 300',
      'SecondaryAddressLine' => '',
      'Urbanization' => '',
      'CityName' => 'Chesapeake',
      'State' => 'Virginia',
      'ZipCode' => '23320'
    );
    $this->assertTrue($this->Cdyne->checkFields('VerifyAddress', $data));
  }
  /**
   * undocumented function
   *
   * @covers Cdyne::_decodeResponse()
   * @group cdyne
   */
  public function testDecodeResponse() {
    $response = '{"ReturnCode":0,"ZipCodes":["19801","19802","19803","19804","19805","19806","19807","19808","19809","19810","19850","19880","19884","19885","19886","19890","19891","19892","19893","19894","19895","19896","19897","19898","19899"]}';
    $expected = array('19801', '19802', '19803', '19804', '19805', '19806', '19807', '19808', '19809', '19810', '19850', '19880', '19884', '19885', '19886', '19890', '19891', '19892', '19893', '19894', '19895', '19896', '19897', '19898', '19899');
    $this->assertEquals($expected, $this->Cdyne->decodeResponse('GetZipCodesForCityAndState', $response));

    $response = '{"CityName":"CHESAPEAKE","Country":null,"County":"","FirmNameOrRecipient":"CDYNECORPORATION","PrimaryAddressLine":"505IndependencePkwySte300","ReturnCode":10,"SecondaryAddressLine":"","StateAbbreviation":"VA","Urbanization":"","ZipCode":"23320"}';
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
    $this->assertEquals($expected, $this->Cdyne->decodeResponse('VerifyAddressAdvanced', $response));

    $response = '{"CensusInfo":null,"CityName":"CHESAPEAKE","Country":"USA","County":"CHESAPEAKE CITY","CountyNum":"550","FinanceNumber":"511750","FirmOrRecipient":"CDYNE CORPORATION","GeoLocationInfo":null,"IntelligentMailBarcodeKey":"6sueSNs9+F2jrRtjlIet7w==","LegislativeInfo":null,"MailingIndustryInfo":null,"MultipleMatches":null,"PMBDesignator":"","PMBNumber":"","PostDirectional":"","PostnetBarcode":"f233205178757f","PreDirectional":"","PreferredCityName":"CHESAPEAKE","Primary":"505","PrimaryDeliveryLine":"505 INDEPENDENCE PKWY STE 300","PrimaryEO":"O","PrimaryHigh":"505","PrimaryLow":"505","ResidentialDeliveryIndicator":null,"ReturnCode":100,"Secondary":"300","SecondaryAbbreviation":"STE","SecondaryDeliveryLine":"","SecondaryEO":"E","SecondaryHigh":"300","SecondaryLow":"300","StateAbbreviation":"VA","StreetName":"INDEPENDENCE","Suffix":"PKWY","Urbanization":"","ZipCode":"23320-5178"}';
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
    $this->assertEquals($expected, $this->Cdyne->decodeResponse('VerifyAddressAdvanced', $response));
  }
  /**
   * undocumented function
   *
   * @covers Cdyne::_mapData()
   * @group cdyne
   */
  public function testMapData() {
    $data = array('ZipCode' => '12345');
    $expects = $data;
    $this->assertEquals($expects, $this->Cdyne->mapData($data));
    
    $this->Cdyne->mapFields = array('zip' => 'ZipCode');
    $data = array('zip' => '12345');
    $expects = array('ZipCode' => '12345');
    $this->assertEquals($expects, $this->Cdyne->mapData($data));
  }
}
