<?php
/**
 * Cdyne services base class.
 *
 * @package Cdyne
 * @author Jad Bitar
 * @see http://cdyne.com
 */
class Cdyne {
  protected $_rawResponse = null;
  /**
   * Constructors.
   *
   * @param string $licenseKey 
   */
  public function __construct($licenseKey = null) {
    $this->licenseKey = $licenseKey;
  }
  /**
   * Checks if the operation uses POST.
   *
   * @param string $operation Name of the operation.
   * @return boolean TRUE if the operation uses POST method.
   */
  public function isPost($operation) {
    return array_key_exists('method', $this->_operations[$operation]) && 'POST' == $this->_operations[$operation]['method'];
  }
  /**
   * Queries CDYNE services requested operation with submitted data using JSON.
   *
   * @param string $operation Name of the operation to perform.
   * @param array $data CDYNE compatible fields.
   * @return array Result of the query.
   */
  public function query($operation, $data) {
    if (!array_key_exists('LicenseKey', $data)) {
      $data['LicenseKey'] = $this->licenseKey;
    }

    $url = $this->_buildUrl($operation, $data);

    $ch = curl_init();
    if ($this->isPost($operation)) {
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } else {
      curl_setopt($ch, CURLOPT_URL, $url);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

    $this->_rawResponse = curl_exec($ch);
    curl_close($ch);
    return $this->_decodeResponse($operation);
  }
  /**
   * Builds service's endpoint URL.
   *
   * @param string $operation Name of the operation to trigger.
   * @param array $data CDYNE compatible fields.
   * @return string Service's endpoint URL.
   */
  protected function _buildUrl($operation, $data) {
    $url = sprintf('%s/%s', $this->_endpoint, $operation);
    if (!$this->isPost($operation)) {
      $params = array();
      foreach ($data as $key => $value) {
        $params[] = "$key=$value";
      }
      $url = sprintf('%s?%s', $url, implode('&', $params));
    }
    return $url;
  }
  /**
   * Checks that all required fields by `$operation` are passed.
   *
   * @param string $operation Name of the operation.
   * @param array $data CDYNE compatible fields.
   * @return boolean False if any field is missing, true otherwise.
   */
  protected function _checkFields($operation, $data) {
    foreach (array_values($this->_operations[$operation]['fields']) as $field) {
      if (!array_key_exists($field, $data)) {
        return false;
      }
    }
    return true;
  }
  protected function _decodeResponse($operation) {
    $response = json_decode($this->_rawResponse);
    if (is_null($response)) {
      throw new CdyneException("Invalid JSON returned.");
    }
    
    $response = (array) $response;

    $this->_returnCode = $response['ReturnCode'];
    unset($response['ReturnCode']);

    if (array_key_exists('responseKey', $this->_operations[$operation])) {
      return $response[$this->_operations[$operation]['responseKey']];
    }
    return (array) $response;
  }
  /**
   * Maps data key names to CDYNE keys.
   *
   * @param array $data Raw fields.
   * @return array CDYNE compatible fields.
   */
  protected function _mapData($data) {
    if (empty($this->mapFields)) {
      return $data;
    }

    foreach ($data as $key => $value) {
      if (array_key_exists($key, $this->mapFields)) {
        $data[$this->mapFields[$key]] = $value;
        unset($data[$key]);
      }
    }
    return $data;
  }
}
/**
 * Cdyne Exception.
 *
 * @package Cdyne
 * @author Jad Bitar
 */
class CdyneException extends Exception {}
