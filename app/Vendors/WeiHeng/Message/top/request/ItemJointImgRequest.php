<?php
/**
 * TOP API: taobao.item.joint.img request
 * 
 * @author auto create
 * @since 1.0, 2014.11.04
 */
class ItemJointImgRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "taobao.item.joint.img";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
