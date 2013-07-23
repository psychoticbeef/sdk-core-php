<?php

/**
 * 
 * Adds non-authentication headers that are specific to
 * PayPal's platform APIs and determines endpoint to
 * hit based on configuration parameters.
 *
 */
class PPPlatformServiceHandler extends PPGenericServiceHandler {
	private $endpoint;
	private $config;
	public function handle($httpConfig, $request, $options) {
		parent::handle($httpConfig, $request, $options);
		$this->config = $options['config'];
		$credential = $request->getCredential();
		if($credential && $credential->getApplicationId() != NULL) {
			$httpConfig->addHeader('X-PAYPAL-APPLICATION-ID', $credential->getApplicationId());
		}
		if(isset($this->config['port']) && isset($this->config['service.EndPoint.'.$options['port']]))
		{
			$endpnt = 'service.EndPoint.'.$options['port']; 
			$this->endpoint = $this->config[$endpnt];
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
				$this->endpoint = PPConstants::PLATFORM_SANDBOX_ENDPOINT;
			}
			else if(strtoupper($this->config['mode']) == 'LIVE')
			{
				$this->endpoint = PPConstants::PLATFORM_LIVE_ENDPOINT;
			}
		}
		else
		{
			throw new PPConfigurationException('endpoint Not Set');
		}
		$httpConfig->setUrl($this->endpoint . $options['serviceName'] . '/' .  $options['apiMethod']);
	
	}
}