<?php
include dirname(dirname(__FILE__)) . '/Cdyne.php';
class TestCdyne extends Cdyne {
  protected $_operations = array(
    'GetCityNamesForZipCode' => array('fields' => array('ZipCode')),
    'GetCongressionalDistrictByZip' => array('fields' => array('ZipCode')),
    'GetIntelligentMailBarcode' => array('fields' => array('BarcodeIdentifier', 'ServiceTypeIdentifier', 'MailerIdentifier', 'SerialNumber', 'IntelligentMailBarcodeKey')),
    'GetUrbanizationListForZipCode' => array('fields' => array('ZipCode')),
    'GetZipCodesForCityAndState' => array('fields' => array('City', 'State')),
    'GetZipCodesForFips' => array('fields' => array('Fips')),
    'GetZipCodesWithinDistance' => array('fields' => array('Latitude', 'Longitude', 'Radius')),
    'VerifyAddress' => array('fields' => array('FirmOrRecipient', 'PrimaryAddressLine', 'SecondaryAddressLine', 'Urbanization', 'CityName', 'State', 'ZipCode')),
    'VerifyAddressAdvanced' => array('method' => 'POST', 'fields' => array('FirmOrRecipient', 'PrimaryAddressLine', 'SecondaryAddressLine', 'Urbanization', 'CityName', 'State', 'ZipCode'))
  );
  public function checkFields($operation, $data) {
    return parent::_checkFields($operation, $data);
  }
  public function mapData($data) {
    return parent::_mapData($data);
  }
}
class CdyneTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->Cdyne = new TestCdyne('06ff735a-62ae-40b5-b545-d92f728c1715');
	}
	public function tearDown() {
		unset($this->Cdyne);
	}
  public function testInstanceOf() {
    $this->assertTrue(is_a($this->Cdyne, 'Cdyne'));
  }
  public function testMapData() {
    $data = array('ZipCode' => '12345');
    $expects = $data;
    $this->assertEquals($expects, $this->Cdyne->mapData($data));
    
    $this->Cdyne->mapFields = array('zip' => 'ZipCode');
    $data = array('zip' => '12345');
    $expects = array('ZipCode' => '12345');
    $this->assertEquals($expects, $this->Cdyne->mapData($data));
  }
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
  public function testIsPost() {
    $this->assertFalse($this->Cdyne->isPost('GetCityNamesForZipCode'));
    $this->assertTrue($this->Cdyne->isPost('VerifyAddressAdvanced'));
  }
}
