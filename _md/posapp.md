#司机App API
##1.API接口通信规定
- 接口采用**HTTP**,**POST**协议
- 请求URL **192.168.2.66/api/v1**
- 附加请求参数 **ter_type**: **web端传1**, **ios端传2**, **android端传3**
- 请求返回数据格式
	所有数据返回都基于以下的**json**协议
	>失败时返回：{"id":'success',"message":"String|Array",'errors':"{account:["不是一个合法的账号"]}"}
	id:状态值
	message:返回具体内容
	error:返回具体字段不合法
## 2.接口详细说明
### 2.1 公共模块 delivery
#### 2.1.1 登陆[post] (login)
`请求参数：`
	
	user_name   string       账号

	password    string       密码

`成功返回：`

	id           int         配送人员id
	user_name    string      登录名
	password     string      密码
	pos_sign     string      pos机编号
	name         string      司机名称

`失败返回：`


#### 2.1.2 分配的订单信息[get] (orders)
`请求参数：`

	id_name				  int/string	订单号/买家店铺名

`成功返回：`

	id                    int            订单号
	price                 float          订单金额
	is_pay                int            支付状态（0未支付，1已支付）
	pieces                array          所有的商品单位
	user_shop_name        string         收货店家名
	created_at            string         下单时间
	remark                string          订单备注
	after_rebates_price   decimal     优惠后订单价格
	shippingAddress       array          收货信息
	
	
	shippingAddress字段说明
	
	consigner       string      收货人
	phone           string      收货人联系方式
	x-lng           string      收货地址经度（隐藏字段）
	y_lat           string      收货地址纬度（隐藏字段）
	address         array       收货地址
		
	address字段说明
	
	area_name       string      收货地址（area_name和address组合在一起是具体收货地址）
	address         string      详细收货地址
	
`失败返回：`

####2.1.3 完成配送操作[get] (deal-delivery)
`请求参数：`

	order_id        int          订单号

`成功返回：`

`失败返回：`

####2.1.4 订单详情[get] (detail)
`请求参数：`

	order_id        int          订单id

`成功返回：`
	
	id               		int         	订单号
	price           		float       	订单金额
	is_pay           		int         	支付状态（0未付款，1已付款）
	delivery_finished_at  	string    		配送完成时间（该字段时空表示未完成配送）
	user             		array       	收货店家信息
	shippingAddress  		array       	收货信息
	orderGoods            		array       商品信息
	user_shop_name          string          收货店家名
    after_rebates_price     decimal         优惠后价格
	mortgageGoods           array           陈列费商品
	display_fee             decimal         陈列费
	paid_at                 string          订单支付时间
    promo					array			促销活动
	gifts					array			赠送商品
	status					int				订单状态(0:未确认,1:未发货,2:已发货,3:完成,4:已作废)
	
	user字段说明
	shop            		array       收货店家
	type					int         买家用户类型（1是终端   2是批发）

	shop字段说明
	name            		string      收货店家名

	promos 子集介绍
	
	id						int					促销活动ID（编号）
	name					string				促销名称
	type					int					促销类型（1:自定义,2:钱返钱,3：钱返商品,4:商品返钱,5:商品返商品）
	start_at				date				开始时间
	end_at					date				结束时间
	remark					string				促销备注
	condition				array				促销申请条件
	rebate					array				促销活动返利

	condition 子集介绍

	goods_id				int					条件商品ID
	quantity				int					条件商品数量
	unit					int					条件商品单位代码
	goods_name				string				条件商品名称
	goods_pirces			string				条件商品单位名称
	money					int					条件金额
	custom					strint				自定义条件	

	rebate	子集介绍
	
	goods_id				int					返利商品ID
	quantity				int					返利商品数量
	unit					int					返利商品单位代码
	goods_name				string				返利商品名称
	goods_pirces			string				返利商品单位名称
	money					int					返利金额
	custom					strint				自定义返利
	
	gifts字段子集说明

    id             		    int         	    商品id
    num              	    int         	    个数
    pieces           	    int         	    单位
	
	shippingAddress字段说明
	consigner       		string       收货人姓名
	phone          	 		string       收货联系电话
	address         		array        收货地址
	
	address字段说明
	area_name       		string      收货地址（area_name和address组合在一起是具体收货地址）
	address         		string      详细收货地址
	
	orderGoods字段说明
	name           			string        商品名称
   
	specification_retailer  string     规格  （对于终端商）
	
    specification_wholesaler  string  规格  （对于批发商 供应商时添加）

	promotion_info      	string      促销信息    （取当是促销时传入）

	pivot               	array        购买商品信息中间表

	image_url           	string      商品图片('第一张')

    images_url          	array       商品全部图片
	
	pivot字段说明
	price              		decimal       购买商品单位价格
	num                		int           购买商品数量
	total_price        		decimal       购买商品总价格
	pieces             		int           单位编号 （对于终端商  0盒  1瓶 2箱 3听 4条 5袋  6罐  7包 8桶 9杯 10支 11个 12筒） 


	mortgageGoods字段子集说明

	name     		string       陈列商品名称
	image_url 		string       陈列商品图片
	pivot          array        中间信息

	pivot字段子集说明

	num            int          陈列商品数量
	price          decimal      陈列商品单价
	total_price    decimal      陈列商品总价
	pieces         int          陈列商品单位

