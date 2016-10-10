<?php
include_once(dirname(__FILE__).'/../mainClass.php');
/*
 * 时间：2015-6-17
 * 创建人：顾磊
 * */
class AgentC extends mainClass{
	public $page = null;

	//添加合作商
	/*
	 * $name 合作商名称 必填
	 * $account 账号 必填
	 * $pwd 密码 必填
	 * $parentAgent 上级合作商
	 * $contact 联系电话
	 * $remark 备注
	 * $points 积分
	 * $smgr_id 专属客服
	 * */
	public function addAgent($name,$account,$pwd,$parentAgentId,$contact,$remark,$points,$smgr_id=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$flag = 0;
		//验证合作商名
		if(!isset($name) || empty($name)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数name缺失';
			$flag = 1;
		}else{
			$model = Agent::model() -> find('name=:name and flag =:flag',array(
				':name' => $name,
				':flag' => FLAG_NO
			));
			if($model){
				$result['status'] = ERROR_DUPLICATE_DATA;
				$result['errMsg'] = 'name';
				return json_encode($result);
			}
		}
		//验证账号
		if(!isset($account) || empty($account)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg  = $errMsg.'参数account缺失';
			$flag = 1;
		}else{
			$model = Agent::model() -> find('account=:account and flag =:flag',array(
				':account' => $account,
				':flag' => FLAG_NO
			));
			if($model){
				$result['status'] = ERROR_DUPLICATE_DATA;
				$result['errMsg'] = 'account';
				return json_encode($result);
			}
		}
		//验证密码
		if(!isset($pwd) || empty($pwd)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg  = $errMsg.'参数pwd缺失';
			$flag = 1;
		}
		if($flag == 1){
			$result['errMsg'] = $errMsg;
			return json_encode($result);
		}

		$agent = new Agent();
		$agent -> name = $name;
		$agent -> account = $account;
		$agent -> pwd = md5($pwd);
		$agent -> role = AGENT_ROLE_AGENT;

		if(isset($parentAgentId) && !empty($parentAgentId)){
			$agent -> pid = $parentAgentId;
			$criteria = new CDbCriteria();
			$criteria->addCondition('id = :id');
			$criteria->params[':id'] = $parentAgentId;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			$parentAgent = Agent::model()->find($criteria);
			if(!isset($parentAgent -> gid) || empty($parentAgent -> gid)){
				$agent -> gid = '/'.$parentAgent->id.'/';
			}else{
				$agent -> gid = $parentAgent -> gid.$parentAgent->id.'/';
			}

		}


		if(isset($contact) && !empty($contact)){
			$agent -> contact = $contact;
		}
		if(isset($remark) && !empty($remark)){
			$agent -> remark = $remark;
		}
		$agent -> create_time = new CDbExpression('now()');
		if(isset($points) && !empty($points)){
			$agent -> points = $points;
		}

		if(isset($smgr_id) && !empty($smgr_id)){
			$agent -> smgr_id = $smgr_id;
		}
		if($agent -> save()){
			$result['status'] = ERROR_NONE;
			return json_encode($result);
		}
	}

	//查询今日新增服务商
	public  function countNewAgent()
	{
		$time = date('Y-m-d',time());
		$result = Agent::model()->count('date_format(create_time ,\'%Y-%m-%d\') = :time ',array(':time'=>$time));
		return json_encode($result);

	}

