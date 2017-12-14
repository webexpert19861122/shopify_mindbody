<?php
require_once(dirname(__FILE__) . "/mbApi.php");

class MBSiteService extends MBAPIService
{	
	function __construct($debug = false)
	{
		$endpointUrl = "https://" . GetApiHostname() . "/0_5_1/SiteService.asmx";
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
	
	/**
	 * Returns the raw result of the MINDBODY SOAP call.
	 * @param int $PageSize
	 * @param int $CurrentPage
	 * @param string $XMLDetail
	 * @param string $Fields
	 * @param SourceCredentials $credentials A source credentials object to use with this call
	 * @return object The raw result of the SOAP call
	 */
	public function GetSites(SourceCredentials $credentials = null, $XMLDetail = XMLDetail::Full, $PageSize = NULL, $CurrentPage = NULL, $Fields = NULL)
	{
		$additions = array();
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);
		
		try {
			$result = $this->client->GetSites($params);
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
		
		return $result;
	}

	public function getLocations(SourceCredentials $credentials = null, $XMLDetail = XMLDetail::Full, $PageSize = NULL, $CurrentPage = NULL, $Fields = NULL)
	{
		$additions = array();
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);
		
		try {
			$result = $this->client->GetLocations($params);
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
        
    if (isset($result->GetLocationsResult->Locations) && $result->GetLocationsResult->ErrorCode == '200') {
      if ($result->GetLocationsResult->ResultCount == 1) {
        $arrReturn['data'][] = $result->GetLocationsResult->Locations->Location;
      } elseif ($result->GetLocationsResult->ResultCount > 1) {
        $arrReturn['data'] = $result->GetLocationsResult->Locations->Location;
      }
    } else {
      $arrReturn['error'] =  'Error on API';
    }

    return $arrReturn;    
	}	

	public function GetPrograms($ScheduleType = '', $OnlineOnly = false, SourceCredentials $credentials = null, $XMLDetail = XMLDetail::Full, $PageSize = NULL, $CurrentPage = NULL, $Fields = NULL)
	{
		$additions = array();
		$additions['OnlineOnly'] = $OnlineOnly;
		if ($ScheduleType != '') $additions['ScheduleType'] = $ScheduleType;
		
		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);

		try {
			$result = $this->client->GetPrograms($params);
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

    if (isset($result->GetProgramsResult->Programs) && $result->GetProgramsResult->ErrorCode == '200') {
      if ($result->GetProgramsResult->ResultCount == 1) {
        $arrReturn['data'][] = $result->GetProgramsResult->Programs->Program;
      } elseif ($result->GetProgramsResult->ResultCount > 1) {
        $arrReturn['data'] = $result->GetProgramsResult->Programs->Program;
      }
    } else {
      $arrReturn['error'] =  'Error on API';
    }

    return $arrReturn;        
	}		

	public function GetSessionTypes($ProgramIDs, $OnlineOnly = false, SourceCredentials $credentials = null, $XMLDetail = XMLDetail::Full, $PageSize = NULL, $CurrentPage = NULL, $Fields = NULL)
	{
		$additions = array();
		$additions['OnlineOnly'] = $OnlineOnly;
		if (count($ProgramIDs) > 0) $additions['ProgramIDs'] = $ProgramIDs;

		$params = $this->GetMindbodyParams($additions, $this->GetCredentials($credentials), $XMLDetail, $PageSize, $CurrentPage, $Fields);

		try {
			$result = $this->client->GetSessionTypes($params);
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

    if (isset($result->GetSessionTypesResult->SessionTypes) && $result->GetSessionTypesResult->ErrorCode == '200') {
      if ($result->GetSessionTypesResult->ResultCount == 1) {
        $arrReturn['data'][] = $result->GetSessionTypesResult->SessionTypes->SessionType;
      } elseif ($result->GetSessionTypesResult->ResultCount > 1) {
        $arrReturn['data'] = $result->GetSessionTypesResult->SessionTypes->SessionType;
      }
    } else {
      $arrReturn['error'] =  'Error on API';
    }
    
		return $arrReturn;
	}			
}