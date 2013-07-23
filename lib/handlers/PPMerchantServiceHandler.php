<?php

/**
 *
 * Adds non-authentication headers that are specific to
 * PayPal's Merchant APIs and determines endpoint to
 * hit based on configuration parameters.
 *
 */
class PPMerchantServiceHandler extends PPGenericServiceHandler {
	private $endpoint;
	private $config;
	public function handle($httpConfig, $request, $options) {
		parent::handle($httpConfig, $request, $options);
		$this->config = $options['config'];
		$credential = $request->getCredential();
		if(isset($options['port']) && isset($this->config['service.EndPoint.'.$options['port']]))
		{
			$this->endpoint = $this->config['service.EndPoint.'.$options['port']];
		}
		// for backward compatibilty (for those who are using old config files with 'service.EndPoint')
		else if (isset($this->config['service.EndPoint']))
		{
			$this->endpoint = $this->config['service.EndPoint'];
		}
		else if (isset($this->config['mode']))
		{
			if(strtoupper($this->config['mode']) == 'SANDBOX')
			{
				if($credential instanceof PPSignatureCredential)
				{
					$this->endpoint = PPConstants::MERCHANT_SANDBOX_SIGNATURE_ENDPOINT;
				}
				else if($credential instanceof PPCertificateCredential)
				{
					$this->endpoint = PPConstants::MERCHANT_SANDBOX_CERT_ENDPOINT;
				}
			}
			else if(strtoupper($this->config['mode']) == 'LIVE')
			{
			if($credential instanceof PPSignatureCredential)
				{
					$this->endpoint = PPConstants::MERCHANT_LIVE_SIGNATURE_ENDPOINT;
				}
				else if($credential instanceof PPCertificateCredential)
				{
					$this->endpoint = PPConstants::MERCHANT_LIVE_CERT_ENDPOINT;
				}
			}
		}
		else
		{
			throw new PPConfigurationException('endpoint Not Set');
		}
		
		if($request->getBindingType() == 'SOAP' )
		{
			$httpConfig->setUrl($this->endpoint);
		}
		else 
		{
			throw new PPConfigurationException('expecting service binding to be SOAP');
		}
		
		$request->addBindingInfo("namespace", "xmlns:ns=\"urn:ebay:api:PayPalAPI\" xmlns:ebl=\"urn:ebay:apis:eBLBaseComponents\" xmlns:cc=\"urn:ebay:apis:CoreComponentTypes\" xmlns:ed=\"urn:ebay:apis:EnhancedDataTypes\"");
	}
}