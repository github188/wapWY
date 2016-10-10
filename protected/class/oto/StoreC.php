<?php
include_once(dirname(__FILE__) . '/../mainClass.php');

/**
 * 门店类
 *
 */
class StoreC extends mainClass
{
    public $page = null;
    private static $_instance = NULL;

    public static function getInstance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    /**
     * 查询门店列表
     * @param $merchant_id 商户id
     * @param $keyword     搜索关键词（商户名称或商户编号）
     * @return array       返回数组
     */
    public function getStoreList($merchant_id = NULL, $keyword = NULL, $merchant_name = NULL,$manager_id=NULL)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            if (!empty($merchant_id)) {
                $criteria->addCondition('merchant_id = :merchant_id');
                $criteria->params[':merchant_id'] = $merchant_id;
            }
            if (!empty($keyword)) {
                $criteria->addCondition("name like '%$keyword%' or number like '%$keyword%'");
            }

            if (isset($merchant_name) && !empty($merchant_name)) {
                $criteria_merchant = new CDbCriteria();
                $criteria_merchant->addCondition("name like '%$merchant_name%'");
                $criteria_merchant->addCondition('flag = :flag');
                $criteria_merchant->params[':flag'] = FLAG_NO;
                $merchant = Merchant::model()->findAll($criteria_merchant);
                if (!empty($merchant)) {
                    $id = array();
                    foreach ($merchant as $k => $v) {
                        $id[$k] = $v->id;
                    }
                    $criteria->addInCondition('merchant_id', $id);
                }
            }
            
            if(!empty($manager_id)){
                $manager = Manager::model() -> findByPk($manager_id);
                $store_id = $manager -> store_id;
                $storeId = substr($store_id, 1, strlen($store_id) - 2);
                $right_arr = explode(',', $storeId);
                $criteria->addInCondition('id', $right_arr);
            }
            

            //按创建时间排序
            $criteria->order = 'create_time DESC';

