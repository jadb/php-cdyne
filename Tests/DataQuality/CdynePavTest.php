<?php
include dirname(dirname(dirname(__FILE__))) . '/DataQuality/CdynePav.php';
class CdynePavTest extends PHPUnit_Framework_TestCase {
  /**
   * Set up tests.
   *
   * @return void
   */
	public function setUp() {
    if (!defined('CDYNE_PAV_KEY')) {
      @include dirname(dirname(__FILE__)) . '/constants.php';
      if (!defined('CDYNE_PAV_KEY')) {
        $this->markTestSkipped("The CDYNE_PAV_KEY constant is not defined.");
      }
    }
		$this->Pav = new CdynePav(CDYNE_PAV_KEY);
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
   * Tests correct instance of class.
   *
   * @return void
   */
  public function testInstanceOf() {
    $this->assertTrue(is_a($this->Pav, 'CdynePav'));
  }
  /**
   * Tests `CdynePav::verifyAddress()`.
   *
   * @return void
   * @author Jad Bitar
   * @covers CdynePav::verifyAddress()
   * @group cdyne
   * @group data_quality
   */
  public function testVerifyAddress() {
    $data = array(
      'FirmOrRecipient' => 'CDYNE Corporation',
      'PrimaryAddressLine' => '505 Independence Pkwy Ste 300',
      'SecondaryAddressLine' => '',
      'Urbanization' => '',
      'CityName' => 'Chesapeake',
      'State' => 'Virginia',
      'ZipCode' => '23320'
    );
    $expected = '{"CityName":"CHESAPEAKE","Country":null,"County":"","FirmNameOrRecipient":"CDYNECORPORATION","PrimaryAddressLine":"505IndependencePkwySte300","ReturnCode":10,"SecondaryAddressLine":"","StateAbbreviation":"VA","Urbanization":"","ZipCode":"23320"}';
    $this->assertEquals($expected, $this->Pav->verifyAddress($data));

    $options = array('ReturnCityAbbreviation' => true);
    $expected = '{"CensusInfo":null,"CityName":"CHESAPEAKE","Country":"USA","County":"CHESAPEAKE CITY","CountyNum":"550","FinanceNumber":"511750","FirmOrRecipient":"CDYNE CORPORATION","GeoLocationInfo":null,"IntelligentMailBarcodeKey":"6sueSNs9+F2jrRtjlIet7w==","LegislativeInfo":null,"MailingIndustryInfo":null,"MultipleMatches":null,"PMBDesignator":"","PMBNumber":"","PostDirectional":"","PostnetBarcode":"f233205178757f","PreDirectional":"","PreferredCityName":"CHESAPEAKE","Primary":"505","PrimaryDeliveryLine":"505 INDEPENDENCE PKWY STE 300","PrimaryEO":"O","PrimaryHigh":"505","PrimaryLow":"505","ResidentialDeliveryIndicator":null,"ReturnCode":100,"Secondary":"300","SecondaryAbbreviation":"STE","SecondaryDeliveryLine":"","SecondaryEO":"E","SecondaryHigh":"300","SecondaryLow":"300","StateAbbreviation":"VA","StreetName":"INDEPENDENCE","Suffix":"PKWY","Urbanization":"","ZipCode":"23320-5178"}';
    $this->assertEquals($expected, $this->Pav->verifyAddress($data, $options));
  }
}
