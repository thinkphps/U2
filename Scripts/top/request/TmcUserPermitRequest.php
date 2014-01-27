<?php
/**
 * TOP API: taobao.tmc.user.permit request
 * 
 * @author auto create
 * @since 1.0, 2013-09-22 12:49:27
 */
class TmcUserPermitRequest
{
	/** 
	 * 消息主题列表，用半角逗号分隔。当用户订阅的topic是应用订阅的子集时才需要设置，一般情况建议不要设置。
	 **/
	private $topics;
	
	private $apiParas = array();
	
	public function setTopics($topics)
	{
		$this->topics = $topics;
		$this->apiParas["topics"] = $topics;
	}

	public function getTopics()
	{
		return $this->topics;
	}

	public function getApiMethodName()
	{
		return "taobao.tmc.user.permit";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->topics,100,"topics");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
