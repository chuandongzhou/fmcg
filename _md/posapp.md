#司机App API
#1,API接口通信规定
-接口采用**HTTP**,**POST**协议
-请求URL **192.168.2.66/api/v1**
-请求返回数据格式
	所有数据返回都基于以下的**json**协议
	>失败时返回：{"id":'success',"message":"String|Array",'errors':"{account:["不是一个合法的账号"]}"}
	id:状态值
	message:返回具体内容
	error:返回具体字段不合法
## 2，接口详细说明
### 2.1 公共模块 delivery
#### 2.1.1 登陆[post] (login)
`请求参数：`
	
	user_name   string       账号

	password    string       密码

`成功返回：`
	id           int         配送人员id

	user_name    string      登录名

	password     string      密码

`失败返回：`


#### 2.2.2 分配的订单信息[get] (orders)
`请求参数：`

`成功返回：`

	id                  int            订单号
	price               float          订单金额
	is_pay              int            支付状态（0未支付，1已支付）
	pieces              array          所有的商品单位
	user_shop_name      string         收货店家名
	after_rebates_price decimal     优惠后订单价格
	shippingAddress     array          收货信息
	
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

####2.2.3 完成配送操作[get] (deal-delivery)
`请求参数：`

	order_id        int          订单号

`成功返回：`

`失败返回：`

####2.2.4 订单详情[get] (detail)
`请求参数：`

	order_id        int          订单id

`成功返回：`
	
	id               int         订单号
	price            float       订单金额
	is_pay           int         支付状态（0未付款，1已付款）
	delivery_finished_at  string    配送完成时间（该字段时空表示未完成配送）
	user             array       收货店家信息
	shippingAddress  array       收货信息
	goods            array       商品信息
	user_shop_name          string          收货店家名
    after_rebates_price     decimal         优惠后价格
	
	user字段说明
	shop            array       收货店家
	type			int         买家用户类型（1是终端   2是批发）

	shop字段说明
	name            string      收货店家名
	
	shippingAddress字段说明
	consigner       string       收货人姓名
	phone           string       收货联系电话
	address         array        收货地址
	
	address字段说明
	area_name       string      收货地址（area_name和address组合在一起是具体收货地址）
	address         string      详细收货地址
	
	goods字段说明
	name            string        商品名称
   
	specification_retailer  string     规格  （对于终端商）
	
    specification_wholesaler  string  规格  （对于批发商 供应商时添加）

	promotion_info      string      促销信息    （取当是促销时传入）

	pivot               array        购买商品信息中间表

	image_url           string      商品图片('第一张')

    images_url          array       商品全部图片
	
	pivot字段说明
	price              decimal       购买商品单位价格
	num                int           购买商品数量
	total_price        decimal       购买商品总价格
	pieces             int           单位编号 （对于终端商  0盒  1瓶 2箱 3听 4条 5袋  6罐  7包 8桶 9杯 10支 11个 12筒） 
`失败返回：`

####2.2.5 订单历史记录[get] (history-orders)
`请求参数：`

	start_at        date        开始时间
	end_at          date        结束时间

`成功返回：`

	 historyOrder         array            历史订单详情
	 
	order字段说明

	date              string              日期

	data 			array               订单详情
	
	data字段说明
          
	id                   int              订单号
	delivery_finished_at  string          订单完成时间
    user                  array           收货店家信息

	user字段说明
	shop                array            店家信息

	shop字段说明          string          收货店家名称

`失败返回：`

####2.2.6 退出登陆[get] (logout)
`请求参数：`

`成功返回：`

`失败返回：`

####2.2.7 修改订单[post] (update-order)
`请求参数：`

	order_id          int            订单号
	num               int            修改后的数量
	pivot_id          int            商品订单关联表ID

`成功返回：`

`失败返回：`


####2.2.8 配送统计[get] (delivery-statistical)
`请求参数：`

	start_at        date           开始时间
	end_at          date           结束时间

`成功返回：`

	deliveryMan     array          配送人员统计
	goods           array          商品统计

	deliveryMan字段说明

		name        string           配送人员姓名
		num         int              配送单数
		price       decimal          配送总金额
		detail      array            配送金额组成
		
		detail字段说明
			pay_type     int        交易方式（0：现金，其他按正常表示）
			amount       decimal    交易金额（其中:易宝=易宝+pingxx_易宝，支付宝支付=支付宝+支付宝app）

	goods字段说明
		name       string            商品名称
		detail     array             按单位统计详细信息
		
		detail字段说明
			pieces     int          商品单位
			num        int          商品数量
			amount     decimal      商品金额
			


`失败返回：`
	
	
	
	
	