            $pages = new CPagination(Store::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $model = Store::model()->findAll($criteria);
            if (!empty($model)) {
                //数据封装
                $data = array();
                foreach ($model as $key => $value) {
                    $data['list'][$key]['id'] = $value['id']; //门店id
                    $data['list'][$key]['number'] = $value['number']; //门店编号
                    $data['list'][$key]['alipay_store_id'] = $value['alipay_store_id']; //支付宝编号
                    $data['list'][$key]['merchant_name'] = $value['merchant']['name']; //门店编号
                    $data['list'][$key]['name'] = $value['name']; //门店名称
                    $data['list'][$key]['address'] = $value['address']; //门店地址
                    $data['list'][$key]['telephone'] = $value['telephone']; //联系电话
                }
                //分页
                $this->page = $pages;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $this->page = $pages;
                $result['status'] = ERROR_NONE; //状态码
                $data['list'] = array();
                $result['data'] = $data;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取门店信息
     */
    public function getStoreInfo($merchant_id = null, $address = null, $keyword = null,$role = null,$manager_id=null)
    {
        $result = array();

        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            if (!empty($address)) {
                $criteria->addSearchCondition('address', $address);
            }

            if (!empty($keyword)) {
                $criteria->addSearchCondition('name', $keyword);
            }

            if (!empty($merchant_id)) {
                $criteria->addCondition('merchant_id = :merchant_id');
                $criteria->params[':merchant_id'] = $merchant_id;
            }

            $stores = array();
            $store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            foreach ($store as $k => $v) {
                $stores[$k] = $v->id;
            }

            if ($role == WQ_ROLE_MANAGER) { //管理员只能操作分配的门店
                $manager = Manager::model() -> findByPk($manager_id);
                $storeId = $manager -> store_id;
                $right_arr = array();
                if (!empty ($storeId)) {
                    $storeId = substr($storeId, 1, strlen($storeId) - 2);
                    $right_arr = explode(',', $storeId);
                }
                if (!empty($right_arr)) {
                    $criteria->addInCondition('id', $right_arr);
                } else { //门店权限为空    可以操作所有门店
                    $criteria->addInCondition('id', $stores);
                }
            } else {  //商户可以操作所有门店
                $criteria->addInCondition('id', $stores);
            }

            //按创建时间排序
            $criteria->order = 'create_time DESC';
            $pages = new CPagination(Store::model()->count($criteria));
            $pages->pageSize = Yii::app()->params['perPage'];
            $pages->applyLimit($criteria);
            $model = Store::model()->findAll($criteria);

            if (!empty($model)) {
                //数据封装
                $data = array();
                foreach ($model as $key => $value) {
                    $data['list'][$key]['id'] = $value->id; //门店id
                    $data['list'][$key]['number'] = $value->number; //门店编号
                    $data['list'][$key]['name'] = $value->name; //门店名称
                    $data['list'][$key]['branch_name'] = $value->branch_name;//分店名称
                    $data['list'][$key]['telephone'] = $value->telephone; //联系电话
                    $data['list'][$key]['address'] = $value->address; //门店地址
                    //$data['list'][$key]['wxpay_qrcode'] = WXPAY_QRCODE_URL.'?store='.$value->id; //微信支付二维码图片地址
                    $sync_type = $value->alipay_sync_type; //口碑同步类型
                    $verify_status = $value->alipay_sync_verify_status; //口碑审核状态

                    $status_desc = '未知';
                    if ($sync_type == STORE_ALIPAY_SYNC_TYPE_SYNC) {
                        if ($verify_status == STORE_ALIPAY_SYNC_STATUS_NONE) {
                            $status_desc = '未开通';
                        }
                        if ($verify_status == STORE_ALIPAY_SYNC_STATUS_AUDITING) {
                            $status_desc = '审核中';
                        }
                        if ($verify_status == STORE_ALIPAY_SYNC_STATUS_REJECT) {
                            $status_desc = '驳回';
                        }
                        if ($verify_status == STORE_ALIPAY_SYNC_STATUS_PASS) {
                            $status_desc = '已开通';
                        }
                    }
                    if ($sync_type == STORE_ALIPAY_SYNC_TYPE_RELATION) {
                        $status_desc = '已关联';
                    }
                    if ($sync_type == STORE_ALIPAY_SYNC_TYPE_NOSYNC_NO_RELATION) {
                        $status_desc = '未开通';
                    }
                    $data['list'][$key]['koubei_status'] = $status_desc; //口碑门店状态
                    $data['list'][$key]['sync_type'] = $value->alipay_sync_type; //门店同步类型
                    //如果没有关联门店
                    if (empty($value->relation_store_id)) {
                        //门店不启用收款账号
                        if ($value->if_alipay_open == IF_ALIPAY_OPEN_CLOSE) {
                            //使用上级收款账号
                            if ($value->alipay_use_pro == IF_USE_PRO_YES) {
                                //获取上级收款账号
                                if (!empty($value->management_id)) {
                                    //如果门店的分组id不为空，则获取门店分组的信息
                                    $model_m = Management::model()->findByPk($value->management_id);
                                    //如果所属门店分组不启用收银且使用上级账号
                                    if ($model_m->if_alipay_open == IF_ALIPAY_OPEN_CLOSE && $model_m->alipay_use_pro == IF_USE_PRO_YES) {
                                        //如果门店分组的上级分组不为空
                                        if (!empty($model_m->p_mid)) {
                                            //获取上级门店分组
                                            $model_m = Management::model()->findByPk($model_m->p_mid);
                                            //如果上级门店分组不启用收银，且使用上级账号
                                            if ($model_m->if_alipay_open == IF_ALIPAY_OPEN_CLOSE && $model_m->alipay_use_pro == IF_USE_PRO_YES) {
                                                $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                            }
                                        } elseif (!empty($model_m->merchant_id)) {
                                            $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                        }
                                    }
                                } elseif (!empty($value->merchant_id)) {//如果商户id不为空，则获取商户
                                    $model_m = Merchant::model()->findByPk($value->merchant_id);
                                }

                                if ($model_m->alipay_api_version == ALIPAY_API_VERSION_1_API) {//1.0
                                    if (isset($model_m->partner)) {
                                        $data['list'][$key]['alipay'] = empty($model_m->partner) ? '未设置' : 'PID:' . $model_m->partner . '(上级账号)';
                                    } else {
                                        $data['list'][$key]['alipay'] = empty($model_m->alipay_pid) ? '未设置' : 'PID:' . $model_m->alipay_pid . '(上级账号)';
                                    }
                                } elseif ($model_m->alipay_api_version == ALIPAY_API_VERSION_2_API) {//2.0
                                    if (isset($model_m->appid)) {
                                        $data['list'][$key]['alipay'] = empty($model_m->appid) ? '未设置' : 'APPID:' . $model_m->appid . '(上级账号)';
                                    } else {
                                        $data['list'][$key]['alipay'] = empty($model_m->alipay_appid) ? '未设置' : 'APPID:' . $model_m->alipay_appid . '(上级账号)';
                                    }
                                } elseif ($model_m->alipay_api_version == ALIPAY_API_VERSION_2_AUTH_API) { //2.0授权
                                    $data['list'][$key]['alipay'] = empty($model_m->alipay_auth_appid) ? '未设置' : 'APPID:' . $model_m->alipay_auth_appid . '(授权账号)';
                                }
                            } elseif ($value->alipay_use_pro == IF_USE_PRO_NO) {
                                //不使用上级收款账号
                                $data['list'][$key]['alipay'] = '未设置';
                            }
                        } elseif ($value->if_alipay_open == IF_ALIPAY_OPEN_OPEN) {
                            //门店启用收款账号
                            //1.0
                            if ($value->alipay_api_version == ALIPAY_API_VERSION_1_API) {
                                if (!empty($value->alipay_pid)) {
                                    $data['list'][$key]['alipay'] = 'PID:' . $value->alipay_pid;
                                } else {
                                    $data['list'][$key]['alipay'] = '未设置';
                                }
                            } else if ($value->alipay_api_version == ALIPAY_API_VERSION_2_API) {
                                //2.0
                                if (!empty($value->alipay_appid)) {
                                    $data['list'][$key]['alipay'] = 'APPID:' . $value->alipay_appid;
                                } else {
                                    $data['list'][$key]['alipay'] = '未设置';
                                }
                            } else {
                                $data['list'][$key]['alipay'] = '未设置';
                            }
                        }

                        //门店不启用收款账号
                        if ($value->if_wx_open == IF_WXPAY_OPEN_NO) {
                            //使用上级收款账号
                            if ($value->wx_use_pro == IF_USE_PRO_YES) {
                                //获取上级收款账号
                                $model_type = '';
                                //获取上级收款账号
                                if (!empty($value->management_id)) {
                                    //如果门店的分组id不为空，则获取门店分组的信息
                                    $model_m = Management::model()->findByPk($value->management_id);
                                    $model_type = 'management';
                                    //如果所属门店分组不启用收银且使用上级账号
                                    if ($model_m->if_wx_open == IF_WXPAY_OPEN_NO && $model_m->wx_use_pro == IF_USE_PRO_YES) {
                                        //如果门店分组的上级分组不为空
                                        if (!empty($model_m->p_mid)) {
                                            //获取上级门店分组
                                            $model_m = Management::model()->findByPk($model_m->p_mid);
                                            $model_type = 'management';
                                            //如果上级门店分组不启用收银，且使用上级账号
                                            if ($model_m->if_wx_open == IF_WXPAY_OPEN_NO && $model_m->wx_use_pro == IF_USE_PRO_YES) {
                                                $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                                $model_type = 'merchant';
                                            }
                                        } elseif (!empty($model_m->merchant_id)) {
                                            $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                            $model_type = 'merchant';
                                        }
                                    }
                                } elseif (!empty($value->merchant_id)) {//如果商户id不为空，则获取商户
                                    $model_m = Merchant::model()->findByPk($value->merchant_id);
                                    $model_type = 'merchant';
                                }


                                if (($model_type == 'merchant' && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) || ($model_type == 'management' && $model_m->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF)) {
                                    if (isset($model_m->wechat_mchid)) {
                                        $data['list'][$key]['wechat'] = empty($model_m->wechat_mchid) ? '未设置' : '商户号:' . $model_m->wechat_mchid . '(上级账号)';
                                    } else {
                                        $data['list'][$key]['wechat'] = empty($model_m->wx_mchid) ? '未设置' : '商户号:' . $model_m->wx_mchid . '(上级账号)';
                                    }
                                } elseif (($model_type == 'merchant' && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) || ($model_type == 'management' && $model_m->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE)) {
                                    if (!isset($model_m->t_wx_mchid)) {
                                        $data['list'][$key]['wechat'] = empty($model_m->wechat_mchid) ? '未设置' : '商户号:' . $model_m->wechat_mchid . '(上级账号)';
                                    } else {
                                        $data['list'][$key]['wechat'] = empty($model_m->t_wx_mchid) ? '未设置' : '商户号:' . $model_m->t_wx_mchid . '(上级账号)';
                                    }
                                }

                            } elseif ($value->wx_use_pro == IF_USE_PRO_NO) {
                                //不使用上级收款账号
                                $data['list'][$key]['wechat'] = '未设置';
                            }
                        } elseif ($value->if_wx_open == IF_ALIPAY_OPEN_OPEN) {
                            //门店启用收款账号
                            //普通商户
                            if ($value->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                                if (!empty($value->wx_mchid)) {
                                    $data['list'][$key]['wechat'] = '商户号:' . $value->wx_mchid;
                                } else {
                                    $data['list'][$key]['wechat'] = '未设置';
                                }

                            } else if ($value->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
                                //特约商户
                                if (!empty($value->t_wx_mchid)) {
                                    $data['list'][$key]['wechat'] = '商户号:' . $value->t_wx_mchid;
                                } else {
                                    $data['list'][$key]['wechat'] = '未设置';
                                }

                            } else {
                                $data['list'][$key]['wechat'] = '未设置';
                            }
                        }
                    } else {//如果关联了其他门店
                        $relation_store = Store::model()->findByPk($value->relation_store_id);
                        //门店不启用收款账号
                        if ($relation_store->if_alipay_open == IF_ALIPAY_OPEN_CLOSE) {
                            //使用上级收款账号
                            if ($relation_store->alipay_use_pro == IF_USE_PRO_YES) {
                                //获取上级收款账号
                                if (!empty($relation_store->management_id)) {
                                    //如果门店的分组id不为空，则获取门店分组的信息
                                    $model_m = Management::model()->findByPk($relation_store->management_id);
                                    //如果所属门店分组不启用收银且使用上级账号
                                    if ($model_m->if_alipay_open == IF_ALIPAY_OPEN_CLOSE && $model_m->alipay_use_pro == IF_USE_PRO_YES) {
                                        //如果门店分组的上级分组不为空
                                        if (!empty($model_m->p_mid)) {
                                            //获取上级门店分组
                                            $model_m = Management::model()->findByPk($model_m->p_mid);
                                            //如果上级门店分组不启用收银，且使用上级账号
                                            if ($model_m->if_alipay_open == IF_ALIPAY_OPEN_CLOSE && $model_m->alipay_use_pro == IF_USE_PRO_YES) {
                                                $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                            }
                                        } elseif (!empty($model_m->merchant_id)) {
                                            $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                        }
                                    }
                                } elseif (!empty($relation_store->merchant_id)) {//如果商户id不为空，则获取商户
                                    $model_m = Merchant::model()->findByPk($relation_store->merchant_id);
                                }

                                if ($model_m->alipay_api_version == ALIPAY_API_VERSION_1_API) {//1.0
                                    if (isset($model_m->partner)) {
                                        $data['list'][$key]['alipay'] = empty($model_m->partner) ? '未设置' : 'PID:' . $model_m->partner;
                                    } else {
                                        $data['list'][$key]['alipay'] = empty($model_m->alipay_pid) ? '未设置' : 'PID:' . $model_m->alipay_pid;
                                    }
                                } elseif ($model_m->alipay_api_version == ALIPAY_API_VERSION_2_API) {//2.0
                                    if (isset($model_m->appid)) {
                                        $data['list'][$key]['alipay'] = empty($model_m->appid) ? '未设置' : 'APPID:' . $model_m->appid;
                                    } else {
                                        $data['list'][$key]['alipay'] = empty($model_m->alipay_appid) ? '未设置' : 'APPID:' . $model_m->alipay_appid;
                                    }
                                } elseif ($model_m->alipay_api_version == ALIPAY_API_VERSION_2_AUTH_API) { //2.0授权
                                    $data['list'][$key]['alipay'] = empty($model_m->alipay_auth_appid) ? '未设置' : 'APPID:' . $model_m->alipay_auth_appid . '(授权账号)';
                                }
                            } elseif ($relation_store->alipay_use_pro == IF_USE_PRO_NO) {
                                //不使用上级收款账号
                                $data['list'][$key]['alipay'] = '未设置';
                            }
                        } elseif ($relation_store->if_alipay_open == IF_ALIPAY_OPEN_OPEN) {
                            //门店启用收款账号
                            //1.0
                            if ($relation_store->alipay_api_version == ALIPAY_API_VERSION_1_API) {
                                if (!empty($relation_store->alipay_pid)) {
                                    $data['list'][$key]['alipay'] = 'PID:' . $relation_store->alipay_pid;
                                } else {
                                    $data['list'][$key]['alipay'] = '未设置';
                                }
                            } else if ($relation_store->alipay_api_version == ALIPAY_API_VERSION_2_API) {
                                //2.0
                                if (!empty($relation_store->alipay_appid)) {
                                    $data['list'][$key]['alipay'] = 'APPID:' . $relation_store->alipay_appid;
                                } else {
                                    $data['list'][$key]['alipay'] = '未设置';
                                }
                            } else {
                                $data['list'][$key]['alipay'] = '未设置';
                            }
                        }


                        //门店不启用收款账号
                        if ($relation_store->if_wx_open == IF_WXPAY_OPEN_NO) {
                            //使用上级收款账号
                            if ($relation_store->wx_use_pro == IF_USE_PRO_YES) {
                                //获取上级收款账号
                                $model_type = '';
                                //获取上级收款账号
                                if (!empty($relation_store->management_id)) {
                                    //如果门店的分组id不为空，则获取门店分组的信息
                                    $model_m = Management::model()->findByPk($relation_store->management_id);
                                    $model_type = 'management';
                                    //如果所属门店分组不启用收银且使用上级账号
                                    if ($model_m->if_wx_open == IF_WXPAY_OPEN_NO && $model_m->wx_use_pro == IF_USE_PRO_YES) {
                                        //如果门店分组的上级分组不为空
                                        if (!empty($model_m->p_mid)) {
                                            //获取上级门店分组
                                            $model_m = Management::model()->findByPk($model_m->p_mid);
                                            $model_type = 'management';
                                            //如果上级门店分组不启用收银，且使用上级账号
                                            if ($model_m->if_wx_open == IF_WXPAY_OPEN_NO && $model_m->wx_use_pro == IF_USE_PRO_YES) {
                                                $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                                $model_type = 'merchant';
                                            }
                                        } elseif (!empty($model_m->merchant_id)) {
                                            $model_m = Merchant::model()->findByPk($model_m->merchant_id);
                                            $model_type = 'merchant';
                                        }
                                    }
                                } elseif (!empty($relation_store->merchant_id)) {//如果商户id不为空，则获取商户
                                    $model_m = Merchant::model()->findByPk($relation_store->merchant_id);
                                    $model_type = 'merchant';
                                }


                                if (($model_type == 'merchant' && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) || ($model_type == 'management' && $model_m->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF)) {
                                    if (isset($model_m->wechat_mchid)) {
                                        $data['list'][$key]['wechat'] = empty($model_m->wechat_mchid) ? '未设置' : '商户号:' . $model_m->wechat_mchid;
                                    } else {
                                        $data['list'][$key]['wechat'] = empty($model_m->wx_mchid) ? '未设置' : '商户号:' . $model_m->wx_mchid;
                                    }
                                } elseif (($model_type == 'merchant' && $model_m->wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) || ($model_type == 'management' && $model_m->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE)) {
                                    if (!isset($model_m->t_wx_mchid)) {
                                        $data['list'][$key]['wechat'] = empty($model_m->wechat_mchid) ? '未设置' : '商户号:' . $model_m->wechat_mchid;
                                    } else {
                                        $data['list'][$key]['wechat'] = empty($model_m->t_wx_mchid) ? '未设置' : '商户号:' . $model_m->t_wx_mchid;
                                    }
                                }

                            } elseif ($relation_store->wx_use_pro == IF_USE_PRO_NO) {
                                //不使用上级收款账号
                                $data['list'][$key]['wechat'] = '未设置';
                            }
                        } elseif ($relation_store->if_wx_open == IF_ALIPAY_OPEN_OPEN) {
                            //门店启用收款账号
                            //普通商户
                            if ($relation_store->wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                                if (!empty($relation_store->wx_mchid)) {
                                    $data['list'][$key]['wechat'] = '商户号:' . $relation_store->wx_mchid;
                                } else {
                                    $data['list'][$key]['wechat'] = '未设置';
                                }

                            } else if ($relation_store->wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
                                //特约商户
                                if (!empty($relation_store->t_wx_mchid)) {
                                    $data['list'][$key]['wechat'] = '商户号:' . $relation_store->t_wx_mchid;
                                } else {
                                    $data['list'][$key]['wechat'] = '未设置';
                                }

                            } else {
                                $data['list'][$key]['wechat'] = '未设置';
                            }
                        }

                    }

                }
                //分页
                $this->page = $pages;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
                $this->page = $pages;
                $result['status'] = ERROR_NONE; //状态码
                $data['list'] = array();
                $result['data'] = $data;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 添加操作员门店列表
     */
    public function StoreList($merchant_id, $management_id = null,$manager_id=null)
    {
        $result = array();//以数组形式返回结果
        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            if (!empty($merchant_id)) {
                //若商户id非空
                $criteria->addCondition('merchant_id = :merchant_id');
                $criteria->params[':merchant_id'] = $merchant_id;
            }

            if (!empty($management_id)) {
                //若管理单元id非空
                if ($management_id == 'merchant') {
                    $criteria->addCondition('management_id is :management_id');
                    $criteria->params[':management_id'] = null;
                } else {
                    $criteria->addCondition('management_id=:management_id');
                    $criteria->params[':management_id'] = $management_id;
                }
            }

            $stores = array();
            $store = Store::model()->findAll('merchant_id = :merchant_id and flag = :flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            foreach ($store as $k => $v) {
                $stores[$k] = $v->id;
            }

            if(!empty($manager_id)){
                $manager = Manager::model() -> findByPk($manager_id);
                $storeId = $manager -> store_id;
                $storeId = substr($storeId, 1, strlen($storeId) - 2);
                $right_arr = explode(',', $storeId);
                $criteria->addInCondition('id', $right_arr);
            }else{
                $criteria->addInCondition('id', $stores);
            }
            

            //按创建时间排序
            $criteria->order = 'create_time DESC';

            $model = Store::model()->findAll($criteria);
            if (!empty($model)) {
                //数据封装
                $data = array();
                foreach ($model as $key => $value) {
                    $data['list'][$key]['id'] = $value['id']; //门店id
                    $data['list'][$key]['number'] = $value['number']; //门店编号
                    $data['list'][$key]['alipay_store_id'] = $value['alipay_store_id']; //支付宝编号
                    $data['list'][$key]['merchant_name'] = $value['merchant']['name']; //门店编号
                    $data['list'][$key]['name'] = $value['name']; //门店名称
                    $data['list'][$key]['branch_name'] = $value['branch_name']; //分店名
                    $data['list'][$key]['address'] = $value['address']; //门店地址
                    $data['list'][$key]['telephone'] = $value['telephone']; //联系电话
                }
                //分页
                //TODO
//                $this->page = $pages;
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
//                $this->page = $pages;
                $result['status'] = ERROR_NONE; //状态码
                $data['list'] = array();
                $result['data'] = $data;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 查询门店详情
     * @param $id     门店id
     * @return array  返回数组
     */
    public function getStoreDetails($id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Store::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('查询的数据不存在');
            }
            //数据封装
            $data = array();
            $data['id'] = $model['id']; //门店id
            $data['number'] = $model['number']; //门店编号
            $data['alipay_store_id'] = $model['alipay_store_id']; //支付宝门店编号

            $data['alipay_seller_id'] = $model['alipay_seller_id']; //卖家支付宝用户id
            $bname = !empty($model['branch_name']) ? '(' . $model['branch_name'] . ')' : '';
            $data['name'] = $model['name'] . $bname; //门店名称
            $data['address'] = $model['address']; //门店地址
            $data['telephone'] = $model['telephone']; //联系电话
            $data['lng'] = $model['lng']; //经度
            $data['lat'] = $model['lat']; //纬度
            $data['open_time'] = $model['open_time']; //营业时间
            $data['logo'] = $model['logo']; //图标
            $data['image'] = $model['image']; //实景图
            $data['is_print'] = $model['is_print']; //是否启用打印机
            $data['introduction'] = $model['introduction']; //门店介绍
            $data['merchant_id'] = $model['merchant_id'];

            $data['merchant_name'] = $model['merchant']['wq_m_name']; //所属商户名称


            $data['alipay_sync_type'] = $model['alipay_sync_type'];
            $data['alipay_sync_time'] = $model['alipay_sync_time'];
            $data['alipay_sync_verify_status'] = $model['alipay_sync_verify_status'];
            $data['audit_desc'] = $model['audit_desc'];
            $data['first_img'] = $model['first_img'];
            $data['brand'] = $model['brand'];
            $data['brand_logo'] = $model['brand_logo'];
            $data['phone_num'] = $model['phone_num'];
            $data['per_capita'] = $model['per_capita'];
            $data['image'] = $model['image'];
            $data['category_id'] = $model['category_id'];
            $data['category'] = $model['category'];
            $data['business_license'] = $model['business_license'];
            $data['auth_letter'] = $model['auth_letter'];
            $data['main_name'] = $model['name'];
            $data['branch_name'] = $model['branch_name'];
            $data['management_id'] = $model['management_id'];
            $data['address_code'] = $model['address_code'];

            $data['licence_code'] = $model['licence_code'];
            $data['licence_name'] = $model['licence_name'];
            $data['business_certificate'] = $model['business_certificate'];
            $data['business_certificate_expires'] = $model['business_certificate_expires'];


            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 添加门店
     * @param $merchant_id    商户id
     * @param $name                门店名称
     * @param $number            门店编号
     * @param $telephone        联系电话
     * @param $address            地址
     * @param $lng                经度
     * @param $lat                纬度
     * @param $open_time        营业时间
     * @param $logo                门店图标
     * @param $image            实景图片
     * @param $introduction        门店介绍
     * @param $alipay_store_id  支付宝门店编号
     * @param $alipay_seller_id 卖家支付宝用户id
     * @return array            返回数组
     */
    public function addStore_old($merchant_id, $name, $number, $telephone, $address, $lng, $lat, $open_time, $logo, $image, $introduction, $alipay_store_id, $alipay_seller_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            //查询门店名称是否重复
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('name = :name');
            $criteria->params[':name'] = $name;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Store::model()->find($criteria);
            if (!empty($model)) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                throw new Exception('门店名称重复');
            }
            $model = new Store();
// 			$model['management_id'] = $management_id;
            $model['merchant_id'] = $merchant_id;
            $model['number'] = $number;
            $model['alipay_store_id'] = $alipay_store_id;
            $model['alipay_seller_id'] = $alipay_seller_id;
            $model['name'] = $name;
            $model['address'] = $address;
            $model['telephone'] = $telephone;
            $model['lng'] = $lng;
            $model['lat'] = $lat;
            $model['open_time'] = $open_time;
            $model['logo'] = $logo;
            $model['image'] = $image;
            $model['introduction'] = $introduction;
            $model['create_time'] = date('Y-m-d H:i:s');

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = array('id' => $model->id);
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 修改门店
     * @param $id                门店id
     * @param $name                门店名称
     * @param $number            门店编号
     * @param $telephone        联系电话
     * @param $address            地址
     * @param $lng                经度
     * @param $lat                纬度
     * @param $open_time        营业时间
     * @param $logo                门店图标
     * @param $image            实景图片
     * @param $introduction        门店介绍
     * @param $alipay_store_id  支付宝门店编号
     * @param $alipay_seller_id 卖家支付宝用户id
     * @return array            返回数组
     */
    public function editStore_old($id, $name, $number, $telephone, $address, $lng, $lat, $open_time, $logo, $image, $introduction, $alipay_store_id, $alipay_seller_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Store::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('修改的门店不存在');
            }
            $model['name'] = $name;
            $model['number'] = $number;
            $model['alipay_store_id'] = $alipay_store_id;
            $model['alipay_seller_id'] = $alipay_seller_id;
            $model['address'] = $address;
            $model['telephone'] = $telephone;
            $model['lng'] = $lng;
            $model['lat'] = $lat;
            $model['open_time'] = $open_time;
            $model['logo'] = $logo;
            $model['image'] = $image;
            $model['introduction'] = $introduction;

            if ($model->save()) {
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
                $result['data'] = '';
            } else {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                $result['errMsg'] = '数据保存失败'; //错误信息
                $result['data'] = '';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 添加门店
     * @param unknown $merchant_id
     * @param unknown $type
     * @param unknown $name
     * @param unknown $number
     * @param unknown $telephone
     * @param unknown $address
     * @param unknown $detail_address
     * @param unknown $lng
     * @param unknown $lat
     * @param unknown $open_time
     * @param unknown $logo
     * @param unknown $image
     * @param unknown $alipay_store_id
     * @param unknown $auth_letter
     * @param unknown $branch_name
     * @param unknown $brand
     * @param unknown $business_license
     * @param unknown $licence_code
     * @param unknown $licence_name
     * @param unknown $certificate
     * @param unknown $certificate_expires
     * @param unknown $category
     * @param unknown $category_id
     * @param unknown $first_img
     * @param unknown $management_id
     * @param unknown $per_capita
     * @param unknown $phone_num
     * @throws Exception
     * @return string
     */
    public function addStore($merchant_id, $type, $name, $number, $telephone, $address, $detail_address, $lng, $lat, $open_time, $logo, $image, $alipay_store_id, $auth_letter, $branch_name, $brand, $business_license, $licence_code, $licence_name, $certificate, $certificate_expires, $category, $category_id, $first_img, $management_id, $per_capita, $phone_num)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction(); //开启事务
        try {
            //参数验证
            //TODO
            //查询门店名称是否重复
            $criteria = new CDbCriteria();
            $criteria->addCondition('merchant_id = :merchant_id');
            $criteria->params[':merchant_id'] = $merchant_id;
            $criteria->addCondition('name = :name');
            $criteria->params[':name'] = $name;
            $criteria->addCondition('branch_name = :branch_name');
            $criteria->params[':branch_name'] = $branch_name;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Store::model()->find($criteria);
            if (!empty($model)) {
                $result['status'] = ERROR_DUPLICATE_DATA;
                throw new Exception('门店名称重复');
            }

            //新建门店
            $model = new Store();
            $model['merchant_id'] = $merchant_id;
            $model['number'] = $number;
            $model['alipay_store_id'] = $alipay_store_id;
            $model['alipay_sync_type'] = $type;
            $model['alipay_sync_time'] = date('Y-m-d H:i:s');
            $model['licence_code'] = $licence_code;
            $model['licence_name'] = $licence_name;
            $model['business_certificate'] = $certificate;
            $model['business_certificate_expires'] = $certificate_expires;
            $model['name'] = $name;
            $model['address_code'] = $address;
            $model['address'] = $detail_address;
            $model['telephone'] = $telephone;
            $model['lng'] = $lng;
            $model['lat'] = $lat;
            $model['open_time'] = $open_time;
            $model['brand_logo'] = $logo;
            $model['image'] = $image;
            $model['auth_letter'] = $auth_letter;
            $model['branch_name'] = $branch_name;
            $model['brand'] = $brand;
            $model['business_license'] = $business_license;
            $model['category'] = $category;
            $model['category_id'] = $category_id;
            $model['first_img'] = $first_img;
            $model['management_id'] = $management_id;
            $model['per_capita'] = $per_capita;
            $model['phone_num'] = $phone_num;
            $model['create_time'] = date('Y-m-d H:i:s');

            if (!$model->save()) {
                throw new Exception('数据保存失败');
            }

            if ($type == STORE_ALIPAY_SYNC_TYPE_SYNC) { //需要同步到口碑
                //待比对参数数组,key:数据库字段名，value:请求字段名
                $list = array(
                    'alipay_store_id' => 'store_id',
                    'category_id' => 'category_id',
                    'brand' => 'brand',
                    'brand_logo' => 'brand_logo', //需处理
                    'name' => 'main_shop_name',
                    'branch_name' => 'branch_shop_name',
                    'address_code' => 'province_code,city_code,district_code,address', //需处理
                    'lng' => 'longitude',
                    'lat' => 'latitude',
                    'telephone' => 'contact_number',
                    'phone_num' => 'notify_mobile',
                    'first_img' => 'main_image', //需处理
                    'image' => 'audit_images', //需处理
                    'open_time' => 'business_time', //需处理
                    'per_capita' => 'avg_price',
                    'business_license' => 'licence', //需处理
                    'licence_code' => 'licence_code',
                    'licence_name' => 'licence_name',
                    'business_certificate' => 'business_certificate', //需处理
                    'business_certificate_expires' => 'business_certificate_expires',
                    'auth_letter' => 'auth_letter', //需处理
                );
                $orgin = array(
                    'alipay_store_id' => $alipay_store_id,
                    'category_id' => $category_id,
                    'brand' => $brand,
                    'brand_logo' => $logo,
                    'name' => $name,
                    'branch_name' => $branch_name,
                    'address_code' => $address,
                    'lng' => $lng,
                    'lat' => $lat,
                    'telephone' => $telephone,
                    'phone_num' => $phone_num,
                    'first_img' => $first_img,
                    'image' => $image,
                    'open_time' => $open_time,
                    'per_capita' => $per_capita,
                    'business_license' => $business_license,
                    'licence_code' => $licence_code,
                    'licence_name' => $licence_name,
                    'business_certificate' => $certificate,
                    'business_certificate_expires' => $certificate_expires,
                    'auth_letter' => $auth_letter
                );
                //提交口碑的请求字段名
                $commit = array();
                foreach ($list as $key => $val) {
                    //添加操作所有字段都是修改的
                    $commit[$val] = $orgin[$key];
                }

                $ret = $this->syncStore($model['alipay_store_id'], $commit);
                if ($ret['status'] != ERROR_NONE) {
                    throw new Exception($ret['errMsg']);
                }
            }

            $transaction->commit();
            $result['status'] = ERROR_NONE;
            $result['store_id'] = $model -> id;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
            $transaction->rollback();
        }

        return json_encode($result);
    }

    /**
     * 修改门店
     * @param unknown $id
     * @param unknown $type
     * @param unknown $name
     * @param unknown $number
     * @param unknown $telephone
     * @param unknown $address
     * @param unknown $detail_address
     * @param unknown $lng
     * @param unknown $lat
     * @param unknown $open_time
     * @param unknown $logo
     * @param unknown $image
     * @param unknown $alipay_store_id
     * @param unknown $auth_letter
     * @param unknown $branch_name
     * @param unknown $brand
     * @param unknown $business_license
     * @param unknown $licence_code
     * @param unknown $licence_name
     * @param unknown $certificate
     * @param unknown $certificate_expires
     * @param unknown $category
     * @param unknown $category_id
     * @param unknown $first_img
     * @param unknown $management_id
     * @param unknown $per_capita
     * @param unknown $phone_num
     * @throws Exception
     * @return string
     */
    public function editStore($id, $type, $name, $number, $telephone, $address, $detail_address, $lng, $lat, $open_time, $logo, $image, $alipay_store_id, $auth_letter, $branch_name, $brand, $business_license, $licence_code, $licence_name, $certificate, $certificate_expires, $category, $category_id, $first_img, $management_id, $per_capita, $phone_num)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction(); //开启事务
        try {
            //参数验证
            //TODO
            //查询门店
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Store::model()->find($criteria);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('修改的门店不存在');
            }
            //原记录
            $old = array(
                'alipay_store_id' => $model['alipay_store_id'],
                'category_id' => $model['category_id'],
                'brand' => $model['brand'],
                'brand_logo' => $model['brand_logo'],
                'name' => $model['name'],
                'branch_name' => $model['branch_name'],
                'address_code' => $model['address_code'],
                'lng' => $model['lng'],
                'lat' => $model['lat'],
                'telephone' => $model['telephone'],
                'phone_num' => $model['phone_num'],
                'first_img' => $model['first_img'],
                'image' => $model['image'],
                'open_time' => $model['open_time'],
                'per_capita' => $model['per_capita'],
                'business_license' => $model['business_license'],
                'licence_code' => $model['licence_code'],
                'licence_name' => $model['licence_name'],
                'business_certificate' => $model['business_certificate'],
                'business_certificate_expires' => $model['business_certificate_expires'],
                'auth_letter' => $model['auth_letter']
            );

            //修改门店
            $model['number'] = $number;
            $model['alipay_store_id'] = $alipay_store_id;
            $model['alipay_sync_type'] = $type;
            $model['alipay_sync_time'] = date('Y-m-d H:i:s');
            $model['licence_code'] = $licence_code;
            $model['licence_name'] = $licence_name;
            $model['business_certificate'] = $certificate;
            $model['business_certificate_expires'] = $certificate_expires;
            $model['name'] = $name;
            $model['address_code'] = $address;
            $model['address'] = $detail_address;
            $model['telephone'] = $telephone;
            $model['lng'] = $lng;
            $model['lat'] = $lat;
            $model['open_time'] = $open_time;
            $model['brand_logo'] = $logo;
            $model['image'] = $image;
            $model['auth_letter'] = $auth_letter;
            $model['branch_name'] = $branch_name;
            $model['brand'] = $brand;
            $model['business_license'] = $business_license;
            $model['category'] = $category;
            $model['category_id'] = $category_id;
            $model['first_img'] = $first_img;
            $model['management_id'] = $management_id;
            $model['per_capita'] = $per_capita;
            $model['phone_num'] = $phone_num;

            if (!$model->save()) {
                throw new Exception('数据保存失败');
            }

            if ($model['alipay_sync_type'] == STORE_ALIPAY_SYNC_TYPE_SYNC) { //需要同步到口碑
                //待比对参数数组,key:数据库字段名，value:请求字段名
                $list = array(
                    'alipay_store_id' => 'store_id',
                    'category_id' => 'category_id',
                    'brand' => 'brand',
                    'brand_logo' => 'brand_logo', //需处理
                    'name' => 'main_shop_name',
                    'branch_name' => 'branch_shop_name',
                    'address_code' => 'province_code,city_code,district_code,address', //需处理
                    'lng' => 'longitude',
                    'lat' => 'latitude',
                    'telephone' => 'contact_number',
                    'phone_num' => 'notify_mobile',
                    'first_img' => 'main_image', //需处理
                    'image' => 'audit_images', //需处理
                    'open_time' => 'business_time', //需处理
                    'per_capita' => 'avg_price',
                    'business_license' => 'licence', //需处理
                    'licence_code' => 'licence_code',
                    'licence_name' => 'licence_name',
                    'business_certificate' => 'business_certificate', //需处理
                    'business_certificate_expires' => 'business_certificate_expires',
                    'auth_letter' => 'auth_letter', //需处理
                );
                $orgin = array(
                    'alipay_store_id' => $alipay_store_id,
                    'category_id' => $category_id,
                    'brand' => $brand,
                    'brand_logo' => $logo,
                    'name' => $name,
                    'branch_name' => $branch_name,
                    'address_code' => $address,
                    'lng' => $lng,
                    'lat' => $lat,
                    'telephone' => $telephone,
                    'phone_num' => $phone_num,
                    'first_img' => $first_img,
                    'image' => $image,
                    'open_time' => $open_time,
                    'per_capita' => $per_capita,
                    'business_license' => $business_license,
                    'licence_code' => $licence_code,
                    'licence_name' => $licence_name,
                    'business_certificate' => $certificate,
                    'business_certificate_expires' => $certificate_expires,
                    'auth_letter' => $auth_letter
                );
                //门店是否已同步创建到口碑后台，已创建：则只提交有改动的字段；未创建：则提交所有的字段
                //门店审核状态，无审核：未创建；非无审核：已创建
                if ($model['alipay_sync_verify_status'] != STORE_ALIPAY_SYNC_STATUS_NONE) {
                    foreach ($orgin as $k => $v) {
                        if ($orgin[$k] == $old[$k]) {
                            unset($list[$k]);
                        }
                    }
                }

                //提交口碑的请求字段名
                $commit = array();
                foreach ($list as $key => $val) {
                    //需要修改的字段
                    $commit[$val] = $orgin[$key];
                }

                $ret = $this->syncStore($model['alipay_store_id'], $commit);
                if ($ret['status'] != ERROR_NONE) {
                    throw new Exception($ret['errMsg']);
                }
            }

            $transaction->commit();
            $result['status'] = ERROR_NONE;

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
            $transaction->rollback();
        }

        return json_encode($result);
    }

    /**
     * 删除门店
     * @param $id        门店id
     * @return array    返回数组
     */
    public function deleteStore($id)
    {
        $result = array();
        $transaction = Yii::app()->db->beginTransaction(); //开启事务
        try {
            //参数验证
            //TODO
            $model = Store::model()->findByPk($id);
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('删除的数据不存在');
            }
            //修改删除标识
            $model['flag'] = FLAG_YES;

            //将门店下的所有操作员锁定
            $operator = Operator::model()->findAll('store_id = :store_id and flag = :flag',
                array(':store_id' => $id, ':flag' => FLAG_NO));
            foreach ($operator as $k => $v) {
                //修改状态
                $v['status'] = OPERATOR_STATUS_LOCK;
                if (!$v->save()) {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('门店删除失败，错误码：STATUS');
                }
            }

            if (!$model->save()) {
                $result['status'] = ERROR_SAVE_FAIL; //状态码
                throw new Exception('门店删除失败，错误码：FLAG');
            }

            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            $result['data'] = '';
            $transaction->commit(); //数据提交
        } catch (Exception $e) {
            $transaction->rollback(); //数据回滚
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 门店pos机编号验证
     * @param unknown $store_id
     * @param unknown $pos_no
     * @throws Exception
     * @return string
     */
    public function verifyPosNo($store_id, $pos_no)
    {
        $result = array();
        try {
            //参数验证
            if (empty($store_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数store_id不能为空');
            }
            if (empty($pos_no)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数pos_no不能为空');
            }
            $model = Store::model()->find('id = :id and pos_no like :pos_no', array(':id' => $store_id, ':pos_no' => '%,' . $pos_no . ',%'));
            if (empty($model)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('数据不存在');
            }

            $result['data'] = array('name' => $model['name']);

            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
            //$result['data'] = '';
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取属性
     * @return array
     */
    function getAttributes()
    {
        return Store::model()->attributes;
    }


    /*
	 * 修改打印机名称
	 * $printname 打印机名称 必填
	 * $store_id 门店id 必填
	 * */
    public function editPrintName($printname, $store_id)
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($store_id);
            if ($store) {
                $store->print_name = $printname;
                if ($store->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据保存失败');
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('数据不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
	 * 开启或关闭打印功能
	 * $is_print 是否开启打印机 必填
	 * $store_id 门店id 必填
	 * */
    public function setPrint($is_print, $store_id, $state)
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($store_id);
            if ($store) {
                $store->is_print = $is_print;
                if ($store->update()) {
                    $result['status'] = ERROR_NONE;
                    $result['data'] = $store->is_print;
                    $result['state'] = $state;
                } else {
                    $result['status'] = ERROR_SAVE_FAIL;
                    throw new Exception('数据保存失败');
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('数据不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 商户信息验证
     * @param unknown $merchant_id
     * @param unknown $info
     * @return string
     */
    public function storeInfoValidate($merchant_id, $key, $value)
    {
        $msg = '';

        if ($key == 'name') {
            $model = Store::model()->find('merchant_id = :merchant_id and name = :name', array(
                ':merchant_id' => $merchant_id,
                ':name' => $value
            ));
            if (!empty($model)) {
                $msg = '门店名称重复';
            }
        }

        if ($key == 'number') {
            $model = Store::model()->find('merchant_id = :merchant_id and number = :number', array(
                ':merchant_id' => $merchant_id,
                ':number' => $value
            ));
            if (!empty($model)) {
                $msg = '门店编号重复';
            }
        }

        return $msg;
    }

    /**
     * 门店名称是否存在
     * @param unknown $merchant_id
     * @param unknown $main_name
     * @param unknown $branch_name
     * @return string
     */
    public function validateStoreName($merchant_id, $main_name, $branch_name)
    {
        $msg = '';

        $model = Store::model()->find('merchant_id = :merchant_id and name = :name and branch_name = :branch_name and flag = :flag',
            array(':merchant_id' => $merchant_id, ':name' => $main_name, ':branch_name' => $branch_name,':flag' => FLAG_NO));
        if (!empty($model)) {
            $msg = '主门店名+分店名已经存在';
        }
        return $msg;
    }

    /**
     * 验证门店编号
     */
    public function checkStoreNumber($merchant_id, $number)
    {
        $result = array();
        $data = array();
        try {
            //参数验证
            if (empty($merchant_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数merchant_id不能为空');
            }
            $criteria = new CDbCriteria();

            $criteria->addCondition('status = :status');
            $criteria->params[':status'] = ADMIN_STATUS_NORMAL;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            $model = Operator::model()->findAll($criteria);
            if (!empty($model)) {
                foreach ($model as $key => $value) {
                    $data[$value['id']] = $value['name'] . '(' . $value['number'] . ')';
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE;
                $result['errMsg'] = ''; //错误信息
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '数据为空'; //错误信息
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 导出交易明细excel
     */
    public function ExportExcel($list, $flag)
    {
        include 'PHPExcel/Reader/Excel2007.php';
        include 'PHPExcel/Reader/Excel5.php';
        include 'PHPExcel/IOFactory.php';
        $objPHPExcel = new PHPExcel();

        $min_time = null;//最小时间
        $max_time = null;
        $min_year = null;
        $min_month = null;
        $max_year = null;
        $max_month = null;
        $current_year = null;
        $current_month = null;
        $month_count = 0;
        if (!empty($list)) {
            $min_time = $list[count($list) - 1]['create_time'];
            $max_time = $list[0]['create_time'];
            $min_year = date('Y', strtotime($min_time));
            $min_month = date('m', strtotime($min_time));
            $max_year = date('Y', strtotime($max_time));
            $max_month = date('m', strtotime($max_time));
            $current_year = date('Y', strtotime($max_time));
            $current_month = date('m', strtotime($max_time));
            if ($max_year != $min_year)
                $month_count = ($max_year - $min_year + 1) * 12 + $max_year + (12 - $min_month + 1);//总共有多少个月
            else
                $month_count = $max_month - $min_month + 1;


            $status = "";
            foreach ($list as $n => $val) {
                if ($val['pay_status'] == ORDER_STATUS_PAID && $val['order_status'] == ORDER_STATUS_NORMAL) {
                    $list[$n]['status'] = '已付款';
                }
                if ($val['pay_status'] == ORDER_STATUS_UNPAID) {
                    $list[$n]['status'] = '待付款';
                }
                if ($val['order_status'] == ORDER_STATUS_REFUND) {
                    $list[$n]['status'] = '已退款';
                }
                if ($val['order_status'] == ORDER_STATUS_PART_REFUND) {
                    $list[$n]['status'] = '已部分退款';
                }
                if ($val['order_status'] == ORDER_STATUS_REVOKE) {
                    $list[$n]['status'] = '已撤销';
                }
            }
        }
        if ($flag == "true") {
            //有搜索条件,取所有搜索的交易明细，按创建时间月份排
            $num = 0;
            $objPHPExcel->setActiveSheetIndex($num)
                ->setCellValue('A1', '订单号')
                ->setCellValue('B1', '支付宝账号')
                ->setCellValue('C1', '订单金额')
                ->setCellValue('D1', '状态')
                ->setCellValue('E1', '交易类型')
                ->setCellValue('F1', '操作员')
                ->setCellValue('G1', '交易时间');
            //设置列宽
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet->getColumnDimension('A')->setWidth(20);
            $objActSheet->getColumnDimension('B')->setWidth(20);
            $objActSheet->getColumnDimension('C')->setWidth(20);
            $objActSheet->getColumnDimension('D')->setWidth(20);
            $objActSheet->getColumnDimension('E')->setWidth(20);
            $objActSheet->getColumnDimension('F')->setWidth(20);
            $objActSheet->getColumnDimension('G')->setWidth(30);
            //设置sheet名称
            $objActSheet->setTitle($current_year . '年' . $current_month . '月');
            if (!empty($list)) {
                $i = 2;
                foreach ($list as $k => $v) {
                    if ($v['pay_status'] == ORDER_STATUS_PAID && $v['order_status'] == ORDER_STATUS_NORMAL) {
                        $status = '已付款';
                    }
                    if ($v['pay_status'] == ORDER_STATUS_UNPAID) {
                        $status = '待付款';
                    }
                    if ($v['order_status'] == ORDER_STATUS_REFUND) {
                        $status = '已退款';
                    }
                    if ($v['order_status'] == ORDER_STATUS_PART_REFUND) {
                        $status = '已部分退款';
                    }
                    if ($v['order_status'] == ORDER_STATUS_REVOKE) {
                        $status = '已撤销';
                    }
                    if ($current_year == date('Y', strtotime($v['create_time'])) && $current_month == date('m', strtotime($v['create_time']))) {
                        //数据添加
                        $objPHPExcel->setActiveSheetIndex($num)
                            ->setCellValueExplicit('A' . $i, $v['order_no'])
                            ->setCellValue('B' . $i, $v['alipay_account'])
                            ->setCellValue('C' . $i, $v['paymoney'])
                            ->setCellValue('D' . $i, $status)
                            ->setCellValue('E' . $i, $GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']])
                            ->setCellValue('F' . $i, $v['operator_name'])
                            ->setCellValue('G' . $i, $v['pay_time']);
                        $i++;
                    } else {
                        //年月不相等
                        $objPHPExcel->createSheet();
                        $current_year = date('Y', strtotime($v['create_time']));
                        $current_month = date('m', strtotime($v['create_time']));
                        $num++;
                        $objPHPExcel->setActiveSheetIndex($num)
                            ->setCellValue('A1', '订单号')
                            ->setCellValue('B1', '支付宝账号')
                            ->setCellValue('C1', '订单金额')
                            ->setCellValue('D1', '状态')
                            ->setCellValue('E1', '交易类型')
                            ->setCellValue('F1', '操作员')
                            ->setCellValue('G1', '交易时间');
                        //设置列宽
                        $objActSheet = $objPHPExcel->getActiveSheet();
                        $objActSheet->getColumnDimension('A')->setWidth(20);
                        $objActSheet->getColumnDimension('B')->setWidth(20);
                        $objActSheet->getColumnDimension('C')->setWidth(20);
                        $objActSheet->getColumnDimension('D')->setWidth(20);
                        $objActSheet->getColumnDimension('E')->setWidth(20);
                        $objActSheet->getColumnDimension('F')->setWidth(20);
                        $objActSheet->getColumnDimension('G')->setWidth(30);
                        //设置sheet名称
                        $objActSheet->setTitle($current_year . '年' . $current_month . '月');
                        $i = 2;
                        //数据添加
                        $objPHPExcel->setActiveSheetIndex($num)
                            ->setCellValueExplicit('A' . $i, $v['order_no'])
                            ->setCellValue('B' . $i, $v['alipay_account'])
                            ->setCellValue('C' . $i, $v['paymoney'])
                            ->setCellValue('D' . $i, $status)
                            ->setCellValue('E' . $i, $GLOBALS['ORDER_PAY_CHANNEL'][$v['pay_channel']])
                            ->setCellValue('F' . $i, $v['operator_name'])
                            ->setCellValue('G' . $i, $v['pay_time']);
                        $i++;
                    }
                }
            }
        } else {
            //无搜索条件取三个月
            $num = 0;
            $objPHPExcel->setActiveSheetIndex($num)
                ->setCellValue('A1', '订单号')
                ->setCellValue('B1', '支付宝账号')
                ->setCellValue('C1', '订单金额')
                ->setCellValue('D1', '状态')
                ->setCellValue('E1', '交易类型')
                ->setCellValue('F1', '操作员')
                ->setCellValue('G1', '交易时间');
            //设置列宽
            $objActSheet = $objPHPExcel->getActiveSheet();
            $objActSheet->getColumnDimension('A')->setWidth(20);
            $objActSheet->getColumnDimension('B')->setWidth(20);
            $objActSheet->getColumnDimension('C')->setWidth(20);
            $objActSheet->getColumnDimension('D')->setWidth(20);
            $objActSheet->getColumnDimension('E')->setWidth(20);
            $objActSheet->getColumnDimension('F')->setWidth(20);
            $objActSheet->getColumnDimension('G')->setWidth(30);
            //设置sheet名称
            $objActSheet->setTitle($current_year . '年' . $current_month . '月');
            if (!empty($list)) {
                $i = 2;
                foreach ($list as $key => $value) {
                    if ($value['pay_status'] == ORDER_STATUS_PAID && $value['order_status'] == ORDER_STATUS_NORMAL) {
                        $status = '已付款';
                    }
                    if ($value['pay_status'] == ORDER_STATUS_UNPAID) {
                        $status = '待付款';
                    }
                    if ($value['order_status'] == ORDER_STATUS_REFUND) {
                        $status = '已退款';
                    }
                    if ($value['order_status'] == ORDER_STATUS_PART_REFUND) {
                        $status = '已部分退款';
                    }
                    if ($value['order_status'] == ORDER_STATUS_REVOKE) {
                        $status = '已撤销';
                    }

                    if ($current_year == date('Y', strtotime($value['create_time'])) && $current_month == date('m', strtotime($value['create_time']))) {
                        //数据添加
                        $objPHPExcel->setActiveSheetIndex($num)
                            ->setCellValueExplicit('A' . $i, $value['order_no'])
                            ->setCellValue('B' . $i, $value['alipay_account'])
                            ->setCellValue('C' . $i, $value['paymoney'])
                            ->setCellValue('D' . $i, $status)
                            ->setCellValue('E' . $i, $GLOBALS['ORDER_PAY_CHANNEL'][$value['pay_channel']])
                            ->setCellValue('F' . $i, $value['operator_name'])
                            ->setCellValue('G' . $i, $value['pay_time']);
                        $i++;
                    } else {
                        //年月不相等
                        $current_year = date('Y', strtotime($value['create_time']));
                        $current_month = date('m', strtotime($value['create_time']));
                        $num++;
                        if ($num == 3) {
                            $filename = '交易明细表' . date('YmdHis');//定义文件名

                            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                            $this->outPut($filename);
                            $objWriter->save("php://output");
                            return;
                        }
                        $objPHPExcel->createSheet();
                        $objPHPExcel->setActiveSheetIndex($num)
                            ->setCellValue('A1', '订单号')
                            ->setCellValue('B1', '支付宝账号')
                            ->setCellValue('C1', '订单金额')
                            ->setCellValue('D1', '状态')
                            ->setCellValue('E1', '交易类型')
                            ->setCellValue('F1', '操作员')
                            ->setCellValue('G1', '交易时间');
                        //设置列宽
                        $objActSheet = $objPHPExcel->getActiveSheet();
                        $objActSheet->getColumnDimension('A')->setWidth(20);
                        $objActSheet->getColumnDimension('B')->setWidth(20);
                        $objActSheet->getColumnDimension('C')->setWidth(20);
                        $objActSheet->getColumnDimension('D')->setWidth(20);
                        $objActSheet->getColumnDimension('E')->setWidth(20);
                        $objActSheet->getColumnDimension('F')->setWidth(20);
                        $objActSheet->getColumnDimension('G')->setWidth(30);
                        //设置sheet名称
                        $objActSheet->setTitle($current_year . '年' . $current_month . '月');
                        $i = 2;
                        //数据添加
                        $objPHPExcel->setActiveSheetIndex($num)
                            ->setCellValueExplicit('A' . $i, $value['order_no'])
                            ->setCellValue('B' . $i, $value['alipay_account'])
                            ->setCellValue('C' . $i, $value['paymoney'])
                            ->setCellValue('D' . $i, $status)
                            ->setCellValue('E' . $i, $GLOBALS['ORDER_PAY_CHANNEL'][$value['pay_channel']])
                            ->setCellValue('F' . $i, $value['operator_name'])
                            ->setCellValue('G' . $i, $value['pay_time']);
                        $i++;
                    }

                }
            }
        }

        $filename = '交易明细表' . date('YmdHis');//定义文件名

        $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
        $this->outPut($filename);
        $objWriter->save("php://output");
    }

    /**
     * 到浏览器  浏览器下载excel
     */
    public function outPut($filename)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate,post-check=0,pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename={$filename}.xls");
        header("Content-Transfer-Encoding:binary");
    }

    /*
     * 获取门店的收款账号信息（支付宝）
     * */
    public function getStoreAlipay($id)
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($id);
            if (!empty($store)) {
                $data = array();
                $data['id'] = $store->id;
                $data['if_alipay_open'] = $store->if_alipay_open;
                $data['alipay_use_pro'] = $store->alipay_use_pro;
                $data['alipay_api_version'] = $store->alipay_api_version;
                $data['alipay_pid'] = $store->alipay_pid;
                $data['alipay_key'] = $store->alipay_key;
                $data['alipay_appid'] = $store->alipay_appid;

//     			$management = Management::model() -> findByPk($store -> management_id);
                $merchant = Merchant::model()->findByPk($store->merchant_id);
                $data['encrypt_id'] = $merchant->encrypt_id;

                $result['data'] = $data;
                $result['status'] = ERROR_NONE;

            } else {
                throw new Exception('该门店不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
     * 获取门店的收款账号信息（微信）
    * */
    public function getStoreWechatPay($id)
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($id);
            if (!empty($store)) {
                $data = array();
                $data['id'] = $store->id;
                $data['if_wx_open'] = $store->if_wx_open;
                $data['wx_use_pro'] = $store->wx_use_pro;
                $data['wx_merchant_type'] = $store->wx_merchant_type;
                $data['wx_apiclient_cert'] = $store->wx_apiclient_cert;
                $data['wx_apiclient_key'] = $store->wx_apiclient_key;
                $data['wx_appid'] = $store->wx_appid;
                $data['wx_appsecret'] = $store->wx_appsecret;
                $data['wx_api'] = $store->wx_api;
                $data['wx_mchid'] = $store->wx_mchid;
                $data['t_wx_appid'] = $store->t_wx_appid;
                $data['t_wx_mchid'] = $store->t_wx_mchid;

                $result['data'] = $data;
                $result['status'] = ERROR_NONE;

            } else {
                throw new Exception('该门店不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 设置门店收款账号是否使用上级收款账号
     * $id 门店id
     * $state 状态
     * */
    public function setStorePayUse($id, $state, $type)
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($id);
            if (!empty($store)) {
                if ($state == 'true') {
                    //开启
                    if ($type == 'alipay') {
                        $store->alipay_use_pro = IF_ALIPAY_OPEN_OPEN;
                    } elseif ($type == 'wechat') {
                        $store->wx_use_pro = IF_ALIPAY_OPEN_OPEN;
                    }
                } else if ($state == 'false') {
                    //关闭
                    if ($type == 'alipay') {
                        $store->alipay_use_pro = IF_ALIPAY_OPEN_CLOSE;
                    } elseif ($type == 'wechat') {
                        $store->wx_use_pro = IF_ALIPAY_OPEN_CLOSE;
                    }
                }
                if ($store->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    throw new Exception('数据保存失败');
                }
            } else {
                throw new Exception('该门店不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /**
     * 设置门店收款账号是否启用
     * $id 门店id
     * $state 状态
     * */
    public function setStorePayOpen($id, $state, $type)
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($id);
            if (!empty($store)) {
                if ($state == 'true') {
                    //开启
                    if ($type == 'alipay') {
                        $store->if_alipay_open = IF_ALIPAY_OPEN_OPEN;
                    } elseif ($type == 'wechat') {
                        $store->if_wx_open = IF_ALIPAY_OPEN_OPEN;
                    }
                } else if ($state == 'false') {
                    //关闭
                    if ($type == 'alipay') {
                        $store->if_alipay_open = IF_ALIPAY_OPEN_CLOSE;
                    } elseif ($type == 'wechat') {
                        $store->if_wx_open = IF_ALIPAY_OPEN_CLOSE;
                    }
                }
                if ($store->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    throw new Exception('数据保存失败');
                }
            } else {
                throw new Exception('该门店不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /*
     * 设置门店支付宝收款账号
     *
     * */
    public function setStoreAlipay($id, $alipay_api_version, $alipay_pid = '', $alipay_key = '', $alipay_appid = '')
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($id);
            if (!empty($store)) {
                $store->alipay_api_version = $alipay_api_version;
                if ($alipay_api_version == ALIPAY_API_VERSION_1_API) {
                    $store->alipay_pid = $alipay_pid;
                    $store->alipay_key = $alipay_key;
                } elseif ($alipay_api_version == ALIPAY_API_VERSION_2_API) {
                    $store->alipay_appid = $alipay_appid;
                }
                if ($store->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    throw new Exception('数据保存失败');
                }
            } else {
                throw new Exception('该门店不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
     * 设置门店微信收款账号
    *
    * */
    public function setStoreWechatPay($id, $wx_merchant_type, $appid = '', $appsecret = '', $mchid = '', $api_key = '', $t_appid = '', $t_mchid = '')
    {
        $result = array();
        try {
            $store = Store::model()->findByPk($id);
            if (!empty($store)) {
                $store->wx_merchant_type = $wx_merchant_type;
                if ($wx_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                    $store->wx_appid = $appid;
                    $store->wx_appsecret = $appsecret;
                    $store->wx_mchid = $mchid;
                    $store->wx_api = $api_key;
                } elseif ($wx_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
                    $store->t_wx_appid = $t_appid;
                    $store->t_wx_mchid = $t_mchid;
                }
                if ($store->update()) {
                    $result['status'] = ERROR_NONE;
                } else {
                    throw new Exception('数据保存失败');
                }
            } else {
                throw new Exception('该门店不存在');
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //保存证书路径
    public function updateWechatCert($id, $dir_name, $type)
    {
        $result = array('status' => 'null', 'errMsg' => 'null', 'data' => 'null');
        try {
            $store = Store::model()->find('id=:id and flag=:flag', array(
                ':id' => $id,
                ':flag' => FLAG_NO
            ));
            if (!empty($store)) {
                if (!empty($id) && !empty($dir_name)) {
                    if ($type == 'apiclient_cert.pem')
                        $store->wx_apiclient_cert = $dir_name;
                    else if ($type == 'apiclient_key.pem')
                        $store->wx_apiclient_key = $dir_name;
                    if ($store->update()) {
                        $result['status'] = ERROR_NONE;
                    } else {
                        $result['status'] = ERROR_SAVE_FAIL;
                        throw new Exception('上传失败');
                    }
                }
            } else {
                $result['status'] = ERROR_NO_DATA;
                $result['errMsg'] = '无此数据';
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /**
     * 同步门店
     * @param unknown $store_id
     * @param unknown $params
     * @throws Exception
     * @return array
     */
    public function syncStore($store_id, $params)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('alipay_store_id = :alipay_store_id');
            $criteria->params[':alipay_store_id'] = $store_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Store::model()->find($criteria);
            if (empty($model)) {
                throw new Exception('数据不存在');
            }
            //查询商户信息
            $merchant = Merchant::model()->findByPk($model['merchant_id']);
            if (empty($merchant)) {
                throw new Exception('数据不存在');
            }
            $token = $merchant['alipay_auth_token']; //授权令牌
            $shop_id = $model['koubei_store_id']; //支付宝门店id

            if (empty($token)) {
                throw new Exception('商户未授权');
            }

            //请求参数格式或内容处理
            foreach ($params as $k => $v) {
                if ($k == 'brand_logo') {
                    //上传图片
                    $ret = $this->uploadToAlipay($store_id, 'brand_logo');
                    if ($ret['status'] == ERROR_NONE) {
                        $params[$k] = $ret['data'];
                        $model['brand_logo_id'] = $ret['data'];
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
                if ($k == 'main_image') {
                    //上传图片
                    $ret = $this->uploadToAlipay($store_id, 'first_img');
                    if ($ret['status'] == ERROR_NONE) {
                        $params[$k] = $ret['data'];
                        $model['first_img_id'] = $ret['data'];
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
                if ($k == 'audit_images') {
                    //上传图片
                    $ret = $this->uploadToAlipay($store_id, 'image');
                    if ($ret['status'] == ERROR_NONE) {
                        $params[$k] = $ret['data'];
                        $model['image_id'] = $ret['data'];
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
                if ($k == 'licence') {
                    //上传图片
                    $ret = $this->uploadToAlipay($store_id, 'business_license');
                    if ($ret['status'] == ERROR_NONE) {
                        $params[$k] = $ret['data'];
                        $model['business_license_id'] = $ret['data'];
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
                if ($k == 'business_certificate') {
                    //上传图片
                    $ret = $this->uploadToAlipay($store_id, 'business_certificate');
                    if ($ret['status'] == ERROR_NONE) {
                        $params[$k] = $ret['data'];
                        $model['business_certificate_id'] = $ret['data'];
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
                if ($k == 'auth_letter') {
                    //上传图片
                    $ret = $this->uploadToAlipay($store_id, 'auth_letter');
                    if ($ret['status'] == ERROR_NONE) {
                        $params[$k] = $ret['data'];
                        $model['auth_letter_id'] = $ret['data'];
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
                if ($k == 'province_code,city_code,district_code,address') {
                    //转化为省市区编码
                    $address = explode(',', $v);
                    $params['province_code'] = $address[0];
                    $params['city_code'] = $address[1];
                    $params['district_code'] = $address[2];
                    $params['address'] = $address[3];
                }
                if ($k == 'longitude' || $k == 'latitude') {
                    $bd_lon = $model['lng'];
                    $bd_lat = $model['lat'];
                    //转化为高德坐标系
                    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
                    $x = $bd_lon - 0.0065;
                    $y = $bd_lat - 0.006;
                    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
                    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
                    $params['longitude'] = $z * cos($theta);
                    $params['latitude'] = $z * sin($theta);
                }
                if ($k == 'business_time') {
                    $tmp = explode(',', $v);
                    $params[$k] = $tmp[1];
                }
            }

            $api = new AlipayApi();
            $sync_status = $model['alipay_sync_verify_status']; //口碑审核状态
            if ($sync_status == STORE_ALIPAY_SYNC_STATUS_NONE) { //未审核状态，门店信息未在口碑创建
                $params['operate_notify_url'] = ALIPAY_STORE_AUDIT_REDIRECT_URI . '?req_id=' . $store_id; //设置回跳地址
                $response = $api->shopCreateApi($params, NULL, $token);
            } else { //有审核状态，门店信息已在口碑创建
                $response = $api->shopModifyApi($shop_id, $params, NULL, $token);
            }
            if (!$response) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('请求接口失败');
            }
            //返回请求结果
            $result_code = $api->getVal($response, 'result_code'); //结果码
            $shop_id = $api->getVal($response, 'shop_id'); //支付宝门店ID
            $audit_status = $api->getVal($response, 'audit_status'); //门店审核状态
            $is_online = $api->getVal($response, 'is_online'); //是否上架
            $detail_error_des = $api->getVal($response, 'detail_error_des'); //错误描述
            $error = $api->getVal($response, 'error'); //其他错误

            //请求成功
            if ($result_code == ALIPAY_V2_CODE_SUCCESS) {
                //根据返回的口碑审核状态更新门店的审核状态
                if ($audit_status == ALIPAY_STORE_STATUS_AUDITING) {
                    $model['alipay_sync_verify_status'] = STORE_ALIPAY_SYNC_STATUS_AUDITING;
                }
                if ($audit_status == ALIPAY_STORE_STATUS_AUDIT_SUCCESS) {
                    $model['alipay_sync_verify_status'] = STORE_ALIPAY_SYNC_STATUS_PASS;
                }
                if ($audit_status == ALIPAY_STORE_STATUS_AUTO_PASS) {
                    //无需变动
                }
                //支付宝如果碰到内部的系统错误等异常，会返回该错误码，重试可能能够解决问题，也不会重新进入审核流， 不会 有notify消息通知到开发者网关
                if ($audit_status == ALIPAY_STORE_STATUS_AUTO_FAIL) {
                    throw new Exception('内部系统错误');
                }
                if ($audit_status == ALIPAY_STORE_STATUS_AUDIT_FAILED) {
                    $model['alipay_sync_verify_status'] = STORE_ALIPAY_SYNC_STATUS_REJECT;
                }
                if (!empty($shop_id)) {
                    $model['koubei_store_id'] = $shop_id;
                    $model['alipay_sync_time'] = date('Y-m-d H:i:s');
                }
            }
            //请求失败
            if ($result_code == ALIPAY_V2_CODE_FAIL || $result_code == ALIPAY_V2_CODE_UNKNOWN) {
                $result['status'] = ERROR_REQUEST_FAIL;
                throw new Exception($detail_error_des);
            }
            //其他接口错误
            if (!empty($error)) {
                $result['status'] = ERROR_REQUEST_FAIL;
                throw new Exception($error);
            }

            if (!$model->save()) {
                throw new Exception('数据保存失败');
            }

            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 上传图片
     * @param unknown $store_id
     * @param unknown $img_column
     * @throws Exception
     * @return multitype:string NULL
     */
    private function uploadToAlipay($store_id, $img_column)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('alipay_store_id = :alipay_store_id');
            $criteria->params[':alipay_store_id'] = $store_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Store::model()->find($criteria);
            if (empty($model)) {
                throw new Exception('数据不存在');
            }

            //查询商户的信息
            $merchant = Merchant::model()->findByPk($model['merchant_id']);
            if (empty($merchant)) {
                throw new Exception('数据不存在');
            }
            $token = $merchant['alipay_auth_token'];

            //待保存数据
            $data = '';
            //返回数据
            $shop_id = '';

            //获取对应字段数据
            $image = $model[$img_column];
            if (!empty($image)) {
                //是否多图
                if (strstr($image, ",")) {
                    //上传多图
                    $arr = explode(',', $image);
                    foreach ($arr as $k => $v) {
                        $phy = $v; //物理地址
                        $ret = $this->uploadImage($v, $token);
                        if ($ret['status'] == ERROR_NONE) {
                            //$data .= $ret['image_id'].':'.$phy.',';
                            $shop_id .= $ret['image_id'] . ',';
                        } else {
                            throw new Exception($ret['errMsg']);
                        }
                    }
                } else {
                    //上传单图
                    $ret = $this->uploadImage($image, $token);
                    if ($ret['status'] == ERROR_NONE) {
                        //$data = $ret['image_id'].':'.$image.',';
                        $shop_id = $ret['image_id'] . ',';
                    } else {
                        throw new Exception($ret['errMsg']);
                    }
                }
//     			$data = trim($data, ',');
                $shop_id = trim($shop_id, ',');

                /*
    			//保存数据
    			$img_id = $img_column.'_id';
    			$model[$img_id] = $shop_id;
    			if (!$model->save()) {
    				throw new Exception('数据保存失败');
    			}
    			*/
            }

            $result['status'] = ERROR_NONE;
            $result['data'] = $shop_id;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 上传门店图片
     * @param unknown $image_path
     * @return multitype:string Ambigous <Ambigous, string, unknown, mixed, unknown>
     */
    private function uploadImage($image_path, $token)
    {
        $result = array();
        try {
            $file = end(explode('/', $image_path)); //文件名+文件类型
            $info = explode('.', $file);
            $name = $info[0]; //名字
            $type = $info[1]; //类型
            $path = '@' . UPLOAD_SYSTEM_PATH . 'images/gj/source/' . $image_path; //物理路径

            $api = new AlipayApi();
            $response = $api->uploadImageApi($name, $type, $path, NULL, $token);
            //返回请求结果
            $result_code = $api->getVal($response, 'result_code'); //结果码
            $detail_error_des = $api->getVal($response, 'detail_error_des'); //错误描述
            $error = $api->getVal($response, 'error'); //其他错误
            $image_id = $api->getVal($response, 'image_id'); //图片id

            if ($result_code == ALIPAY_V2_CODE_SUCCESS) {
                $result['status'] = ERROR_NONE;
                $result['image_id'] = $image_id;
            } else {
                $result['status'] = ERROR_EXCEPTION;
                $result['errMsg'] = $detail_error_des;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 门店审核状态更新
     * @param unknown $store_id
     * @param unknown $status
     * @param unknown $desc
     * @throws Exception
     * @return multitype:string NULL
     */
    public function updateAuditStatus($store_id, $status, $desc)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('alipay_store_id = :alipay_store_id');
            $criteria->params[':alipay_store_id'] = $store_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Store::model()->find($criteria);
            if (empty($model)) {
                throw new Exception('数据不存在');
            }

            if ($status == ALIPAY_STORE_STATUS_AUDIT_FAILED) { //审核失败
                $model['alipay_sync_verify_status'] = STORE_ALIPAY_SYNC_STATUS_REJECT;
                $model['alipay_sync_time'] = date('Y-m-d H:i:s');
                $model['audit_desc'] = $desc;
            }
            if ($status == ALIPAY_STORE_STATUS_AUDIT_SUCCESS) {
                $model['alipay_sync_verify_status'] = STORE_ALIPAY_SYNC_STATUS_PASS;
                $model['alipay_sync_time'] = date('Y-m-d H:i:s');
            }

            //保存数据
            if (!$model->save()) {
                throw new Exception('数据保存失败');
            }

            $result['status'] = ERROR_NONE;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return $result;
    }

    /**
     * 获取支付宝口碑门店信息
     * @param unknown $store_id
     * @throws Exception
     * @return multitype:string NULL
     */
    public function getAlipayStoreDetail($store_id)
    {
        $result = array();
        try {
            $criteria = new CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->params[':id'] = $store_id;
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;

            $model = Store::model()->find($criteria);
            if (empty($model)) {
                throw new Exception('数据不存在');
            }

            $shop_id = $model['koubei_store_id'];
            if (empty($shop_id)) {
                throw new Exception('门店未同步到口碑');
            }

            //查询授权令牌
            $merchant = Merchant::model()->findByPk($model['merchant_id']);
            $auth_token = $merchant['alipay_auth_token'];

            $api = new AlipayApi();
            $response = $api->queryShopDetailApi($shop_id, NULL, $auth_token);
            //返回请求结果
            $result_code = $api->getVal($response, 'result_code'); //结果码
            $detail_error_des = $api->getVal($response, 'detail_error_des'); //错误描述
            $error = $api->getVal($response, 'error'); //其他错误
            $processed_qr_code = $api->getVal($response, 'processed_qr_code'); //整合二维码图片
            $qr_code = $api->getVal($response, 'qr_code'); //二维码图片
            //其他返回信息按需添加
            //TODO

            if ($result_code == ALIPAY_V2_CODE_SUCCESS) {
                $result['status'] = ERROR_NONE;
                $result['qrcode'] = $processed_qr_code;
            } else {
                $result['status'] = ERROR_EXCEPTION;
                $result['errMsg'] = $detail_error_des;
            }
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取支付宝支付收款信息
     * @param unknown $store_id
     * @throws Exception
     * @return string
     */
    public function getAlipaySellerInfo($store_id)
    {
        $result = array();
        try {
            //参数验证
            if (empty($store_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数store_id不能为空');
            }

            $store = Store::model()->findByPk($store_id);
            if (empty($store)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('门店不存在');
            }

            $alipay_api_version = ''; //支付宝接口版本
            $alipay_pid = ''; //支付宝合作者id
            $alipay_key = ''; //支付宝安全检验码
            $alipay_appid = ''; //支付宝appid
            $alipay_store_id = ''; //支付宝门店id
            $alipay_seller_id = ''; //支付宝卖家账号id
            $alipay_auth_token = ''; //支付宝授权令牌

            $open = $store['if_alipay_open'];
            $alipay_store_id = $store['alipay_store_id'];
            $alipay_seller_id = $store['alipay_seller_id'];
            $alipay_audit_status = $store['alipay_sync_verify_status'];
            $category_id = $store['category_id'];
            if ($open == IF_ALIPAY_OPEN_YES) { //使用门店自身收款账号收款
                $alipay_api_version = $store['alipay_api_version'];
                $alipay_pid = $store['alipay_pid'];
                $alipay_key = $store['alipay_key'];
                $alipay_appid = $store['alipay_appid'];
                $alipay_seller_id = ''; //使用自身收款账号时，为空
            } else { //使用上级管理单元收款账号收款
                /*
    			$super = $store['alipay_use_pro']; //是否使用上级单元收款账号
    			if ($super == IF_USE_PRO_YES) {
    				//上级管理单元id
    				$manage_id = $store['management_id'];
    				do {
    					$management = Management::model()->findByPk($manage_id);
    					if (empty($management)) {
    						break;
    					}
    					$open = $management['if_alipay_open'];
    					if ($open == IF_ALIPAY_OPEN_NO) {
    						$manage_id = $management['p_mid'];
    						continue;
    					}
    				}while (!$open);

    				if ($management) {
    					$alipay_api_version = $management['alipay_api_version'];
    					$alipay_pid = $management['alipay_pid'];
    					$alipay_key = $management['alipay_key'];
    					$alipay_appid = $management['alipay_appid'];
    				}
    			}
    			*/
                $super = $store['alipay_use_pro']; //是否使用上级单元收款账号
                if ($super == IF_USE_PRO_YES) {
                    $merchant_id = $store['merchant_id'];
                    $merchant = Merchant::model()->findByPk($merchant_id);
                    if ($merchant) {
                        $alipay_api_version = $merchant['alipay_api_version'];
                        $alipay_pid = $merchant['partner'];
                        $alipay_key = $merchant['key'];
                        $alipay_appid = $merchant['appid'];
                        $alipay_auth_token = $merchant['alipay_auth_token'];
                    }
                }
            }

            if (empty($alipay_api_version)) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('错误的收款参数');
            }
            if ($alipay_api_version != ALIPAY_API_VERSION_1_API && $alipay_api_version != ALIPAY_API_VERSION_2_API && $alipay_api_version != ALIPAY_API_VERSION_2_AUTH_API) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('错误的收款参数');
            }
            if ($alipay_api_version == ALIPAY_API_VERSION_1_API) {
                if (empty($alipay_pid) || empty($alipay_key)) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('该门店无可用收款账号');
                }
                $alipay_appid = '';
                $alipay_seller_id = '';
                $alipay_auth_token = '';
            }
            if ($alipay_api_version == ALIPAY_API_VERSION_2_API) {
                if (empty($alipay_appid)) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('该门店无可用收款账号');
                }
                $alipay_pid = '';
                $alipay_key = '';
                $alipay_auth_token = '';
            }
            if ($alipay_api_version == ALIPAY_API_VERSION_2_AUTH_API) {
                if (empty($alipay_auth_token)) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('该门店无可用收款账号');
                }
                $alipay_pid = '';
                $alipay_key = '';
                $alipay_appid = '';
            }

            $data = array(
                'alipay_api_version' => $alipay_api_version,
                'alipay_pid' => $alipay_pid,
                'alipay_key' => $alipay_key,
                'alipay_appid' => $alipay_appid,
                'alipay_store_id' => $alipay_store_id,
                'alipay_seller_id' => $alipay_seller_id,
                'alipay_auth_token' => $alipay_auth_token,
                'alipay_audit_status' => $alipay_audit_status,
                'category_id' => $category_id,
            );

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

    /**
     * 获取微信支付收款信息
     * @param unknown $store_id
     * @throws Exception
     * @return string
     */
    public function getWxpaySellerInfo($store_id)
    {
        $result = array();
        try {
            //参数验证
            if (empty($store_id)) {
                $result['status'] = ERROR_PARAMETER_MISS;
                throw new Exception('参数store_id不能为空');
            }

            $store = Store::model()->findByPk($store_id);
            if (empty($store)) {
                $result['status'] = ERROR_NO_DATA;
                throw new Exception('门店不存在');
            }

            $wxpay_merchant_type = ''; //微信商户类型
            $wxpay_appid = ''; //微信appid
            $wxpay_appsecret = ''; //微信应用密钥
            $wxpay_api_key = ''; //微信API密钥
            $wxpay_mchid = ''; //微信商户号
            $wxpay_apiclient_cert = ''; //cert文件路径
            $wxpay_apiclient_key = ''; //key文件路径

            $open = $store['if_wx_open'];
            if ($open == IF_WXPAY_OPEN_YES) { //使用门店自身收款账号收款
                $wxpay_merchant_type = $store['wx_merchant_type'];
                if ($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                    $wxpay_appid = $store['wx_appid'];
                    $wxpay_appsecret = $store['wx_appsecret'];
                    $wxpay_api_key = $store['wx_api'];
                    $wxpay_mchid = $store['wx_mchid'];
                    $wxpay_apiclient_cert = UPLOAD_SYSTEM_PATH . 'cert/' . $store['wx_apiclient_cert'] . '/apiclient_cert.pem';
                    $wxpay_apiclient_key = UPLOAD_SYSTEM_PATH . 'cert/' . $store['wx_apiclient_key'] . '/apiclient_key.pem';
                } else {
                    $wxpay_appid = $store['t_wx_appid'];
                    $wxpay_mchid = $store['t_wx_mchid'];
                }
            } else { //使用上级管理单元收款账号收款
                /*
    			$super = $store['wx_use_pro']; //是否使用上级单元收款账号
    			if ($super == IF_USE_PRO_YES) {
    				//上级管理单元id
    				$manage_id = $store['management_id'];
    				do {
    					$management = Management::model()->findByPk($manage_id);
    					if (empty($management)) {
    						break;
    					}
    					$open = $management['if_wx_open'];
    					if ($open == IF_WXPAY_OPEN_NO) {
    						$manage_id = $management['p_mid'];
    						continue;
    					}
    				}while (!$open);

    				if ($management) {
    					$wxpay_merchant_type = $management['wx_merchant_type'];
    					if ($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
    						$wxpay_appid = $management['wx_appid'];
    						$wxpay_appsecret = $management['wx_appsecret'];
    						$wxpay_api_key = $management['wx_api'];
    						$wxpay_mchid = $management['wx_mchid'];
    						$wxpay_apiclient_cert = UPLOAD_SYSTEM_PATH.'cert/'.$management['wx_apiclient_cert'].'/apiclient_cert.pem';
    						$wxpay_apiclient_key = UPLOAD_SYSTEM_PATH.'cert/'.$management['wx_apiclient_key'].'/apiclient_key.pem';
    					}else {
    						$wxpay_appid = $management['t_wx_appid'];
    						$wxpay_mchid = $management['t_wx_mchid'];
    					}
    				}
    			}
    			*/
                $super = $store['wx_use_pro']; //是否使用上级单元收款账号
                if ($super == IF_USE_PRO_YES) {
                    $merchant_id = $store['merchant_id'];
                    $merchant = Merchant::model()->findByPk($merchant_id);
                    if ($merchant) {
                        $wxpay_merchant_type = $merchant['wxpay_merchant_type'];
                        if ($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                            $wxpay_appid = $merchant['wechat_appid'];
                            $wxpay_appsecret = $merchant['wechat_appsecret'];
                            $wxpay_api_key = $merchant['wechat_key'];
                            $wxpay_mchid = $merchant['wechat_mchid'];
                            $wxpay_apiclient_cert = UPLOAD_SYSTEM_PATH . 'cert/' . $merchant['wechat_apiclient_cert'] . '/apiclient_cert.pem';
                            $wxpay_apiclient_key = UPLOAD_SYSTEM_PATH . 'cert/' . $merchant['wechat_apiclient_key'] . '/apiclient_key.pem';
                        } else {
                            $wxpay_appid = $merchant['t_wx_appid'];
                            $wxpay_mchid = $merchant['t_wx_mchid'];
                        }
                    }
                }
            }

            if (empty($wxpay_merchant_type)) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('错误的收款参数');
            }
            if ($wxpay_merchant_type != WXPAY_MERCHANT_TYPE_SELF && $wxpay_merchant_type != WXPAY_MERCHANT_TYPE_AFFILIATE) {
                $result['status'] = ERROR_EXCEPTION;
                throw new Exception('错误的收款参数');
            }
            if ($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_SELF) {
                if (empty($wxpay_appid) || empty($wxpay_mchid) || empty($wxpay_api_key)) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('该门店无可用收款账号');
                }
            }
            if ($wxpay_merchant_type == WXPAY_MERCHANT_TYPE_AFFILIATE) {
                if (empty($wxpay_mchid)) {
                    $result['status'] = ERROR_EXCEPTION;
                    throw new Exception('该门店无可用收款账号');
                }
            }

            $data = array(
                'wxpay_merchant_type' => $wxpay_merchant_type,
                'wxpay_appid' => $wxpay_appid,
                'wxpay_appsecret' => $wxpay_appsecret,
                'wxpay_mchid' => $wxpay_mchid,
                'wxpay_api_key' => $wxpay_api_key,
                'wxpay_apiclient_cert' => $wxpay_apiclient_cert,
                'wxpay_apiclient_key' => $wxpay_apiclient_key,
            );

            $result['data'] = $data;
            $result['status'] = ERROR_NONE; //状态码
            $result['errMsg'] = ''; //错误信息
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }


    /*
     * 获取省市区信息
    * */
    public function getProvince()
    {
        $result = array();
        try {
            $p = ShopCity::model()->findAll('level =:level', array(
                ':level' => CITY_LEVEL_PROVINCE
            ));
            $data = array();
            foreach ($p as $k => $v) {
                $data[$k]['name'] = $v->name;
                $data[$k]['code'] = $v->code;
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /*
     * 根据code 获取市
    * */
    public function getCity($code)
    {
        $result = array();
        try {
            $subcode = substr($code, 0, 2);
            $criteria = new CDbCriteria;
            $criteria->addCondition("code like '$subcode%'");
            $criteria->addCondition("level = :level");
            $criteria->params[':level'] = CITY_LEVEL_CITY;

            $c = ShopCity::model()->findAll($criteria);
            $data = array();
            foreach ($c as $k => $v) {
                $data[$k]['name'] = $v->name;
                $data[$k]['code'] = $v->code;
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    public function getProvinceCityName($code)
    {
        $sp = ShopCity::model()->find('code=:code', array(':code'=>$code));
        return $sp->name; 
    }
    
    /*
     * 根据code 获取区
    * */
    public function getArea($code)
    {
        $result = array();
        try {
            $subcode = substr($code, 0, 4);
            $criteria = new CDbCriteria;
            $criteria->addCondition("code like '$subcode%'");
            $criteria->addCondition("level = :level");
            $criteria->params[':level'] = CITY_LEVEL_AREA;

            $a = ShopCity::model()->findAll($criteria);
            $data = array();
            foreach ($a as $k => $v) {
                $data[$k]['name'] = $v->name;
                $data[$k]['code'] = $v->code;
            }
            $result['status'] = ERROR_NONE;
            $result['data'] = $data;
        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
     * 获取商户门店数量,门店分组和门店
     * $merchant_id 商户id
     * */
    public function getStoreCountAndManagement($merchant_id,$manager_id = '')
    {
        $result = array();
        try {
            if(!empty($manager_id)){
                $manager = Manager::model() -> findByPk($manager_id);
                $store_id = $manager -> store_id;
                $storeId = substr($store_id, 1, strlen($store_id) - 2);
                $right_arr = explode(',', $storeId);
                $store_count = count($right_arr);
            }else{
                //计算商户门店数量
                $store_count = count(Store::model()->findAll('merchant_id =:merchant_id and flag =:flag', array(
                    ':merchant_id' => $merchant_id,
                    ':flag' => FLAG_NO
                )));
            }
            
            
            
            //查询商户的门店分组
            $management = Management::model()->findAll('merchant_id =:merchant_id and flag =:flag', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            //查询商户门店
            $store = Store::model()->findAll('merchant_id =:merchant_id and flag =:flag and management_id is null', array(
                ':merchant_id' => $merchant_id,
                ':flag' => FLAG_NO
            ));
            
            
            $data = array();
            $data['store_count'] = $store_count;
            $data['management'] = array();
            foreach ($management as $k => $v) {
                if (empty($v->p_mid)) {
                    $data['management'][$k]['id'] = $v->id;
                    $data['management'][$k]['name'] = $v->name;
                }
                $management_id_arr[] = $v->id;
            }
            $data['store'] = array();
            $i = 0;
            foreach ($store as $v2) {
                if(!empty($manager_id)){
                    if(!in_array($v2 -> id, $right_arr)){
                        continue;   
                    }
                }
                $show_arr[$i]['id'] = $v2->id;
                $show_arr[$i]['name'] = $v2->name;
                $i++;
            }
            
            $data['store'] = $show_arr;
            $result['data'] = $data;
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }


    /*
     * 获取下级门店分组和门店
     *
     */
    public function getSubmanagementAndStore($management_id,$manager_id = null)
    {
        $result = array();
        try {
            //根据门店分组id查询下级门店分组
            $management = Management::model()->findAll('p_mid =:p_mid and flag =:flag', array(
                ':p_mid' => $management_id,
                ':flag' => FLAG_NO
            ));
            //根据门店分组id查询门店
            $store = Store::model()->findAll('management_id =:management_id and flag =:flag', array(
                ':management_id' => $management_id,
                ':flag' => FLAG_NO
            ));

            $data = array();
            foreach ($management as $k => $v) {
                $data['management'][$k]['id'] = $v->id;
                $data['management'][$k]['name'] = $v->name;
            }

            //如果管理员id不为空，则查询该管理员
            $storeId = '';
            if(!empty($manager_id)){
                $manager = Manager::model() -> findByPk($manager_id);
                $storeId = $manager -> store_id;
                $storeId = substr($storeId, 1, strlen($storeId) - 2);
                $right_arr = explode(',', $storeId);
            }
            
            if(!empty($store)){
                foreach ($store as $k => $v) {
                    if(!empty($manager_id)){
                        if(!in_array($v -> id, $right_arr)){
                            continue;
                        }
                    }
                    $data['store'][] = array();
                    $data['store'][$k]['id'] = $v->id;
                    if (!empty($v->branch_name)) {
                        $data['store'][$k]['name'] = $v->name . '-' . $v->branch_name;
                    } else {
                        $data['store'][$k]['name'] = $v->name;
                    }
                }
            }
            $result['data'] = $data;
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    /*
     * 通过门店分组查询门店id
     */
    public function getStoreByManagement($management_id,$manager_id='')
    {
        $result = array();
        try {
            $store = Store::model()->findAll('management_id =:management_id and flag =:flag', array(
                ':management_id' => $management_id,
                ':flag' => FLAG_NO
            ));
            if(!empty($manager_id)){
                $manager = Manager::model() -> findByPk($manager_id);
                $storeId = $manager -> store_id;
                $storeId = substr($storeId, 1, strlen($storeId) - 2);
                $right_arr = explode(',', $storeId);
            }
            $data = array();
            foreach ($store as $k => $v) {
                if(!empty($manager_id)){
                    if(!in_array($v -> id, $right_arr)){
                        continue;
                    }
                }
                $data['store'][] = array();
                $data['store'][$k]['id'] = $v->id;
                $data['store'][$k]['name'] = $v->name;
            }
            
            $result['data'] = $data;
            $result['status'] = ERROR_NONE;
            $result['errMsg'] = '';

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }
        return json_encode($result);
    }

    //判断门店支付宝门店编号是否重复
    public function createNewAlipayStoreId()
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < 8; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        $store_temp = Store::model()->find('alipay_store_id = :alipay_store_id and flag = :flag', array(
            ':alipay_store_id' => $str,
            ':flag' => FLAG_NO
        ));
        //判断是否重复
        while (!empty($store_temp)) {
            $str = "";
            for ($i = 0; $i < 8; $i++) {
                $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            }
            $store_temp = Store::model()->find('alipay_store_id = :alipay_store_id and flag = :flag', array(
                ':alipay_store_id' => $str,
                ':flag' => FLAG_NO
            ));
        }

        return $str;
    }

    /**
     * 获取商户所有门店列表
     * @param $merchant_id 商户id
     * @return array       返回数组
     */
    public function getStoreListAll($merchant_id)
    {
        $result = array();
        try {
            //参数验证
            //TODO
            $criteria = new CDbCriteria();
            $criteria->addCondition('flag = :flag');
            $criteria->params[':flag'] = FLAG_NO;
            if (!empty($merchant_id)) {
                $criteria->addCondition('merchant_id = :merchant_id');
                $criteria->params[':merchant_id'] = $merchant_id;
            }

            //按创建时间排序
            $criteria->order = 'create_time DESC';

            $model = Store::model()->findAll($criteria);
            if (!empty($model)) {
                //数据封装
                $data = array();
                foreach ($model as $key => $value) {
                    $data['list'][$key]['id'] = $value['id']; //门店id
                    $data['list'][$key]['number'] = $value['number']; //门店编号
                    $data['list'][$key]['alipay_store_id'] = $value['alipay_store_id']; //支付宝编号
                    $data['list'][$key]['merchant_name'] = $value['merchant']['name']; //门店编号
                    $data['list'][$key]['branch_name'] = $value['branch_name']; //门店名称
                    $data['list'][$key]['name'] = $value['name']; //门店名称
                    $data['list'][$key]['address'] = $value['address']; //门店地址
                    $data['list'][$key]['telephone'] = $value['telephone']; //联系电话
                }
                $result['data'] = $data;
                $result['status'] = ERROR_NONE; //状态码
                $result['errMsg'] = ''; //错误信息
            } else {
// 				$this->page = $pages;
                $result['status'] = ERROR_NONE; //状态码
                $data['list'] = array();
                $result['data'] = $data;
            }

        } catch (Exception $e) {
            $result['status'] = isset($result['status']) ? $result['status'] : ERROR_EXCEPTION;
            $result['errMsg'] = $e->getMessage(); //错误信息
        }

        return json_encode($result);
    }

}