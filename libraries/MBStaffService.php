<?php
require_once(dirname(__FILE__) . "/mbApi.php");

class MBStaffService extends MBAPIService
{  
  function __construct($debug = false)
  {
    $endpointUrl = "https://" . GetApiHostname() . "/0_5_1/StaffService.asmx";
    $wsdlUrl = $endpointUrl . "?wsdl";
  
    $this->debug = $debug;
    $option = array();
    if ($debug)
    {
      $option = array('trace'=>1);
    }
    $this->client = new soapclient($wsdlUrl, $option);
    $this->client->__setLocation($endpointUrl);
  }

  public function GetStaff(array $StaffIds, $SessionTypeId = '', $LocationId = '', $StartDateTime = '', SourceCredentials $credentials = null, $XMLDetail = XMLDetail::Full, $PageSize = NULL, $CurrentPage = NULL, $Fields = NULL)
  {
    $additions = array();
    if ($SessionTypeId != '') $additions['SessionTypeID'] = $SessionTypeId;
    if ($LocationId != '') $additions['LocationID'] = $LocationId;
    if ($StartDateTime != '') $additions['StartDateTime'] = $StartDateTime;
    if (isset($staffIDs) && count($staffIDs) > 0) {
      $additions['StaffIDs'] = $staffIDs;
    }
    $additions['Filters'] = array('StaffViewable');
    
    $params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);

    try {
      $result = $this->client->GetStaff($params);
    } 
    catch (SoapFault $fault)
    {
      DebugResponse($this->client, $fault->getMessage());
      // <xmp> tag displays xml output in html
      echo '</xmp><br/><br/> Error Message : <br/>', $fault->getMessage(); 
    }

    if ($this->debug)
    {
      DebugRequest($this->client);
      DebugResponse($this->client, $result);
    }
    
    $arrReturn = array(
      'error' => '',
      'data' => array()
    );

    if (isset($result->GetStaffResult->StaffMembers) && $result->GetStaffResult->ErrorCode == '200') {
      if ($result->GetStaffResult->ResultCount == 1) {
        $arrReturn['data'][] = $result->GetStaffResult->StaffMembers->Staff;
      } elseif ($result->GetStaffResult->ResultCount > 1) {
        $arrReturn['data'] = $result->GetStaffResult->StaffMembers->Staff;
      }
    } else {
      $arrReturn['error'] =  'Error on API';
    }

    return $arrReturn;        
  }    
}