	//编辑合作商
	/*
	 * $agentId 合作商id 必填
	 * $name 合作商名称
	 * $account 账号
	 * $contact 联系电话
	 * $remark 备注
	 * $points 积分
	 * $smgr_id 专属客服id
	 * */
	public function editAgent($agentId,$name,$account,$contact,$remark,$points,$smgr_id='',$pay_type=''){
		//返回结果
		$result = array('status'=>1,'errMsg'=>'null','data'=>'null');
		//验证合作商id
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = RETURN_RESULT_FAIL;
			$result['errMsg'] = ERROR_PARAMETER_MISS;
			return json_encode($result);
		}
		$criteria = new CDbCriteria();
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $agentId;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$agent = Agent::model()->find($criteria);
		if($agent){
			//修改合作商名称
			if(isset($name) && !empty($name)){
				$agent -> name = $name;
			}
			//修改账号
			if(isset($account) && !empty($account)){
				$agent -> account = $account;
			}
			//修改联系电话
			if(isset($contact) && !empty($contact)){
				$agent -> contact = $contact;
			}
			//修改备注
			if(isset($remark) && !empty($remark)){
				$agent -> remark = $remark;
			}
			//修改积分
			if(isset($points) && !empty($points)){
				$agent -> points = $points;
			}
			//修改专属客服
			if(isset($smgr_id) && !empty($smgr_id)){
				$agent -> smgr_id = $smgr_id;
			}
            //修改支付方式
            if(isset($pay_type) && !empty($pay_type)){
                $agent -> pay_type = $pay_type;
            }
			if($agent -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}
		}
	}

	//开启或关闭子账号功能
	/*
	 * $agentId 合作商id 必填
	 * */
	public function openAgentSubAccount($agentId){
		//返回结果
		$result = array('status'=>1,'errMsg'=>'null','data'=>'null');
		//验证合作商id
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = 'agentId参数缺失';
			return json_encode($result);
		}
		//根据合作商id查询合作商
		$criteria = new CDbCriteria();
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $agentId;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$agent = Agent::model()->find($criteria);
		if($agent){
			//修改合作商if_subaccount字段
			if($agent -> if_subaccount == SUBACCOUNT_OPEN){
				$agent -> if_subaccount = SUBACCOUNT_CLOSE;
			}elseif ($agent -> if_subaccount == SUBACCOUNT_CLOSE){
				$agent -> if_subaccount = SUBACCOUNT_OPEN;
			}
			if($agent -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}
		}else{
			//没有找到合作商
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该合作商不存在';
			return json_encode($result);
		}
	}

    /**
     * @param $agentId
     * @return string
     * @throws CDbException
     * 确认付款
     */
    public function getPayStatus($agentId) {
        //验证结果
        $result = array();
        //验证运营商id
        if(!isset($agentId) || empty($agentId)){
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = 'agentId参数缺失';
            return json_encode($result);
        }
        //根据运营商id查询运营商
        $criteria = new CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params[':id'] = $agentId;
        $criteria->addCondition('flag = :flag');
        $criteria->params[':flag'] = FLAG_NO;
        $agent = Agent::model()->find($criteria);
        if($agent){
            //修改运营商pay_status字段
            if($agent -> pay_status == AGENT_PAY_STATUS_WAITING_CONFIRM){
                $agent -> pay_status = AGENT_PAY_STATUS_PAID;
            }
            if($agent -> update()){
                $result['status'] = ERROR_NONE;
                return json_encode($result);
            }
        }else{
            //没有找到合作商
            $result['status'] = ERROR_PARAMETER_FORMAT;
            $result['errMsg'] = '该合作商不存在';
            return json_encode($result);
        }
    }

	//锁定和解锁合作商（子账号）
	public function setAgentStatus($agentId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		//验证合作商id
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数agentId缺失';
			return json_encode($result);
		}
		//根据合作商id查询合作商
		$criteria = new CDbCriteria();
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $agentId;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$agent = Agent::model()->find($criteria);
		if($agent){
			//修改合作商if_subaccount字段
			if($agent -> status == AGENT_STATUS_NORMAL){
				$agent -> status = AGENT_STATUS_LOCK;
			}elseif ($agent -> status == AGENT_STATUS_LOCK){
				$agent -> status = AGENT_STATUS_NORMAL;
			}
			if($agent -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}
		}else{
			//没有找到合作商
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该合作商不存在';
			return json_encode($result);
		}
	}

	//查询合作商列表
	/*$parentAgentId 上级合作商id
	 * $name 合作商名称
	 * $account 账号
	 * $status 状态
	 * $if_subaccount 是否开启子账号功能
	 * */
	public function getAgentList($parentAgentId='',$name='',$account='',$status='',$if_subaccount=''){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');

		$criteria = new CDbCriteria();
		if(isset($parentAgentId) && !empty($parentAgentId)){
			$criteria->addCondition("gid like '%/$parentAgentId/%'");
		}
		if(isset($name) && !empty($name)){
			$criteria->addCondition("name like '%$name%'");
		}
		if(isset($account) && !empty($account)){
			$criteria->addCondition("account like '%$account%'");
		}
		if(isset($status) && !empty($status)){
			$criteria->addCondition('status = :status');
			$criteria->params[':status'] = $status;
		}
		if(isset($if_subaccount) && !empty($if_subaccount)){
			$criteria->addCondition('if_subaccount = :if_subaccount');
			$criteria->params[':if_subaccount'] = $if_subaccount;
		}
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;

		$criteria->addCondition('agent_type = :agent_type');
		$criteria->params[':agent_type'] = AGENT_TYPE_OLD;

		//按创建时间排序
		$criteria->order = 'create_time DESC';

		//分页
		$pages = new CPagination(Agent::model()->count($criteria));
		$pages -> pageSize = Yii::app() -> params['perPage'];
		$pages -> applyLimit($criteria);
		$this -> page = $pages;

		$agent = Agent::model()->findAll($criteria);
		$data = array();
		if(!empty($agent)){
			foreach ($agent as $k => $v){
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['pid'] = $v -> pid;
				$data['list'][$k]['gid'] = $v -> gid;
				$data['list'][$k]['name'] = $v -> name;

                if (isset($v -> pid) && !empty($v -> pid)){
                    $parentagent = Agent::model() -> find('id = :id and flag = :flag', array(
                        ':id' => $v -> id,
                        ':flag' => FLAG_NO
                    ));
                    if ($parentagent) {
                        $data['list'][$k]['parentagent_name'] = $parentagent -> name;
                    }
                }
				$data['list'][$k]['account'] = $v -> account;
				$data['list'][$k]['pwd'] = $v -> pwd;
				$data['list'][$k]['contact'] = $v -> contact;
				$data['list'][$k]['remark'] = $v -> remark;
				$data['list'][$k]['create_time'] = $v -> create_time;
				$data['list'][$k]['last_time'] = $v -> last_time;
				$data['list'][$k]['flag'] = $v -> flag;
				$data['list'][$k]['status'] = $v -> status;
				$data['list'][$k]['login_time'] = $v -> login_time;
				$data['list'][$k]['login_ip'] = $v -> login_ip;
				$data['list'][$k]['points'] = $v -> points;
				$data['list'][$k]['if_subaccount'] = $v -> if_subaccount;
				$data['list'][$k]['withdraw_cash'] = $v -> withdraw_cash;
				$data['list'][$k]['discount'] = $v -> discount;
				$data['list'][$k]['if_recommend'] = $v -> if_recommend;
			}
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
			return json_encode($result);
		}else{
			$result['status'] = ERROR_NONE;
			$data['list'] = array();
			$result['data'] = $data;
			return json_encode($result);
		}

	}

	//查询合作商详情
	/*
	 * $agentId 合作商id 必填
	 * */
	public function getAgentDetails($agentId){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		//验证合作商id
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$result['errMsg'] = '参数agentId缺失';
			return json_encode($result);
		}
		$criteria = new CDbCriteria();
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $agentId;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$agent = Agent::model()->find($criteria);
		if($agent){
			$result['status'] = ERROR_NONE;
			//合作商详情
			$result['data'] = array(
					'id' => $agent -> id,
					'pid' => $agent -> pid,
					'gid' => $agent -> gid,
					'name' => $agent -> name,
					'smgr_id' => $agent -> smgr_id,
					'smgr_name' => empty($agent -> smgr_id) ? '': $agent -> smgr -> name,
					'account' => $agent -> account,
					'pwd' => $agent -> pwd,
					'contact' => $agent -> contact,
					'remark' => $agent -> remark,
					'create_time' => $agent -> create_time,
					'last_time' => $agent -> last_time,
					'flag' => $agent -> flag,
					'status' => $agent -> status,
					'login_time' => $agent -> login_time,
					'login_ip' => $agent -> login_ip,
					'points' => $agent -> points,
					'if_subaccount' => $agent -> if_subaccount,
					'withdraw_cash' => $agent -> withdraw_cash,
					'discount' => $agent -> discount,
					'if_show_old' => $agent -> if_show_old,
                    'contract_status' => $agent -> contract_status,
                    'pay_type' => $agent -> pay_type,
			);
			return json_encode($result);
		}else{
			//没有找到合作商
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该合作商不存在';
			return json_encode($result);
		}
	}


	//修改密码
	/*$agentId 合作商id 必填
	 * $oldPwd 旧密码 必填
	 * $pwd 新密码 必填
	 * */
	public function editAgentPwd($agentId,$oldPwd,$pwd){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$flag = 0;
		//验证合作商名
		if(!isset($agentId) || empty($agentId)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数agentId缺失';
			$flag = 1;
		}
		//验证账号
		if(!isset($oldPwd) || empty($oldPwd)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg  = $errMsg.'参数oldPwd缺失';
			$flag = 1;
		}
		//验证密码
		if(!isset($pwd) || empty($pwd)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg  = $errMsg.'参数pwd缺失';
			$flag = 1;
		}
		if($flag == 1){
			$result['errMsg'] = $errMsg;
			return json_encode($result);
		}
		$criteria = new CDbCriteria();
		$criteria->addCondition('id = :id');
		$criteria->params[':id'] = $agentId;
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;
		$criteria->addCondition('pwd = :pwd');
		$criteria->params[':pwd'] = $oldPwd;
		$agent = Agent::model()->find($criteria);
		if($agent){
			$agent -> pwd = $pwd;
			if($agent -> update()){
				$result['status'] = ERROR_NONE;
				return json_encode($result);
			}
		}else{
			//没有找到合作商
			$result['status'] = ERROR_PARAMETER_FORMAT;
			$result['errMsg'] = '该合作商不存在';
			return json_encode($result);
		}

	}

	//合作商登录
	/*
	 * $account 账号 必填
	 * $pwd 密码 必填
	 * */
	public function Login($account,$pwd){
		//返回结果
		$result = array('status'=>'null','errMsg'=>'null','data'=>'null');
		$flag = 0;
		//验证合作商名
		if(!isset($account) || empty($account)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg = '参数account缺失';
			$flag = 1;
		}
		//验证账号
		if(!isset($pwd) || empty($pwd)){
			$result['status'] = ERROR_PARAMETER_MISS;
			$errMsg  = $errMsg.'参数pwd缺失';
			$flag = 1;
		}

		if($flag == 1){
			$result['errMsg'] = $errMsg;
			return json_encode($result);
		}

		$agent = Agent::model() -> find('account = :account and pwd = :pwd and status = :status and flag = :flag',array(
				':account' => $account,
				':pwd' => $pwd,
				':status' => AGENT_STATUS_NORMAL,
				':flag' => FLAG_NO
		));
		if($agent){
			$agent -> login_time = new CDbExpression('now()');
			if(isset($agent -> pid) && !empty($agent -> pid)){
				//$role = AGENT_RULE_SUBACCOUNT;
				$agent_p = Agent::model() -> find('id=:id and flag = :flag',array(
						':id' => $agent -> pid,
						':flag' => FLAG_NO
				));
                $parentagent_name = $agent_p -> name;
			}else{
				//$role = AGENT_RULE_ADMIN;
				$parentagent_name = '';
			}

			if($agent -> update()){
                $order_status = 3;
                $order_res = AgentOrder::model()->find('agent_id = :agent_id and flag = :flag', array(':agent_id' => $agent -> id, ":flag" => FLAG_NO));
                if (!empty($order_res)) {
                    $order_status = $order_res -> pay_status;
                }
                $result['status'] = ERROR_NONE;
                $result['data'] = array(
                    'id' => $agent -> id,
                    'account'=>$agent -> account,
                    'name' => $agent -> name,
                    'points' => $agent -> points,
                    'role' => $agent -> role,
                    'parentagent_name' => $parentagent_name,
                    'if_subaccount' => $agent -> if_subaccount,
                    'smgr_id' => $agent -> smgr_id,
                    'agent_type' => $agent -> agent_type,
                    'audit_status' => $agent -> audit_status,
                    'if_recommend' => $agent -> if_recommend,
                    'contract_status' => $agent -> contract_status,
                    'pay_status' => $order_status,
                    'pay_type' => $agent -> pay_type
				);
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '登录失败，账号或密码不正确';
		}
        return json_encode($result);
	}

	/**
	 * 找回密码
	 * $mobile  分销商手机号
	 * $newPwd  分销商新密码
	 */
	public function retrieve($mobile,$newPwd)
	{
		$result = array();
		$model = Agent::model()->find('account=:account',array(':account'=>$mobile));
		if(!empty($model)){
			$model -> pwd = md5($newPwd);
			$model -> last_time = date('Y-m-d H:i:s');
			if($model -> save()){
				$result['status'] = ERROR_NONE;
			}else{
				$result['status'] = ERROR_SAVE_FAIL;
				$result['errMsg'] = '密码修改失败';
			}
		}else{
			$result['status'] = ERROR_NO_DATA;
			$result['errMsg'] = '商户不存在';
		}
		return  json_encode($result);
	}

	/**
	 * 验证分销商手机号是否存在
	 * $mobile  分销商手机号
	 */
	public function isMobile($mobile)
	{
		$model = Agent::model()->find('account=:account and flag = :flag',array(':account'=>$mobile, ':flag' => FLAG_NO));
		if(count($model)>0){ //存在
			return true;
		}else{ //不存在
			return false;
		}
	}

	/**
	 * 验证公司名称是否重复
	 */
	public function isCompany($company)
	{
		$model = Agent::model()->find('name=:name',array(':name'=>$company));
		if(count($model)>0){ //存在
			return true;
		}else{ //不存在
			return false;
		}
	}

	/**
	 * 判断该分销商是否有父分销商
	 * return true:是总分销     false：不是总分销
	 */
	public function isFather($agent_id)
	{
		$model = Agent::model()->findByPk($agent_id);
		if(empty($model -> pid)){ //父id为空
			return true;
		}else{ //父id不为空
			return false;
		}
	}

	/**
	 * 获取上级信息
	 */
	public function getFatherInfo($agent_id)
	{
		$result = array();
		$data = array();

		$model = Agent::model()->findByPk($agent_id);
		if(!empty($model->pid)){
			$list = Agent::model()->findByPk($model->pid);
			$data['list']['father'] = 'YES';
			$data['list']['name'] = $list -> name;
			$data['list']['id'] = $model -> id;
		}else{
			$data['list']['father'] = 'NO';
		}
		$result['data'] = $data;
		return json_encode($result);
	}


	/*
	 * 获取合作商推荐列表
	 * $audit_status 审核状态
	 * */
	public function getNewAgentList($audit_status='',$agentname='',$pagentname=''){
		$result = array();
		try {
			$criteria = new CDbCriteria();
			$criteria->addCondition('agent_type = :agent_type');
			$criteria->params[':agent_type'] = AGENT_TYPE_NEW;
			$criteria->addCondition('role = :role');
			$criteria->params[':role'] = AGENT_ROLE_AGENT;
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;
			if(!empty($audit_status)){
				$criteria->addCondition('audit_status = :audit_status');
				$criteria->params[':audit_status'] = $audit_status;
			}
			//合作商名搜索
			if(!empty($agentname)){
				//根据合作商名模糊匹配找到所有的合作商id
				$criteria_agent = new CDbCriteria();
				$criteria_agent->addCondition("name like '%$agentname%'");
				$agent = Agent::model() -> findAll($criteria_agent);
				$agent_id = array();
				foreach ($agent as $k => $v){
					$agent_id[$k] = $v -> id;
				}
				$criteria -> addInCondition('id',$agent_id);
			}

			//上级合作商名搜索
			if(!empty($pagentname)){
				//根据上级合作商名模糊匹配找到所有的合作商id
				$criteria_pagent = new CDbCriteria();
				$criteria_pagent->addCondition("name like '%$pagentname%'");
				$pagent = Agent::model() -> findAll($criteria_pagent);
				$pagent_id = array();
				foreach ($pagent as $k => $v){
					$pagent_id[$k] = $v -> id;
				}
				$criteria -> addInCondition('pid',$pagent_id);
			}

			$criteria->order = 'create_time DESC';

			//分页信息
			$pages = new CPagination(Agent::model()->count($criteria));
			$pages->pageSize = Yii::app() -> params['perPage'];
			$pages->applyLimit($criteria);
			$this->page = $pages;
            if (!empty($audit_status)) {
                $criteria -> addCondition('audit_status = :audit_status');
                $criteria -> params[':audit_status'] = $audit_status;
            }

			//未提交支付宝的合同数量
			$wait_agent = count(Agent::model() -> findAll('agent_type =:agent_type and flag=:flag and audit_status = :audit_status',array(
					':agent_type' => AGENT_TYPE_NEW,
					':flag' => FLAG_NO,
					':audit_status' => AGENT_AUDIT_STATUS_WAIT
			)));

			//未提交支付宝的合同数量
			$pass_agent = count(Agent::model() -> findAll('agent_type =:agent_type and flag=:flag and audit_status = :audit_status',array(
					':agent_type' => AGENT_TYPE_NEW,
					':flag' => FLAG_NO,
					':audit_status' => AGENT_AUDIT_STATUS_PASS
			)));


			$agent = Agent::model() -> findAll($criteria);
			$data = array();
			if(!empty($agent)){
				foreach ($agent as $k => $v){
					$data['list'][$k]['id'] = $v -> id;
					$data['list'][$k]['name'] = $v -> name;
					if(!empty($v -> pid)){
						$pagent = Agent::model() -> findByPk($v -> pid);
						$data['list'][$k]['pid_name'] = $pagent -> name;
					}else{
						$data['list'][$k]['pid_name'] = '';
					}
					$data['list'][$k]['cooperation_grade'] = $v -> cooperation_grade;
					$data['list'][$k]['contact_person'] = $v -> contact_person;
					$data['list'][$k]['account'] = $v -> account;
					$data['list'][$k]['audit_status'] = $v -> audit_status;
					$data['list'][$k]['cooperation_area'] = $v -> cooperation_area;
				}
			}else{
				$data['list'] = array();
			}
			$data['wait_agent'] = $wait_agent;
			$data['pass_agent'] = $pass_agent;
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;

		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}

	/*
	 * 合作商驳回
	 * $agent_id 合作商id 必填
	 * */
	public function rejectAgent($agent_id,$reject_remark){
		$result = array();
		try {
			$agent = Agent::model() -> findByPk($agent_id);
			if(!empty($agent)){
				if($agent -> audit_status == AGENT_AUDIT_STATUS_WAIT){
					$agent -> audit_status = AGENT_AUDIT_STATUS_REJECT;
					$agent -> reject_remark = $reject_remark;
					if($agent -> update()){
						$result['status'] = ERROR_NONE;
					}else{
						throw new Exception('合作商驳回失败');
					}
				}else{
					throw new Exception('该合作商无法被驳回');
				}
			}else{
				throw new Exception('该合作商不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}

	/*
	 * 合作商通过审核
	 * $agent_id 合作商id 必填
	 * */
	public function passAgent($agent_id){
		$result = array();
		try {
			$agent = Agent::model() -> findByPk($agent_id);
			if(!empty($agent)){
				if($agent -> audit_status == AGENT_AUDIT_STATUS_WAIT){
					$agent -> audit_status = AGENT_AUDIT_STATUS_PASS;
					if($agent -> update()){
						$result['status'] = ERROR_NONE;
					}else{
						throw new Exception('合作商审核通过失败');
					}
				}else{
					throw new Exception('该合作商无法被通过审核');
				}
			}else{
				throw new Exception('该合作商不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}


	/*
	 * 获取推荐合作商详情
	 * $agent_id 合作商id 必填
	 * */
	public function getNewAgentDetails($agent_id){
		$result = array();
		try {
			$agent = Agent::model() -> findByPk($agent_id);
			if(!empty($agent)){
				$data = array();
				$data['id'] = $agent -> id;
				$data['name'] = $agent -> name;
				$data['account'] = $agent -> account;
				$data['contact_person'] = $agent -> contact_person;
				$data['e_mail'] = $agent -> e_mail;
				$data['cooperation_grade'] = $agent -> cooperation_grade;
				$data['cooperation_area'] = $agent -> cooperation_area;
				$data['cooperation_type'] = $agent -> cooperation_type;
				$data['img'] = $agent -> img;
				$data['company_address'] = $agent -> company_address;
				$data['company_scale'] = $agent -> company_scale;
				$data['scope_of_business'] = $agent -> scope_of_business;
				$data['business_resources'] = $agent -> business_resources;
				$data['audit_status'] = $agent -> audit_status;
				$data['reject_remark'] = $agent -> reject_remark;
				if(!empty($agent -> pid)){
					$pagent = Agent::model() -> findByPk($agent -> pid);
					$data['pagent_name'] = $pagent -> name;
				}
				$result['status'] = ERROR_NONE;
				$result['data'] = $data;
			}else{
				throw new Exception('该合作商不存在');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}


	/**
	 * 添加    推荐下级合作商
	 * $agent_id 合作商id
	 * $account  注册手机号
	 * $pwd 初始密码
	 * $e_mail  邮箱
	 * $cooperation_grade  合作等级
	 * $selector_province  合作区域 省份
	 * $selector_city    合作区域 城市
	 * $selector_area    合作区域 地区
	 * $contact_person  联系人
	 * $cooperation_type  合作商类型
	 * $business_license  资料图片
	 * $name  公司名称
	 * $area_province  公司地址 省份
	 * $area_city      公司地址 城市
	 * $area_area      公司地址 地区
	 * $company_address   详细地址
	 * $company_scale     团队规模
	 * $scope_of_business 公司业务范围
	 * $business_resources 线下商户资源
	 */
	public function addRecommend($agent_id,$account,$pwd,$e_mail,$cooperation_grade,
			$selector_province,$selector_city,$selector_area,$contact_person,$cooperation_type,$business_license,
			$name,$area_province,$area_city,$area_area,$company_address,$company_scale,$scope_of_business,$business_resources)
	{
		$result = array();
		$model = new Agent();

		$model -> pid = $agent_id;
		$model -> account = $account;
		$model -> pwd = md5($pwd);
		$model -> e_mail = $e_mail;
		$model -> cooperation_grade = $cooperation_grade; //合作等级

		if($cooperation_grade == COOPERATIVE_LEVEL_PROVINCE){ //省级
			$model -> cooperation_area = $selector_province;
		}elseif ($cooperation_grade == COOPERATIVE_LEVEL_CITY){ //市级
			$model -> cooperation_area = $selector_province.','.$selector_city;
		}elseif ($cooperation_grade == COOPERATIVE_LEVEL_AREA){ //区 县
			$model -> cooperation_area = $selector_province.','.$selector_city.','.$selector_area; //合作区域
		}

		$model -> contact_person = $contact_person; //联系人
		$model -> cooperation_type = $cooperation_type; //合作类型
		$model -> img = $business_license; //资料图片
		$model -> name = $name; //公司名称
		$model -> company_address = $area_province.','.$area_city.','.$area_area.','.$company_address; //公司地址
		$model -> company_scale = $company_scale; //团队规模
		$model -> scope_of_business = $scope_of_business; //公司业务范围
		$model -> business_resources = $business_resources; //线下商户资源
		$model -> agent_type = AGENT_TYPE_NEW; //合作商类型  1旧的 2新的
		$model -> ppid = $this -> getFatherId($agent_id); //父父id
		$model -> audit_status = AGENT_AUDIT_STATUS_WAIT; //合作商审核状态
		$model -> create_time = date('Y-m-d H:i:s');

		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result ['errMsg'] = '';
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}

		return json_encode($result);
	}


	/**
	 * 获取  推荐的合作商列表
	 * $time  申请时间搜索
	 * $cooperation_grade  合作等级搜索
	 * $keyword  合作商名称或者手机号搜索
	 * $audit_status  合作商审核状态搜索
	 */
	public function getPartnersList($time,$cooperation_grade,$keyword,$audit_status)
	{
		$result = array();
		$data = array();

		$criteria = new CDbCriteria();
		$criteria -> addCondition('flag=:flag and agent_type=:agent_type and pid=:pid');
		$criteria -> params = array(':flag'=>FLAG_NO,':agent_type'=>AGENT_TYPE_NEW,':pid'=>Yii::app() -> session['agent_id']);
		$criteria -> order = 'create_time desc';

		//搜索
		if(!empty($time)){
			$time_array = explode('-', $time);
			$criteria -> addBetweenCondition('create_time', $time_array[0].' 00:00:00', $time_array[1].' 23:59:59');
		}

		if(!empty($cooperation_grade)){
			$criteria -> addCondition('cooperation_grade=:cooperation_grade');
			$criteria -> params[':cooperation_grade'] = $cooperation_grade;
		}

		if(!empty($keyword)){
			$criteria -> addCondition("name like '%$keyword%' or account like '%$keyword%'");
		}

		if(!empty($audit_status)){
			$criteria -> addCondition('audit_status=:audit_status');
			$criteria -> params[':audit_status'] = $audit_status;
		}


		//待审核的代理商数量
		$wait_agent = count(Agent::model() -> findAll('pid=:pid and agent_type =:agent_type and flag=:flag and audit_status = :audit_status',array(
				':agent_type' => AGENT_TYPE_NEW,
				':flag' => FLAG_NO,
				':audit_status' => AGENT_AUDIT_STATUS_WAIT,
				':pid' => Yii::app() -> session['agent_id']
		)));

		//通过审核的代理商数量
		$pass_agent = count(Agent::model() -> findAll('pid=:pid and agent_type =:agent_type and flag=:flag and audit_status = :audit_status',array(
				':agent_type' => AGENT_TYPE_NEW,
				':flag' => FLAG_NO,
				':audit_status' => AGENT_AUDIT_STATUS_PASS,
				':pid' => Yii::app() -> session['agent_id']
		)));

		//驳回的代理商数量
		$reject_agent = count(Agent::model() -> findAll('pid=:pid and agent_type =:agent_type and flag=:flag and audit_status = :audit_status',array(
				':agent_type' => AGENT_TYPE_NEW,
				':flag' => FLAG_NO,
				':audit_status' => AGENT_AUDIT_STATUS_REJECT,
				':pid' => Yii::app() -> session['agent_id']
		)));

		$result['wait_agent'] = $wait_agent;
		$result['pass_agent'] = $pass_agent;
		$result['reject_agent'] = $reject_agent;

		//分页
		$pages = new CPagination(Agent::model()->count($criteria));
		$pages -> pageSize = Yii::app() -> params['perPage'];
		$pages -> applyLimit($criteria);
		$this -> page = $pages;

		$model = Agent::model()->findAll($criteria);
		$data = array();
		if(isset($model)){
			foreach ( $model as $k => $v ) {
				$data['list'][$k]['id'] = $v -> id;
				$data['list'][$k]['account'] = $v -> account;
				$data['list'][$k]['create_time'] = $v -> create_time;
				$data['list'][$k]['cooperation_grade'] = $v -> cooperation_grade; //合作等级
				$data['list'][$k]['contact_person'] = $v -> contact_person; //联系人
				$data['list'][$k]['audit_status'] = $v -> audit_status; //合作商审核状态
				$data['list'][$k]['name'] = $v -> name; //公司名称
			}
			$result['data'] = $data;
			$result ['status'] = ERROR_NONE; // 状态码
			$result ['errMsg'] = ''; // 错误信息

		}else{
			$data['list'] = array();
			$result['data'] = $data;
			$result ['status'] = ERROR_NO_DATA; // 状态码
			$result ['errMsg'] = '无此数据'; // 错误信息
		}
		return json_encode($result);
	}

	/**
	 * 获取推荐合作商详情
	 * $agent_id  推荐合作商id
	 */
	public function getDetail($agent_id)
	{
		$result = array();
		$data = array();

		$model = Agent::model()->findByPk($agent_id);

		if(!empty($model)){
			$result['status'] = ERROR_NONE;
			$result['errMsg'] = '';

			$data['list']['id'] = $model -> id;
			$data['list']['account'] = $model -> account;
			$data['list']['name'] = $model -> name;
			$data['list']['e_mail'] = $model -> e_mail;
			$data['list']['cooperation_grade'] = $model -> cooperation_grade; //合作等级
			$data['list']['cooperation_area'] = $model -> cooperation_area; //合作区域
			$data['list']['contact_person'] = $model -> contact_person; //联系人
			$data['list']['cooperation_type'] = $model -> cooperation_type; //合作类型 1公司 2个人
			$data['list']['img'] = $model -> img; //资料图片
			$data['list']['company_address'] = $model -> company_address; //公司地址
			$data['list']['company_scale'] = $model -> company_scale; //团队规模
			$data['list']['scope_of_business'] = $model -> scope_of_business; //公司业务范围
			$data['list']['business_resources'] = $model -> business_resources; //线下商户资源
			$data['list']['audit_status'] = $model -> audit_status; //合作商审核状态
			$data['list']['reject_remark'] = $model -> reject_remark; //驳回理由

		}else{
			$result ['status'] = ERROR_NO_DATA; // 状态码
			$result ['errMsg'] = '无此数据'; // 错误信息
		}

		$result['data'] = $data;
		return json_encode($result);
	}


	/**
	 * 修改合作商
	 * $agent_id  推荐合作商id
	 * $account  注册手机号
	 * $pwd 初始密码
	 * $e_mail  邮箱
	 * $cooperation_grade  合作等级
	 * $selector_province  合作区域 省份
	 * $selector_city    合作区域 城市
	 * $selector_area    合作区域 地区
	 * $contact_person  联系人
	 * $cooperation_type  合作商类型
	 * $business_license  资料图片
	 * $name  公司名称
	 * $area_province  公司地址 省份
	 * $area_city      公司地址 城市
	 * $area_area      公司地址 地区
	 * $company_address   详细地址
	 * $company_scale     团队规模
	 * $scope_of_business 公司业务范围
	 * $business_resources 线下商户资源
	 */
	public function edit($agent_id,$account,$pwd,$e_mail,$cooperation_grade,
			$selector_province,$selector_city,$selector_area,$contact_person,$cooperation_type,$business_license,
			$name,$area_province,$area_city,$area_area,$company_address,$company_scale,$scope_of_business,$business_resources)
	{
		$model = Agent::model()->findByPk($agent_id);
		$result = array();
		$model -> account = $account;
		if($pwd != $model -> pwd){ //密码输入框值变了
		    $model -> pwd = md5($pwd);
		}else{ //密码输入框值没变
			$model -> pwd = $pwd;
		}
		$model -> e_mail = $e_mail;
		$model -> cooperation_grade = $cooperation_grade; //合作等级

	    if($cooperation_grade == COOPERATIVE_LEVEL_PROVINCE){ //省级
			$model -> cooperation_area = $selector_province;
		}elseif ($cooperation_grade == COOPERATIVE_LEVEL_CITY){ //市级
			$model -> cooperation_area = $selector_province.','.$selector_city;
		}elseif ($cooperation_grade == COOPERATIVE_LEVEL_AREA){ //区 县
			$model -> cooperation_area = $selector_province.','.$selector_city.','.$selector_area; //合作区域
		}elseif ($cooperation_grade == COOPERATIVE_LEVEL_COUNTRY){
			$model -> cooperation_area = '';
		}

		$model -> contact_person = $contact_person; //联系人
		$model -> cooperation_type = $cooperation_type; //合作类型
		$model -> img = $business_license; //资料图片
		$model -> name = $name; //公司名称
		$model -> company_address = $area_province.','.$area_city.','.$area_area.','.$company_address; //公司地址
		$model -> company_scale = $company_scale; //团队规模
		$model -> scope_of_business = $scope_of_business; //公司业务范围
		$model -> business_resources = $business_resources; //线下商户资源
		$model -> agent_type = AGENT_TYPE_NEW; //合作商类型  1旧的 2新的
		$model -> audit_status = AGENT_AUDIT_STATUS_WAIT; //合作商审核状态
		$model -> last_time = date('Y-m-d H:i:s');

		if($model -> save()){
			$result ['status'] = ERROR_NONE;
			$result ['errMsg'] = '';
		}else{
			$result ['status'] = ERROR_SAVE_FAIL; // 状态码
			$result ['errMsg'] = '数据保存失败'; // 错误信息
		}

		return json_encode($result);

	}

	/**
	 * 获取合作商父id
	 */
	public function getFatherId($agent_id)
	{
		$model = Agent::model()->findByPk($agent_id);
		return $model -> pid;
	}

	/**
	 * 获取值为$id的合作商对象
	 */
	public function getModel($agent_id)
	{
		$model = Agent::model()->findByPk($agent_id);
		return $model;
	}

	/*
	 * 获取下级合作商及下下级合作商列表
	 * $agent_id 合作商id 必填
	 * $start_time - $end_time 搜索时间段
	 * */
	public function getSubAgent($agent_id,$time = '',$name = ''){
		$result = array();
		try {
			$criteria = new CDbCriteria();
			//合作商名称搜索
            if(isset($name) && !empty($name)){
                $criteria->addCondition("name like '%$name%'");
            }

			//新合作商
			$criteria->addCondition('agent_type = :agent_type');
			$criteria->params[':agent_type'] = AGENT_TYPE_NEW;

			//审核状态为已开通
			$criteria->addCondition('audit_status = :audit_status');
			$criteria->params[':audit_status'] = AGENT_AUDIT_STATUS_OPEN;

            //父父ID
			$criteria->addCondition('ppid = :ppid');
			$criteria->params[':ppid'] = $agent_id;

            //删除标志位：正常
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;

			//按时间排序（倒序）
			$criteria -> order = 'create_time desc';

			//找到所有符合的二级合作商
			$subagent = Agent::model() -> findAll($criteria);
			$data = array();
			$allcount = 0;
			if(!empty($subagent)){
				foreach ($subagent as $k => $v){
					if(!isset($data['list'][$v -> pid]['father']) || empty($data['list'][$v -> pid]['father'])){
						$father_arr = array();
						$criteria_father = new CDbCriteria();
						//合作商名称搜索
						if(isset($name) && !empty($name)){
							$criteria_father -> addCondition("name like '%$name%'");
						}
						$criteria_father->addCondition('id = :id');
						$criteria_father->params[':id'] = $v -> pid;
						$father_agent = Agent::model() -> find($criteria_father);
						if(empty($father_agent)){
							$father_agent = Agent::model() -> findByPk($v -> pid);
							$father_arr['is_show'] = false;
						}else{
							$father_arr['is_show'] = true;
						}
						$father_arr['id'] = $father_agent -> id;
						$father_arr['name'] = $father_agent -> name;
						$father_arr['achievement'] = $this->countAgentAchievement($father_agent -> id,$time);
						$allcount += $father_arr['achievement']*0.06;
						$data['list'][$v -> pid]['father'] = $father_arr;
					}
					$son_arr = array();
					$son_arr['id'] = $v -> id;
					$son_arr['name'] = $v -> name;
					$son_arr['achievement'] = $this->countAgentAchievement($v -> id,$time);
					$allcount += $son_arr['achievement']*0.04;
					$data['list'][$v -> pid]['son'][$v -> id] = $son_arr;
				}
			}else{
				$data['list'] = array();
			}

			//找到所有符合的一级合作商
			$criteria = new CDbCriteria();

			//合作商名称搜索
			if(isset($name) && !empty($name)){
				$criteria->addCondition("name like '%$name%'");
			}
			//新合作商
			$criteria->addCondition('agent_type = :agent_type');
			$criteria->params[':agent_type'] = AGENT_TYPE_NEW;

			//审核状态为已开通
			$criteria->addCondition('audit_status = :audit_status');
			$criteria->params[':audit_status'] = AGENT_AUDIT_STATUS_OPEN;

            //父ID
			$criteria->addCondition('pid = :pid');
			$criteria->params[':pid'] = $agent_id;

            //删除标志位：正常
			$criteria->addCondition('flag = :flag');
			$criteria->params[':flag'] = FLAG_NO;

			//按时间排序(倒序)
			$criteria -> order = 'create_time desc';

			$agent = Agent::model() -> findAll($criteria);
			foreach ($agent as $k => $v){
				if(!isset($data['list'][$v -> id]['father']) || empty($data['list'][$v -> id]['father'])){
					$father_arr = array();
					$father_arr['id'] = $v -> id;
					$father_arr['name'] = $v -> name;
					$father_arr['is_show'] = true;
					$father_arr['achievement'] = $this->countAgentAchievement($v -> id,$time);
					$allcount += $father_arr['achievement']*0.06;
					$data['list'][$v -> id]['father'] = $father_arr;
					$data['list'][$v -> id]['son'] = array();
				}
			}
			$data['count'] = $allcount;
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;

		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}

    /**
     * @param $agent_id
     * @param $time
     * @return int
     * 计算合作商业绩（已付款，订单状态不是已退款的，订单玩券管家版本价格的40%的总和）
     */
	private function countAgentAchievement($agent_id,$time){
		$criteria = new CDbCriteria();
		//时间段搜索
		if(!empty($time)){
			$time_array = explode('-', $time);
			$criteria -> addBetweenCondition('create_time', $time_array[0].' 00:00:00', $time_array[1].' 23:59:59');
		}

		//支付状态为已支付
		$criteria->addCondition('pay_status = :pay_status');
		$criteria->params[':pay_status'] = GJORDER_PAY_STATUS_PAID;

		//订单状态不是已退款状态
		$criteria->addCondition('order_status != :order_status');
		$criteria->params[':order_status'] = GJORDER_STATUS_REFUND;

		//没被删除
		$criteria->addCondition('flag = :flag');
		$criteria->params[':flag'] = FLAG_NO;

		//合作商id
		$criteria->addCondition('agent_id = :agent_id');
		$criteria->params[':agent_id'] = $agent_id;

		$order = GjOrder::model() -> findAll($criteria);
		$count = 0;
		foreach ($order as $k => $v){
			$count += $v -> gjproduct -> price * NEW_AGENT_DISCOUNT;
		}
		return $count;
	}

	/**
	 * 根据账号获取代理商信息
	 * $account  账号
	 */
	public function getInfoForAccount($account)
	{
		$model = Agent::model()->find('account=:account',array(':account'=>$account));
		return $model;
	}


	/*
	 * 开启推荐下级代理商功能
	 * */
	public function openRecommend($agent_id){
		$result = array();
		try {
			$agent = Agent::model() -> findByPk($agent_id);
			if(!empty($agent)){
				//判断开启情况
				if($agent -> if_recommend == IF_RECOMMEND_CLOSE){
					$agent -> if_recommend = IF_RECOMMEND_OPEN;
				}elseif ($agent -> if_recommend == IF_RECOMMEND_OPEN){
					$agent -> if_recommend = IF_RECOMMEND_CLOSE;
				}
				if($agent -> update()){
					$result['status'] = ERROR_NONE; //状态码
					$result['errMsg'] = '成功'; //错误信息
				}else{
					$result['status'] = ERROR_SAVE_FAIL; //状态码
					throw new Exception('代理商更新失败');
				}

			}else{
				$result['status'] = ERROR_NO_DATA; //状态码
				throw new Exception('无此代理商');
			}
		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}


	/*
	 * 获取佣金表信息
	 * */
	public function getCommissionList($agent_id,$date='',$merchant_name){
		$result = array();
		try {
			$criteria = new CDbCriteria;
			$criteria->addInCondition('agent_id',$agent_id);

			if(!empty($merchant_name)){
				$criteria->addCondition("merchant_name like '%$merchant_name%'");
			}

			if(!empty($date)){
				$criteria->addCondition("date = :date");
				$criteria->params[':date']=$date;
			}
// 			//分页
// 			$pages = new CPagination(Commission::model()->count($criteria));
// 			$pages -> pageSize = Yii::app() -> params['perPage'];
// 			$pages -> applyLimit($criteria);
// 			$this -> page = $pages;

			$commission = Commission::model() -> findAll($criteria);
			$data = array();
			$countamount = 0;
			$countcommission = 0;
			$agent_arr = array();
			foreach ($commission as $k => $v){
				if(!isset($data[$v -> merchant_id]['amount'])){
					$data[$v -> merchant_id]['amount'] = 0;
				}

				if(!isset($data[$v -> merchant_id]['commission'])){
					$data[$v -> merchant_id]['commission'] = 0;
				}
				$data[$v -> merchant_id]['merchant_name'] = $v -> merchant_name;
				$data[$v -> merchant_id]['agent_name'] = $v -> agent -> name;
				$data[$v -> merchant_id]['amount'] += $v -> amount;
				$data[$v -> merchant_id]['commission'] += $v -> commission;
				$countamount += $v -> amount;
				$countcommission += $v -> commission;
				$agent_arr[$v -> agent_id] = $v -> agent -> name;
			}
			$result['status'] = ERROR_NONE;
			$result['data'] = $data;
			$result['all_amount'] = $countamount;
			$result['all_commission'] = $countcommission;
			$result['agent'] = $agent_arr;

		} catch (Exception $e) {
			$result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
			$result['errMsg'] = $e->getMessage(); //错误信息
		}
		return json_encode($result);
	}

    /**
     * 刷新当前页面
     * $merchant_name 商户名
     * $amount 交易额
     * $commission 佣金
     * $complete 交易完成时间
     * $arent_name 代理商名称
     */
    public function ImportExcel($merchant_name,$amount,$commission,$complete,$arent_name)
    {
        $result = array();
        $transaction= Yii::app()->db->beginTransaction();
        $errMsg='';
        try
        {
            $flag=true;
            $date=date('Y-m-d H:i:s');
            if(!empty($merchant_name)&&is_array($merchant_name))
            {
                for($i=0;$i<count($merchant_name);$i++)
                {
                    for($j=0;$j<count($merchant_name[$i]);$j++) {
                        $com = new AlipayCommission();
                        $com->merchant_name = trim($merchant_name[$i][$j]);
                        //根据商户名查找id
                        $merchant=Merchant::model()->find('name=:name and flag=:flag',array(':name'=>trim($merchant_name[$i][$j]),':flag'=>FLAG_NO));
                        if(empty($merchant))
                        {
                            $flag = false;//保存失败
                            $errMsg="商户不存在";
                            break;
                        }
                        $com->merchant_id=$merchant->id;
                        $com->amount = $amount[$i][$j];
                        $com->commission = $commission[$i][$j];
                        $d = date('Y-m-d H:i:s', $complete[$i][$j]);
                        $com->business_completion_time = strtotime($d);//转换成时间戳
                        $com->create_time = $date;
                        if (!$com->save()) {
                            $flag = false;//保存失败
                            $errMsg="保存失败";
                            break;
                        }
                    }
                    if($flag==false)
                        break;
                }
            }else
            {
                $flag=false;
            }

            if($flag) {
                $transaction->commit();
                $result['status'] = ERROR_NONE;
            }
            else {
                $transaction->rollback();
                $result['errMsg']=$errMsg;
                $result['status'] = ERROR_SAVE_FAIL;
            }
        }catch (Exception $e) {
            $transaction->rollback();
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商户佣金列表
     */
    public function CommissionList($start_time,$end_time,$agent_name)
    {
        $result = array();
        try{
            $criteria=new CDbCriteria();
            $criteria->addCondition('flag=:flag');
            $criteria->params['flag']=FLAG_NO;
//            $criteria->order='business_completion_time desc';
            if(!empty($start_time)&&!empty($end_time))
            {
                $criteria->addCondition('business_completion_time<=:end_time');
                $criteria->params['end_time']=$end_time;

                $criteria->addCondition('business_completion_time>=:start_time');
                $criteria->params['start_time']=$start_time;
            }

            if(!empty($agent_name)&&!empty($agent_name))
            {
                $criteria->addCondition("merchant_name like '%$agent_name%'");
            }

            $pages = new CPagination(AlipayCommission::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $this->page = $pages;

            $alipayCommission=AlipayCommission::model()->findAll($criteria);
            $data=array();
            if(!empty($alipayCommission))
            {
                foreach($alipayCommission as $key=>$value)
                {
                    $data[$key]['merchant_id']=$value['merchant_id'];
                    $data[$key]['merchant_name']=$value['merchant_name'];
                    $data[$key]['amount']=$value['amount'];
                    $data[$key]['commission']=$value['commission'];
                    $data[$key]['business_completion_time']=$value['business_completion_time'];
                }
                if(!empty($data))
                {
                    $result['data']=$data;
                    $result['status']=ERROR_NONE;
                }else
                {
                    $result['data']=null;
                    $result['status']=ERROR_NO_DATA;
                }
            }else
            {
                $result['data']=null;
                $result['status']=ERROR_NO_DATA;
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 佣金详情
     */
    public function CommissionDetails($id,$merchant_name,$start_time,$end_time)
    {
        $result = array();
        try{

            $criteria=new CDbCriteria();
            //三表联查  ,wq_alipay_commission as commission  ,wq_merchant as merchant
            $condition="(agent.gid like '%/$id/%' or agent.id='$id') and merchant.id=commission.merchant_id and agent.id=merchant.agent_id";
            if(!empty($merchant_name))
            {
                $condition=$condition." and commission.merchant_name like '%$merchant_name%'";
            }
            if(!empty($start_time)&&!empty($end_time))
            {
                $condition=$condition." and commission.business_completion_time>='$start_time'";
                $condition=$condition." and commission.business_completion_time<='$end_time'";
            }
            $count=Yii::app()->db->createCommand()
                ->select("count(*)")
                ->from("wq_agent as agent")
                ->leftJoin('wq_merchant as merchant','agent.id=merchant.agent_id')
                ->leftJoin('wq_alipay_commission as commission','merchant.id=commission.merchant_id')
                ->where($condition)
                ->queryScalar();
            $pages = new CPagination($count);
            $pages->pageSize = Yii::app() -> params['perPage'];
            $pages->applyLimit($criteria);
            $data = Yii::app()->db->createCommand()
                ->select("commission.*,merchant.name as name")
                ->from("wq_agent as agent")
                ->leftJoin('wq_merchant as merchant','agent.id=merchant.agent_id')
                ->leftJoin('wq_alipay_commission as commission','merchant.id=commission.merchant_id')
                ->where($condition)
                ->order("business_completion_time desc")
                ->offset($pages->currentPage*$pages->pageSize)//分页查询起始位置
                ->limit($pages->pageSize) //每次查询条数
                ->queryAll();
            $this->page=$pages;
            if(!empty($data))
            {
                $result['data']=$data;
                $result['count']=$count;
                $result['status']=ERROR_NONE;
                $result['errMsg']='';
            }else{
                $result['data']=$data;
                $result['count']=$count;
                $result['status']=ERROR_NO_DATA;
                $result['errMsg']='无此数据';
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 获取商户总交易金额佣金
     */
    public function getCommissionData($id,$merchant_name,$start_time,$end_time){
        $result = array();
        try{
            //三表联查  ,wq_alipay_commission as commission  ,wq_merchant as merchant
            $condition="(agent.gid like '%/$id/%' or agent.id='$id') and merchant.id=commission.merchant_id and agent.id=merchant.agent_id";
            $data = Yii::app()->db->createCommand()
                ->select("commission.*,merchant.name as name")
                ->from("wq_agent as agent")
                ->leftJoin('wq_merchant as merchant','agent.id=merchant.agent_id')
                ->leftJoin('wq_alipay_commission as commission','merchant.id=commission.merchant_id')
                ->where($condition)
                ->queryAll();

            if(!empty($merchant_name))
            {
                $condition=$condition." and commission.merchant_name like '%$merchant_name%'";
            }
            if(!empty($start_time)&&!empty($end_time))
            {
                $condition=$condition." and commission.business_completion_time>='$start_time'";
                $condition=$condition." and commission.business_completion_time<='$end_time'";
            }
            $model= Yii::app()->db->createCommand()
                ->select("commission.*,merchant.name as name")
                ->from("wq_agent as agent")
                ->leftJoin('wq_merchant as merchant','agent.id=merchant.agent_id')
                ->leftJoin('wq_alipay_commission as commission','merchant.id=commission.merchant_id')
                ->where($condition)
                ->queryAll();

            $commission_sum=0;
            $amount_sum=0;
            $merchant_arr=array();
            if(!empty($data))
            {
                if(!empty($model))
                {
                    foreach($model as $k=>$v)
                    {
                        $amount_sum=$amount_sum+floatval($v['amount']);
                        $commission_sum=$commission_sum+floatval($v['commission']);
                    }
                }

                $result['amount_sum']=$amount_sum;//佣金总额
                $result['commission_sum']=$commission_sum;//佣金总额
                $result['merchant_arr']=$merchant_arr;
                $result['status']=ERROR_NONE;
                $result['errMsg']='';
            }else{
                $result['amount_sum']=$amount_sum;//佣金总额
                $result['commission_sum']=$commission_sum;//佣金总额
                $result['merchant_arr']=$merchant_arr;
                $result['status']=ERROR_NO_DATA;
                $result['errMsg']='无此数据';
            }
        }catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }
/*******************************分销系统20160408******************************************/
    /**
     * 激活码验证
     */
    public function checkCode($activation_code) {
        $result = array();

        try {
            //参数验证
            if (empty($activation_code)) {
                $result['status'] = ERROR_PARAMETER_FORMAT;
                throw new Exception('参数code不能为空');
            }

            $criteria = new CDbCriteria();
            $criteria -> addCondition('activation_code = :activation_code');
            $criteria -> params['activation_code'] = $activation_code;
            $criteria -> addCondition('flag = :flag');
            $criteria -> params[':flag'] = FLAG_NO;
            $criteria -> addCondition('activation_code_status = :activation_code_status');
            $criteria ->  params[':activation_code_status'] = CODE_STATUS_NOT_USED;
            $model = Agent::model() -> find($criteria);

            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('无效的激活码');
            }

            if ($model -> activation_code_status != CODE_STATUS_NOT_USED) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('激活码已被使用');
            }

            $result['data'] = array('id' => $model ->id);
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $account
     * @param $pwd
     * @return string
     * 代理商注册
     */
    public function fxRegister($id, $account, $pwd, $activation_code) {
        $result = array();
        //代理商id
        if (!isset($id) || empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            return json_encode($result);
        }
        //账号
        if (!isset($account) || empty($account)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数account缺失';
            return json_encode($result);
        } else {
            $agent = Agent::model() -> find('account = :account and flag = :flag', array(
                ':account' => $account,
                ':flag' => FLAG_NO,
            ));
            if (!empty($agent)) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                $result['errMsg'] = 'account';
                return json_encode($result);
            }
        }
        //密码
        if (!isset($pwd) || empty($pwd)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数pwd缺失';
            return json_encode($result);
        }
        //激活码
        if (!isset($activation_code) || empty($activation_code)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数activation_code缺失';
            return json_encode($result);
        }
        try{
            $agent = Agent::model() -> find("id = :id and flag = :flag", array(":id" =>  $id, ':flag' => FLAG_NO));
            if (empty($agent)){
                throw new Exception('数据为空');
            }
            if ($activation_code != $agent -> activation_code){
                throw new Exception('激活码不匹配');
            }
            $agent -> account = $account;
            $agent -> pwd = $pwd;
            $agent -> activation_code_status = CODE_STATUS_USED;

            if ($agent -> update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 签约合同
     */
    public function agreeContract($id, $contract_status){
        $result = array();
        //验证id
        if (!isset($id) || empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            return json_encode($result);
        }
        try {
            $model = Agent::model() -> find('id = :id and flag = :flag', array(':id' => $id, ':flag' => FLAG_NO));
           if (empty($model)){
               throw new Exception('数据为空');
           }
            $model -> id = $id;
            $model -> contract_status = 2;

            if ($model -> update()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取未支付订单
     */
    public function getOrder($id) {
        $result = array();

        $criteria = new CDbCriteria();
        $criteria -> addCondition("id = :id");
        $criteria -> params[':id'] = $id;
        $criteria -> addCondition('flag = :flag');
        $criteria -> params[':flag'] = FLAG_NO;
        $order = AgentOrder::model();

        if ($order) {
            $result['status'] = ERROR_NONE;
            $result['data'] = array(
                'id' => $order -> agent_id,
                'order_no' => $order -> order_no,
                'pay_status' => $order -> pay_status,
            );
        }

        return json_encode($result);
    }

    /**
     * 生成订单
     */
    public function createAgentOrder($id) {
        $result = array();

        $order = new AgentOrder();
        $league_fee = Agent::model() -> find('id = :id', array(':id' => $id))->league_fee;
        $cash_deposit = Agent::model() -> find('id = :id', array(':id' => $id))->cash_deposit;
        $total = $league_fee + $cash_deposit;

        $order -> order_no = $this -> getOrderNo();
        $order -> agent_id = $id;
        $order -> pay_status = FX_PAY_STATUS_NUPAID;
        $order -> pay_money = $total;
        $order -> create_time = new CDbExpression('now()');

        try {
            if ($order -> save()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
                $result['data']['id'] = $order -> id;
                $result['data']['order_no'] = $order->order_no;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '订单保存失败';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @return string
     * 生成加盟订单号
     */
    private function getOrderNo()
    {
        $flag = 1;
        do{
            $Code = 'AO'.date('Ymd').$this -> getRandChar(4);
            $agentOrder= AgentOrder::model() -> find('order_no = :order_no', array(':order_no' => $Code));
            if(empty($agentOrder)) {
                $flag = 2;
            }
        } while($flag == 1);

        return $Code;
    }

    private function getRandChar($length){
        $str = null;
        $strPol = "0123456789";
        $max = strlen($strPol)-1;

        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /**
     * @param $pay_type
     * @param $invoice_type
     * @param $invoice_title
     * @param $invoice_content
     * @param $invoice_address
     * @param $invoice_person
     * @param $invoice_phone
     * @param $remittance_name
     * @param $remittance_account
     * @param $remittance_bank
     * @return string
     * 签约打款
     */
    public function fxToPay($id, $pay_type, $invoice_type, $invoice_title, $invoice_content, $invoice_address, $invoice_person, $invoice_phone, $remittance_name, $remittance_account, $remittance_bank) {
        $result = array();

        if (!isset($id) || empty($id)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数id缺失';
            return json_encode($result);
        }
        try{
            $model = Agent::model() -> find('id = :id and flag = :flag', array(':id' => $id, ':flag' => FLAG_NO));
            if (empty($model)){
                throw new Exception('数据为空');
            }

            $model -> pay_type = $pay_type;
            $model -> invoice_type = $invoice_type;
            $model -> invoice_title = $invoice_title;
            $model -> invoice_content = $invoice_content;
            $model -> invoice_address = $invoice_address;
            $model -> invoice_person = $invoice_person;
            $model -> invoice_phone = $invoice_phone;
            $model -> remittance_name = $remittance_name;
            $model -> remittance_account = $remittance_account;
            $model -> remittance_bank = $remittance_bank;

            if ($model -> save()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsg'] = '数据保存失败';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @param $name
     * @param $business_license
     * @param $selector_province
     * @param $selector_city
     * @param $selector_area
     * @param $address
     * @param $legal_person
     * @param $legal_person_id
     * @param $legal_person_id_card_positive
     * @param $legal_person_id_card_opposite
     * @param $contact_name
     * @param $contact
     * @param $contact_email
     * @param $partner
     * @param $league_type
     * @param $store_num
     * @param $league_fee_type
     * @param $league_start_time
     * @param $league_end_time
     * @param $directly_operated_ratio
     * @param $first_level_ratio
     * @param $second_level_ratio
     * @param $smgr_id
     * @return string
     * 添加加盟商相关信息
     */
    public function   stepOne($name, $business_license, $selector_province, $selector_city, $selector_area, $address, $legal_person, $legal_person_id, $legal_person_id_card_positive, $legal_person_id_card_opposite, $contact_name, $contact, $contact_email, $partner, $league_type, $league_fee_type, $league_start_time, $league_end_time, $directly_operated_ratio, $first_level_ratio, $second_level_ratio, $smgr_id, $league_fee, $cash_deposit, $team_num, $advantage, $scope_of_service) {
        $result = array();

        try {
            $model = new Agent();

            $model -> name = $name; //公司名称
            $model -> business_license = $business_license; //营业执照
            $model -> address = $selector_province . ',' . $selector_city . ',' . $selector_area . ',' . $address; //公司地址
            $model -> legal_person = $legal_person; //法人
            $model -> legal_person_id = $legal_person_id; //法人身份证号
            $model -> legal_person_id_card_positive = $legal_person_id_card_positive; //身份证正面照
            $model -> legal_person_id_card_opposite = $legal_person_id_card_opposite; //身份证反面照
            $model -> contact_name = $contact_name; //联系人
            $model -> contact = $contact; //联系人电话
            $model -> contact_email = $contact_email; //联系人邮箱
            $model -> partner = $partner; //合作方
            $model -> league_type = $league_type; //加盟类型
            $model -> league_fee_type = $league_fee_type; //加盟费类型
            $model -> league_start_time = $league_start_time; //加盟开始时间
            $model -> league_end_time = $league_end_time; //加盟结束时间
            $model -> directly_operated_ratio = $directly_operated_ratio; //直营服务费分成比例
            $model -> first_level_ratio = $first_level_ratio; //一级下线服务费分成比例
            $model -> second_level_ratio = $second_level_ratio; //二级下线服务费分成比例
            $model -> smgr_id = $smgr_id; //客服经理
            $model -> activation_code = $this -> getActivationCode(); //获取激活码
            $model -> create_time = new CDbExpression('now()'); //创建时间
            $model -> league_fee = $league_fee; //加盟费
            $model -> cash_deposit = $cash_deposit; //合作费
            $model -> team_num = $team_num; //团队人数
            $model -> advantage = $advantage; //优势说明
            $model -> scope_of_service = $scope_of_service; //服务范围

            //推荐商户号
//            $Cri = new CDbCriteria();
//            $Cri->select = 'account';
//            $Cri->addCondition('flag = :flag');
//            $Cri->params[':flag'] = FLAG_NO;
//            $list = Agent::model()->findAll($Cri);
//            $agent = array();
//            foreach($list as $k => $v){
//                $agent[$k] = $v->account;
//            }

            if ($model -> save()) {
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = '';
                $result['data']['id'] = $model->id;
                $result['data']['code'] = $model -> activation_code;
            } else {
                $result['status'] = ERROR_SAVE_FAIL;
                $result['errMsh'] = '数据保存失败';
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * @return string
     * 生成激活码
     */
    private function getActivationCode()
    {
        $Code = date('Ymd', time()) . rand(10000000, 99999999);
        $ModelCode = Agent::model()->find('activation_code = :activation_code', array(':activation_code' => $Code));
        if (!empty($ModelCode)) {
            while ($Code == $ModelCode->activation_code) {
                $Code = date('Ymd', time()) . rand(10000000, 99999999);
                $ModelCode = Agent::model()->find('activation_code = :activation_code', array(':activation_code' => $Code));
            }
        }
        return $Code;
    }

    /**
     * 获取激活码
     */
    public function getCodeMsg($id)
    {
        $result = array();

        $criteria = new CDbCriteria();
        $criteria -> addCondition('id = :id');
        $criteria -> params[':id'] = $id;
        $criteria -> addCondition('flag = :flag');
        $criteria -> params[':flag'] = FLAG_NO;
        $agent = Agent::model()->find($criteria);

        if($agent){
            $result['status'] = ERROR_NONE;
            $result['data'] = array(
                'id' => $agent -> id,
                'activation_code' => $agent -> activation_code,
                'smgr_id' => $agent -> smgr_id,
                'flag' => $agent -> flag,
            );
            return json_encode($result);
        }else{
            //没有找到版本
            $result['status'] = ERROR_PARAMETER_FORMAT;
            $result['errMsg'] = '未找到激活码';
            return json_encode($result);
        }
    }

    /**
     * 加盟商列表
     */
    public function toAgentList($agentId='', $keyword='', $address='', $if_subaccount='', $time='', $time2='', $arrow=1, $arrow_type=1, $pay_status) {
        $result = array();
        $criteria = new CDbCriteria();

        //上级加盟商名称
        if (isset($agentId) && !empty($agentId)) {
            $criteria -> addCondition("gid like '%/$agentId/%'");
        }
        //关键词搜索(服务运营商名称或者账号)
        if (isset($keyword) && !empty($keyword)) {
            $criteria -> addCondition("name like '%$keyword%' or account like '%$keyword%'");
        }
        //根据省市区搜索
        if (isset($address) && !empty($address)) {
            $criteria -> addCondition("address like '%$address%'");
        }
        //是否开启子账号
        if(isset($if_subaccount) && !empty($if_subaccount)){
            $criteria->addCondition('if_subaccount = :if_subaccount');
            $criteria->params[':if_subaccount'] = $if_subaccount;
        }
        //根据加盟时间段进行搜索
        if(!empty($time)){
            $Time = explode('-', $time);
            $criteria -> addBetweenCondition('league_start_time', trim($Time[0]) . ' 00:00:00', trim($Time[1]) . ' 23:59:59');
        }
        //根据到期时间段进行搜索
        if(!empty($time2)){
            $Time2 = explode('-', $time2);
            $criteria -> addBetweenCondition('league_end_time', trim($Time2[0]) . ' 00:00:00', trim($Time2[1]) . ' 23:59:59');
        }
        //根据交易时间段进行搜索
        if(!empty($time3)){
            $Time3 = explode('-', $time3);
            $criteria -> addBetweenCondition('date', trim($Time3[0]) . ' 00:00:00', trim($Time3[1]) . ' 23:59:59');
        }

        //即将到期
        $criteria -> addCondition('flag = :flag');
        $criteria -> params[':flag'] = FLAG_NO;
        //查询（支付）状态
        if (isset ($pay_status) && !empty($pay_status)) {
            $criteria->addCondition ('pay_status = :pay_status' );
            $criteria->params [':pay_status'] = $pay_status;
        }

        //按创建时间倒序排列
        $criteria -> order = 'create_time DESC';

        $cri = new CDbCriteria();
        $cri -> addCondition('flag = :flag');
        $cri -> params[':flag'] = FLAG_NO;
        //根据筛选条件进行排序
        switch ($arrow_type) {
            case $arrow_type = 1: //按加盟时间排序
                if ($arrow == 1) {
                    $criteria->order = 'league_start_time DESC';//排序条件，降序
                } else {
                    $criteria->order = 'league_start_time ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 2: //按商户总数排序
                if ($arrow == 1) {
                    $cri->order = 'total_merchant_num DESC';//排序条件，降序
                } else {
                    $cri->order = 'total_merchant_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 3: //按门店总数排序
                if ($arrow == 1) {
                    $criteria->order = 'total_store_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_store_num ASC';//排序条件，升序
                }
                break;
//                按收银版商户数排序
            case $arrow_type = 4: //按营销版商户数排序
                if ($arrow == 1) {
                    $criteria->order = 'total_yx_merchant_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_yx_merchant_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 5: //按总交易量排序
                if ($arrow == 1) {
                    $criteria->order = 'total_trade_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_trade_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 6: //按支付宝交易量排序
                if ($arrow == 1) {
                    $criteria->order = 'total_trade_alipay_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_trade_alipay_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 7: //按微信交易量排序
                if ($arrow == 1) {
                    $criteria->order = 'total_trade_wechat_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_trade_wechat_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 8: //按银联交易量排序
                if ($arrow == 1) {
                    $criteria->order = 'total_trade_unionpay_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_trade_unionpay_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 9: //按会员数排序
                if ($arrow == 1) {
                    $criteria->order = 'total_new_member_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_new_member_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 10: //按支付宝粉丝数排序
                if ($arrow == 1) {
                    $criteria->order = 'total_new_alipay_fans_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_new_alipay_fans_num ASC';//排序条件，升序
                }
                break;
            case $arrow_type = 11: //按微信粉丝数排序
                if ($arrow == 1) {
                    $criteria->order = 'total_new_wechat_fans_num DESC';//排序条件，降序
                } else {
                    $criteria->order = 'total_new_wechat_fans_num ASC';//排序条件，升序
                }
                break;
        }

        //分页
        $pages = new CPagination(Agent::model()->count($criteria));
        $pages -> pageSize = Yii::app() -> params['perPage'];
        $pages -> applyLimit($criteria);
        $this -> page = $pages;
        $model = Agent::model() -> findAll($criteria);

        //待付款服务运营商数量
        $dfk_agent = Agent::model() -> count('pay_status = :pay_status and flag = :flag', array(
            ':pay_status' => AGENT_PAY_STATUS_UNPAID,
            ':flag' => FLAG_NO
        ));
        //待确认服务运营商数量
        $dqr_agent = Agent::model() -> count('pay_status = :pay_status and flag = :flag', array(
            ':pay_status' => AGENT_PAY_STATUS_WAITING_CONFIRM,
            ':flag' => FLAG_NO
        ));
        //已付款服务运营商数量
        $yfk_agent = Agent::model() -> count('pay_status = :pay_status and flag = :flag', array(
            ':pay_status' => AGENT_PAY_STATUS_PAID,
            ':flag' => FLAG_NO
        ));

        $res = AStatistics::model() -> findAll('agent_id = :agent_id', array(':agent_id' => $agentId));
        $data_res = array();

        $data = array();
        $data_sum = array();
        foreach($model as $k => $v) {
            $data['list'][$k]['id'] = $v -> id;
            //加盟商号
            $data['list'][$k]['name'] = $v -> name;
            $data['list'][$k]['account'] = $v -> account;
            $data['list'][$k]['pid'] = $v -> pid;
            $data['list'][$k]['gid'] = $v -> gid;
            $data['list'][$k]['address'] = $v -> address;
            $data['list'][$k]['create_time'] = $v -> create_time;
            $data['list'][$k]['if_subaccount'] = $v -> if_subaccount;
            $data['list'][$k]['scope_of_service'] = $v -> scope_of_service;
            $data['list'][$k]['activation_code'] = $v -> activation_code;
            $data['list'][$k]['league_start_time'] = $v -> league_start_time;
            $data['list'][$k]['league_end_time'] = $v -> league_end_time;
            $data['list'][$k]['pay_status'] = $v -> pay_status;
            if(isset($v -> pid) && !empty($v -> pid)){
                $parentagent = Agent::model() -> find('id=:id and flag =:flag',array(
                    ':id' => $v -> pid,
                    ':flag' => FLAG_NO
                ));
                if($parentagent){
                    $data['list'][$k]['parentagent_name'] = $parentagent -> name;
                }
            }

            foreach($res as $kk => $vv) {
                $data_res['list'][$kk]['id'] = $vv -> id;
                $data_res['list'][$kk]['agent_id'] = $data['list'][$k]['id'];
                $data_res['list'][$kk]['total_merchant_num'] = $vv -> total_merchant_num;
                $data_res['list'][$kk]['total_store_num'] = $vv -> total_store_num;
                $data_res['list'][$kk]['total_yx_merchant_num'] = $vv -> total_yx_merchant_num;
                $data_res['list'][$kk]['total_trade_num'] = $vv -> total_trade_num;
                $data_res['list'][$kk]['total_trade_alipay_num'] = $vv -> total_trade_alipay_num;
                $data_res['list'][$kk]['total_trade_wechat_num'] = $vv -> total_trade_wechat_num;
                $data_res['list'][$kk]['total_trade_unionpay_num'] = $vv -> total_trade_unionpay_num;
                $data_res['list'][$kk]['total_new_member_num'] = $vv -> total_new_member_num;
                $data_res['list'][$kk]['total_new_alipay_fans_num'] = $vv -> total_new_alipay_fans_num;
                $data_res['list'][$kk]['total_new_wechat_fans_num'] = $vv -> total_new_wechat_fans_num;
            }

            //根据$agent_id从AStatistics表中查询数据
            $sum = AStatistics::model() -> find('agent_id = :agent_id and date like :date and flag = :flag', array(
                ':agent_id' => $v -> id,
                ':date' => date("Y-m-d",strtotime("-1 day")).'%', //'%'模糊查询,满足日期即可
                ':flag' => FLAG_NO
            ));
            if(!empty($sum)) {
                $data_sum['total_merchant_num'] = $sum -> total_merchant_num;
                $data_sum['total_store_num'] = $sum -> total_store_num;
                $data_sum['total_yx_merchant_num'] = $sum -> total_yx_merchant_num;
                $data_sum['total_trade_num'] = $sum -> total_trade_num;
                $data_sum['total_trade_alipay_num'] = $sum -> total_trade_alipay_num;
                $data_sum['total_trade_wechat_num'] = $sum -> total_trade_wechat_num;
                $data_sum['total_trade_unionpay_num'] = $sum -> total_trade_unionpay_num;
                $data_sum['total_new_member_num'] = $sum -> total_new_member_num;
                $data_sum['total_new_alipay_fans_num'] = $sum -> total_new_alipay_fans_num;
                $data_sum['total_new_wechat_fans_num'] = $sum -> total_new_wechat_fans_num;
            } else {
                $data_sum['total_merchant_num'] = 0;
                $data_sum['total_store_num'] = 0;
                $data_sum['total_yx_merchant_num'] = 0;
                $data_sum['total_trade_num'] = 0;
                $data_sum['total_trade_alipay_num'] = 0;
                $data_sum['total_trade_wechat_num'] = 0;
                $data_sum['total_trade_unionpay_num'] = 0;
                $data_sum['total_new_member_num'] = 0;
                $data_sum['total_new_alipay_fans_num'] = 0;
                $data_sum['total_new_wechat_fans_num'] = 0;
            }

            $data['list'][$k]['total_merchant_num'] = $data_sum['total_merchant_num'];
            $data['list'][$k]['total_store_num'] = $data_sum['total_store_num'];
            $data['list'][$k]['total_yx_merchant_num'] = $data_sum['total_yx_merchant_num'];
            $data['list'][$k]['total_trade_num'] = $data_sum['total_trade_num'];
            $data['list'][$k]['total_trade_alipay_num'] = $data_sum['total_trade_alipay_num'];
            $data['list'][$k]['total_trade_wechat_num'] = $data_sum['total_trade_wechat_num'];
            $data['list'][$k]['total_trade_unionpay_num'] = $data_sum['total_trade_unionpay_num'];
            $data['list'][$k]['total_new_member_num'] = $data_sum['total_new_member_num'];
            $data['list'][$k]['total_new_alipay_fans_num'] = $data_sum['total_new_alipay_fans_num'];
            $data['list'][$k]['total_new_wechat_fans_num'] = $data_sum['total_new_wechat_fans_num'];
            //商户数量
        }
        $result['status'] = ERROR_NONE;
        $result['errMsg'] = '';
        $data['dfk_agent'] = $dfk_agent;
        $data['dqr_agent'] = $dqr_agent;
        $data['yfk_agent'] = $yfk_agent;
        $result['data'] = $data;
        $result['data_sum'] = $data_sum;
        $result['data_res'] = $data_res;

        return json_encode($result);
    }

    /**
     * 加盟商详情
     */
    public function toAgentDetails($agentId) {
        $result = array();

        //判断agentId是否存在
        if (!isset($agentId) || empty($agentId)) {
            $result['status'] = ERROR_PARAMETER_MISS;
            $result['errMsg'] = '参数agentId缺失';
            return json_encode($result);
        }

        $criteria = new CDbCriteria();
        $criteria -> addCondition('id = :id');
        $criteria -> params[':id'] = $agentId;
        $criteria -> addCondition('flag = :flag');
        $criteria -> params[':flag'] = FLAG_NO;
        $agent = Agent::model() -> find($criteria);

        if ($agent) {
            $result['status'] = ERROR_NONE;
            $result['data'] = array(
                'id' => $agent -> id,
                'name' => $agent -> name,
                'pid' => $agent -> pid,
                'gid' => $agent -> gid,
                'parentname' => empty($agent -> pid) ? '' : Agent::model() -> findByPk($agent -> pid) -> name,
                //加盟商号
                'address' => $agent -> address,
                'business_license' => $agent -> business_license,
                'legal_person' => $agent -> legal_person,
                'legal_person_id' => $agent -> legal_person_id,
                'legal_person_id_card_positive' => $agent -> legal_person_id_card_positive,
                'legal_person_id_card_opposite' => $agent -> legal_person_id_card_opposite,
                'contact_name' => $agent -> contact_name,
                'contact' => $agent -> contact,
                'contact_email' => $agent -> contact_email,
                'league_type' => $agent -> league_type,
                'league_start_time' => $agent -> league_start_time,
                'league_end_time' => $agent -> league_end_time,
                'directly_operated_ratio' => $agent -> directly_operated_ratio,
                'first_level_ratio' => $agent -> first_level_ratio,
                'second_level_ratio' => $agent -> second_level_ratio,
                'if_subaccount' => $agent -> if_subaccount,
                'remittance_name' => empty($agent -> remittance_name) ? '暂无信息' : $agent -> remittance_name,
                'remittance_account' => empty($agent -> remittance_account) ? '暂无信息' : $agent -> remittance_account,
                'remittance_bank' => empty($agent -> remittance_bank) ? '暂无信息' : $agent -> remittance_bank,
                'league_fee' => $agent -> league_fee,
                'cash_deposit' => $agent -> cash_deposit,
                'total' => $agent -> league_fee + $agent -> cash_deposit,
                'team_num' => $agent -> team_num,
                'scope_of_service' => $agent -> scope_of_service,
                'advantage' => $agent -> advantage,
                'smgr_id' => $agent -> smgr_id,
                'smgr_name' => $agent -> smgr -> name,
            );

            return json_encode($result);
        } else {
            $result['status'] = ERROR_PARAMETER_FORMAT;
            $result['errMsg'] = '未找到请求的数据';

            return json_encode($result);
        }


    }

    /**
     * 判断付款方式
     */
    public function getJudge($id) {
        $result = array();

        $criteria = new CDbCriteria();
        $criteria -> addCondition('id = :id');
        $criteria -> params[':id'] = $id;
        $criteria -> addCondition('flag = :flag');
        $criteria -> params[':flag'] = FLAG_NO;
        $agent = Agent::model()->find($criteria);

        if ($agent) {
            $result['status'] = ERROR_NONE;
            $result['data'] = array(
                'id' => $agent -> id,
                'pay_type' => $agent -> pay_type,
            );
        }

        return json_encode($result);
    }



}