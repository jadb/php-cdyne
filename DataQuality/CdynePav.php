<?php
include '../Cdyne.php';
/**
 * Wrapper class for CDYNE's Postal Address Verification (v3).
 *
 * @package Cdyne.DataQuality
 * @author Jad Bitar
 * @see http://pav3.cdyne.com/PavService.svc/help
 */
class CdynePav extends Cdyne {
  /**
   * Map fields' key names.
   *
   * @var array
   */
  public $mapFields = array();
  /**
   * Postal Address Verification Service.
   *
   * @var string
   */
  protected $_endpoint = 'http://pav3.cdyne.com/PavService.svc';
  /**
   * Operations offered by the Postal Verification Service.
   *
   * @var array
   */
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
  /**
   * Verify Address (normal and advanced).
   *
   * Valid keys for $options:
   *    - ReturnCaseSensitive
   *    - ReturnCensusInfo
   *    - ReturnCityAbbreviation
   *    - ReturnGeoLocation
   *    - ReturnLegislativeInfo
   *    - ReturnMailingIndustryInfo
   *    - ReturnResidentialIndicator
   *    - ReturnStreetAbbreviated
   *
   * @param array $data Fields. 
   * @param array $options When options are passed, VerifyAddressAdvanced operation will be queried.
   * @return array Result of the operation.
   */
  public function verifyAddress($data, $options = false) {
    $operation = 'VerifyAddress';
    $data = $this->_mapData($data);

    if (!$this->_checkFields($operation, $data)) {
      throw new RuntimeException("Missing some fields required by the operation.");
    }

    if (is_array($options) && !empty($options)) {
      $operation = 'VerifyAddressAdvanced';
      $options = array_merge(array(
        'ReturnCaseSensitive' => false,
        'ReturnCensusInfo' => false,
        'ReturnCityAbbreviation' => false,
        'ReturnGeoLocation' => false,
        'ReturnLegislativeInfo' => false,
        'ReturnMailingIndustryInfo' => false,
        'ReturnResidentialIndicator' => false,
        'ReturnStreetAbbreviated' => false
      ), $options);
      $data = array_merge($data, $options);
    }
    return $this->query($operation, $data);
  }
}