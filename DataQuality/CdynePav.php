<?php
include dirname(dirname(__FILE__)) . '/Cdyne.php';
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
    'GetCityNamesForZipCode' => array(
      'fields' => array('ZipCode'),
      'responseKey' => 'CityNames'
    ),
    'GetCongressionalDistrictByZip' => array(
      'fields' => array('ZipCode'),
      'responseKey' => 'CongressionalDistrict'
    ),
    'GetIntelligentMailBarcode' => array(
      'fields' => array('BarcodeIdentifier', 'ServiceTypeIdentifier', 'MailerIdentifier', 'SerialNumber', 'IntelligentMailBarcodeKey'),
      'responseKey' => 'Barcode'
    ),
    'GetUrbanizationListForZipCode' => array(
      'fields' => array('ZipCode'),
      'responseKey' => 'UrbanizationList'
    ),
    'GetZipCodesForCityAndState' => array(
      'fields' => array('City', 'State'),
      'responseKey' => 'ZipCodes'
    ),
    'GetZipCodesForFips' => array(
      'fields' => array('Fips'),
      'responseKey' => 'ZipCodes'
    ),
    'GetZipCodesWithinDistance' => array(
      'fields' => array('Latitude', 'Longitude', 'Radius'),
      'responseKey' => 'ZipCodes'
    ),
    'VerifyAddress' => array(
      'fields' => array('FirmOrRecipient', 'PrimaryAddressLine', 'SecondaryAddressLine', 'Urbanization', 'CityName', 'State', 'ZipCode')
    ),
    'VerifyAddressAdvanced' => array(
      'method' => 'POST',
      'fields' => array('FirmOrRecipient', 'PrimaryAddressLine', 'SecondaryAddressLine', 'Urbanization', 'CityName', 'State', 'ZipCode')
      )
  );
  /**
   * Runs all operations that don't have a defined method of their own.
   *
   * @param string $operation 
   * @param array $args 
   * @throws CdyneException when a service operation does not exist or when some data fields are missing.
   * @return array|boolean Returns operation's response or false when an error is encountered.
   */
  public function __call($operation, $args) {
    $operation[0] = strtoupper($operation[0]);
    if (!array_key_exists($operation, $this->_operations)) {
      throw new CdyneException("Undefined operation.");
    }

    if (empty($args)) {
      throw new CdyneException("Missing all required fields by the `$operation` operation.");
    }

    $data = $this->_argsToData($operation, $args);

    if (!$this->_checkFields($operation, $data)) {
      throw new CdyneException("Missing some fields required by the `$operation` operation.");
    }

    return $this->query($operation, $data);
  }
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
      throw new CdyneException("Missing some fields required by the operation.");
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
  /**
   * Converts arguments passed to overloaded operation into a CDYNE compatible data array.
   *
   * @param string $operation Name of the operation.
   * @param array $args Function arguments caught by `self::__call()`.
   * @return array CDYNE compatible data array.
   */
  protected function _argsToData($operation, $args) {
    $data = array();
    if (is_array($args[0]) && count($args[0]) == count($this->_operations[$operation]['fields'])) {
      if (array_keys($args[0]) == $this->_operations[$operation]['fields']) {
        $data = $args[0];
      } else {
        $data = array_combine($this->_operations[$operation]['fields'], $args[0]);
      }
    } else if (!is_array($args[0]) && count($args) == count($this->_operations[$operation]['fields'])) {
      $data = array_combine($this->_operations[$operation]['fields'], $args);
    }
    return $data;
  }
}