`失败返回：`

####2.2.5 订单历史记录[get] (history-orders)
`请求参数：`

	start_at        	   date        		开始时间
	end_at          	   date        		结束时间
	id_name				  int/string		订单号/买家店铺名

`成功返回：`

	 historyOrder          array            历史订单详情
	 
	order字段说明

	date                   string              日期

	data 			       array               订单详情
	
	data字段说明
          
	id                    int              订单号
	delivery_finished_at  string          订单完成时间
	user_shop_name        string          收货店家名称
	pay_status            int              支付状态（0，未支付；1，已支付）
   

`失败返回：`

####2.1.6 退出登陆[get] (logout)
`请求参数：`

`成功返回：`

`失败返回：`

####2.1.7 修改订单[post] (update-order)
`请求参数：`

	order_id          int            订单号
	num               int            修改后的数量
	pivot_id          int            商品订单关联表ID

`成功返回：`

`失败返回：`


####2.1.8 配送统计[get] (delivery-statistical)
`请求参数：`

	start_at        	date           			开始时间
	end_at          	date           			结束时间
	num					int			   			订单配送人数（可选参数）

`成功返回：`

	deliveryNum     	array          			所有配送订单人数
	data            	array          			统计数据

	data字段说明
	
	goods          		array          			 商品统计
	deliveryMan    		array         			 配送人员统计

	deliveryMan字段说明

		name        	string           		 配送人员姓名
		num         	int             		 配送单数
		price       	decimal         		 配送总金额
		discount        decimal                  总优惠金额
		display_fee     decimal                  总陈列现金
		detail      	array            		 配送金额组成
		
		detail字段说明
			pay_type     int        		交易方式（0：现金，其他按正常表示）
			amount       decimal    		交易金额（其中:易宝=易宝+pingxx_易宝，支付宝支付=支付宝+支付宝app）

	goods字段说明
		deliveryManNum     int     			订单配送人数
		allGoods           array   			按商品名称分类明细

	allGoods字段说明
		name          string          		商品名称
		data          array                按购买单位分类明细


	data字段说明

		num_pieces_format	string				商品数量格式化
		amount    			 decimal      		商品金额
			


`失败返回：`

#### 2.1.9 订单商品删除[delete] (order-goods-delete/{order_goods_id})
`请求参数：`

`成功返回：`

`失败返回：`

#### 2.1.10 修改密码[ post] (modify-password)	

`请求参数：`

	old_password             string             原密码
	password                 string             新密码
	password_confirmation    string             确认新密码

`成功返回：`

`失败返回：`  


#### 2.1.11  检查最新版本[get] (latest-version)

`请求参数：`

`成功返回：`
		
	record                  array             版本信息
	download_url            string            下载地址
	

	record字段说明
	
	version_name           string             版本名称
    version_no             string			  版本号
	content                string             内容

`失败返回：`

#### 2.1.12  获取当前发车单详情[get] (now)

`请求参数：`

`成功返回：` 

	见仓管2.4.7

#### 2.1.13  作废订单[post] (order/{order_id}/cancel)

`请求参数：`

	reason					string				原因
`成功返回：` 

#### 2.1.14  订单确认收款[get] (order-complete/{order_id})

`请求参数：`


`成功返回：` 



### 2.2 微信二维码支付 wechat-pay

#### 2.2.1  获取二维码[get] (qrcode/{order_id})

`请求参数：`

`成功返回：`

    code_url              string                二维码地址
    created_at            timestamp             二维码生成时间
    
    
#### 2.2.2  获取订单支付状态[get] (order-pay-status/{order_id})

`请求参数：`

`成功返回：`

    pay_status             tinyint              订单支付状态（0=>未支付， 1=> 支付成功）

    
### 2.3 银联二维码支付 union-pay

#### 2.3.1  获取二维码[get] (qrcode/{order_id})

`请求参数：`
    
    pay_type                string              支付渠道 （alipay为支付宝二维码， wechat为微信二维码）

`成功返回：`

    code_url              string                二维码地址
    
    
#### 2.3.2  获取订单支付状态[get] (order-pay-status/{order_id})

`请求参数：`

`成功返回：`

    pay_status             tinyint              订单支付状态（0=>未支付， 1=> 支付成功）

