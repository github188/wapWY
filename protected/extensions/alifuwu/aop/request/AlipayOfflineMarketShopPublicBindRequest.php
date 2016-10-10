<?php
/**
 * ALIPAY API: alipay.offline.market.shop.public.bind request
 *
 * @author auto create
 * @since 1.0, 2015-10-13 19:26:32
 */
class AlipayOfflineMarketShopPublicBindRequest
{
	/** 
	 * 是否绑定所有门店，T表示所有门店，F表示不是所有门店
	 **/
	private $isAll;
	
	/** 
	 * 门店ID列表，一次最多绑定100个门店，is_all为F时必须
	 **/
	private $shopIds;

	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;

	
	public function setIsAll($isAll)
	{
		$this->isAll = $isAll;
		$this->apiParas["is_all"] = $isAll;
	}

	public function getIsAll()
	{
		return $this->isAll;
	}

	public function setShopIds($shopIds)
	{
		$this->shopIds = $shopIds;
		$this->apiParas["shop_ids"] = $shopIds;
	}

	public function getShopIds()
	{
		return $this->shopIds;
	}

	public function getApiMethodName()
	{
		return "alipay.offline.market.shop.public.bind";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

}
