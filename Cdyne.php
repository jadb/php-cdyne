<?php
/**
 * Cdyne services base class.
 *
 * @package Cdyne
 * @author Jad Bitar
 * @see http://cdyne.com
 */
class Cdyne {
  /**
   * Constructors.
   *
   * @param string $licenseKey 
   */
  public function __construct($licenseKey = null) {
    $this->licenseKey = $licenseKey;
  }
  /**
   * Queries CDYNE services requested operation with submitted data.
   *
   * @param string $operation Name of the operation to perform.
   * @param array $data CDYNE compatible fields.
   * @return void
   */
  public function query($operation, $data) {
    $url = sprintf('%s/%s', $this->_endpoint, $operation);

    if (!array_key_exists('LicenseKey', $data)) {
      $data['LicenseKey'] = $this->licenseKey;
    }

    $ch = curl_init();
    if ($this->isPost($operation)) {
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } else {
      $params = array();
      foreach ($data as $key => $value) {
        $params[] = "$key=$value";
      }
      curl_setopt($ch, CURLOPT_URL, sprintf('%s?%s', $url, implode('&', $params)));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Accept: application/json'));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }
  public function isPost($operation) {
    return array_key_exists('method', $this->_operations[$operation]) && 'POST' == $this->_operations[$operation]['method'];
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
}
