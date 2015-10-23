# 快销品平台零售商API
## 1. API接口通信规定
- 接口采用 **HTTP**, **POST** 协议
- 请求URL **192.168.2.66/api/v1**
- 请求返回数据格式
	所有数据返回都基于以下的 **JSON** 协议 
	>失败时返回 ：{"id":'success',"message":"String|Array" ,errors: "{account: ["不是一个合法的账号"]}"}
	id:状态值
	message :返回具体内容
	error : 返回具体字段不合法

## 2. 接口详细说明
### 2.1 公共模块（免登录） auth
#### 2.1.1 登录[post] (login)
`请求参数：`  

	account     string    账号
	password      string    密码
`成功返回:`

	id          int             用户id
	user_name   string          登录名
	type        int             用户类型 (1是终端   2是批发    3是供应)
	shop        array           商店

	shop 字段子集说明

	id                   int            商店id
	name                string          商店名
	contact_person      string          联系人
	contact_info        string          联系方式
	min_money           decimal         最低配送额
	delivery_location   string          地图坐标
	user_id             int             用户id
	image_url           string          商店图片（第一张）
	orders              int             订单数
`失败返回：`

	
#### 2.1.2 注销[get] (logout)
`请求参数：`

	　
`接口返回：`

### 2.2 商品模块 goods
#### 2.2.1 获取商品栏目[get] (goods)
`请求参数`

`成功返回：`

    goodsColumns    array   商品栏目信息

    goodsColumns字段子集说明

    id                  int         栏目id
    name                string      栏目名
    goods               array       栏目商品列表

    goods字段子集说明

    id                  int         商品id
    name                string      商品名
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_promotion        int         是否促销产品
    image_url           string      商品图片

`失败返回：`




#### 2.2.2 商品搜索[post] (search)
`请求参数`

    sort        string              排序 （name , price , new）
    province_id int                 省id
    city_id     int                 市id
    district_id int                 县id
    street_id   int                 街道id
    name        string              商品名
    category_id int                 商品分类id  (如110000   最高位数1为层级，后面为分类id)
    attr        array               标签数组
    page        int                 分页

`成功返回：`

    goods    array   商品信息

    goods字段子集说明

    data     array    商品列表

    data字段子集说明

    id                  int         商品id
    name                string      商品名
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    sales_volume        int         销量
    is_new              int         是否新品（1是 , 0不是）
    is_promotion        int         是否促销产品（1是 , 0不是）
    is_out               int         是否缺货（1是 , 0不是）
    image_url           string      商品图片
    categories          array       商品的分类

    categories 字段子集说明

    id                  int         分类id
    name                string      分类名
    level               int         分类层级
    pid                 int         父级id
    icon_url            string      图标地址

`失败返回：`


#### 2.2.3 商品详情[post] (detail/{goods_id})
`请求参数`


`成功返回：`

    goods    array   商品信息

    goods字段子集说明

    data     array    商品列表

    data字段子集说明

    id                  int         商品id
    name                string      商品名
    sales_volume        int         销售量
    price_retailer      decimal     价格（对于终端商）
    min_num_retailer    int         最低购买量 (对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    min_num_wholesaler  int         最低购买量 (对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_out              int         是否缺货
    is_change           int         是否可换货
    is_back             int         是否可退货
    is_expire           int         是否即期品
    is_promotion        int         是否促销产品
    promotion_info      string      促销信息
    introduce           string      商品图文详情
    shop_id             int         商品所属店铺id
    shop_name           string      商品所属店铺
    attrs               array       标签
    delivery_area       array       商品配送区域
    is_like             bool        是否已关注
    image_url           string      商品图片('第一张')
    images_url          array       商品全部图片

    attrs 子字段说明

    $key => $value形式    $key表示标签名， $value 表示标签值

    delivery_area 字段子集说明

        id                      int             地址id
        province_id             int             省id
        city_id                 int             市id
        district_id             int             县id
        street_id               int             街道id
        area_name               string          省、市、县、街道名
        address                 string          详细地址

    images_url 子字段说明

    name                string      图片名
    path                string      路径

`失败返回：`


### 2.3 店铺模块 shop

#### 2.3.1 获取商店栏目[get] shops

`成功返回：`

    shopColumns    array   店铺栏目信息

    shopColumns字段子集说明

    id                  int         栏目id
    name                string      栏目名
    shops               array       栏目店铺列表

    shops 字段子集说明

    id                  int         店铺id
    name                string      店铺名
    logo_url            string      店铺logo地址
    min_money           decimal     最低配送额
    images_url          array       店铺图片

    images_url 字段子集说明

    name                string      图片名
    path                string      图片路径

#### 2.3.2 店铺详情[get]   ({shop_id})
`请求参数：`

`成功返回：`

    id                  int         店铺id
    name                string      店铺名
    contact_person      string      联系人
    contact_info        string      联系方式
    introduction        string      店铺介绍
    min_money           decimal     最低配送额
    images_url          array       店铺图片地址

    images 字段子集介绍

    name                string      图片名
    path                string      图片路径

#### 2.3.3 店铺商品[get]   ({shop_id}/goods)
`请求参数：`

    page                int         分页

`成功返回：`

    data                array       商品列表

    data 字段子集说明

    id                  int         商品id
    name                string      商品名
    sales_volume        int         销售总量
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_promotion        int         是否促销产品
    is_out              int         是否缺货
    images_url          array       商品图片

    images 字段子集说明

    name                string      图片名
    path                string      图片地址

#### 2.3.3 店铺扩展信息[get]   ({shop_id}/extend)
`请求参数：`

`成功返回：`

    license_url             string          营业执照地址
    business_license_url    string          商品经营许可证地址
    agency_contract_url     string          代理合同地址
    images_url              array           店铺图片地址
    delivery_area           array           配送区域列表

    images_url 字段子集介绍

    name                    string          图片名
    path                    string          图片地址

    delivery_area 字段子集介绍

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址


### 2.4 分类 categories
#### 2.4.1 获取所有分类[post] (all)
`请求参数：`

`成功返回：`

    id                  int         分类id
    icon_url            string      分类图标
    pid                 int         父级id
    name                string      分类名
    level               int         分类层级
    child               array       子级分类（子级分类返回数据与当前数据相同）

`失败返回：`

#### 2.4.2 获取标签[get] ({categoryId}/attrs)
`请求参数：`

    format              bool        是否格式化标签  (非必须参数)

`成功返回：`

   attr_id              int         标签id
   name                 string      标签名
   pid                  int         父级id
   child                array       子级标签 （仅当format为true时返回， 子级分类返回数据与当前数据相同）

`失败返回：`

### 2.5 购物车 cart
#### 2.5.1 查看购物车[post] (index)
`请求参数：`

`成功返回：`

    shops                array       商店列表

    shops 子字段说明

    name                string      商店名
    id                  int         商店id
    min_money           decimal     最低配送额
    cart_goods          array       属于本商店的商品

        cart_goods子字段说明

        id                  int         购物车id
        goods_id            int         商品id
        user_id             int         用户id
        is_like             bool        是否已收藏
        images              string      商品图片
        goods               array       商品详情

            goods子字段说明

            id                  int         商品id
            name                string      商品名
            price_retailer      decimal     价格（对于终端商）
            price_wholesaler    decimal     价格（对于批发商）

`失败返回：`

#### 2.5.2 加入购物车[post] (add/{goodsId})
`请求参数：`

    num                 int         购买数量

`成功返回：`

`失败返回`

#### 2.5.3 删除购物车[delete] (delete/{cartId})
`请求参数：`


`成功返回：`

`失败返回`

### 2.6 订单 order
#### 2.6.1 订单订单[post] (confirm-order)
`请求参数：`

    num                 array       购买的商品和数量 （key=>value形式    key为商品id   value 为购买数量）


`成功返回：`

`失败返回`

#### 2.6.2 获取已确认但未提交订单信息[get] (confirm-order)
`请求参数：`


`成功返回：`

     shops                array       商店列表
     shipping_address     array       收货地址列表
	 pay_type			  array		  支付方式
	 cod_pay_type		  array		  货到付款的支付方式

    shops 子字段说明

    name                string      商店名
    id                  int         商店id
    min_money           decimal     最低配送额
    cart_goods          array       属于本商店的商品

        cart_goods子字段说明

        id                  int         购物车id
        goods_id            int         商品id
        user_id             int         用户id
        is_like             bool        是否已收藏
        images              string      商品图片
        goods               array       商品详情

            cart_goods子字段说明

            id                  int         商品id
            name                string      商品名
            price_retailer      decimal     价格（对于终端商）
            price_wholesaler    decimal     价格（对于批发商）

     shipping_address    子字段说明

     id                 int         收货地址id
     consigner          string      收货人
     phone              string      手机号码
     is_default         int         是否默认
     user_id            int         用户id
     address            array       地址详情

     address    子字段说明

     id                 int         地址id
     province_id        int         省id
     city_id            int         市id
     district_id        int         县id
     street_id          int         街道id
     area_name          string      区域名
     address            string      详细地址


`失败返回`

#### 2.6.3 提交订单[get] (confirm-order)
`请求参数：`

    shop                array       商店

    shop 子字段说明（key=>value）  key为商店id

    shipping_address_id int         收货地址id
    pay_type            string      支付方式 （online 在线 ， cod 货到付款）
    remark              string      订单备注信息

`成功返回：`

`失败返回`


#### 2.5.4 买家获取订单列表[get] (list-of-buy)
`请求参数：`

    page                int         分页

`成功返回：`

	data                array       订单信息

	data 字段子集说明
	
	id					int			订单ID号
	price               string      订单总金额
	status_name			string		订单显示状态
	payment_type		string      支付方式(如:在线支付;货到付款)
	cod_pay_type		int			货到付款支付方式(1:现金;2:刷卡)
	pay_type			int			支付方式(1:在线支付;2:货到付款)
	pay_status			int			支付状态(0:未付款;1:已付款)
	status				int			订单状态(1:未发货;2:已发货;3:完成)
	is_cancel			int			订单是否被取消(1取消,0未取消)
	shop                array       店铺信息
    goods    			array		商品信息

	shop 字段子集说明
		
	name				string		店铺名字
	user				array		卖家信息

		user 字段子集说明

		user_name       string      卖家账户名称
		type			int         卖家角色类型
	
	goods 字段子集说明
	
	id  				int 		商品ID
	name                string 		商品名称
	introduce			string		商品描述信息
	image_url			string		商品图片地址
	pivot				array		该商品在本订单中的详细信息

		pivot 字段子集说明

		price			string		商品价格
		num				int			商品数量
	
`失败返回`


#### 2.5.5 买家待付款订单列表[get] (non-payment)(仅显示在线支付订单)
`请求参数：`

	page 				int			分页

`成功返回：`
	
	返回信息同上

`失败返回`


#### 2.5.7 买家待收货订单列表[get] (non-arrived)
`请求参数：`

	page 				int			分页

`成功返回：`
	
	返回信息同上

`失败返回`

#### 2.5.10 买家批量确认订单完成[put] (batch-finish-of-buy)(仅针对在线支付订单)
`请求参数：`

	order_id  				array		订单id

`成功返回：`


`失败返回：`

#### 2.5.8 买家获取订单详情[get] (detail-of-buy)(仅发货后和完成后才能查看)
`请求参数：`

	order_id			int			订单号

`成功返回：`

	id					int			订单ID号
	price               string      订单总金额
	status_name			string		订单显示状态
	payment_type		string      支付方式
	is_cancel			int			订单是否被取消(1取消,0未取消)
	remark				string		订单备注信息
	created_at			string		创建时间
	paid_at				string		支付时间
	send_at				string		发货时间
	finished_at			string		完成时间
	delivery_man		array		送货人信息(仅发货后才有,否则为Null)
	shipping_address    array       收货信息
    goods    			array		商品详细信息

	delivery_man 字段子集说明

	name     			string		送货人姓名
	phone				string		送货人电话

	shipping_address 字段子集说明

	consigner			string		收货人姓名
	phone				string		收货人电话
	address				array		收货地址信息
		
		address 字段子集说明
		
		province_id         int         省id
		city_id             int         市id
    	district_id         int         县id
    	street              int         街道id
		address				string		详细地址

`失败返回`


#### 2.5.4 卖家获取订单列表[get] (list-of-sell)
`请求参数：`

    page                int         分页

`成功返回：`

	data                array       订单信息

	data 字段子集说明
	
	id					int			订单ID号
	price               string      订单总金额
	status_name			string		订单显示状态
	payment_type		string      支付方式(如:在线支付;货到付款)
	cod_pay_type		int			货到付款支付方式(1:现金;2:刷卡)
	pay_type			int			支付方式(1:在线支付;2:货到付款)
	pay_status			int			支付状态(0:未付款;1:已付款)
	status				int			订单状态(1:未发货;2:已发货;3:完成)
	is_cancel			int			订单是否被取消(1取消,0未取消)
	user                array       买家信息
    goods    			array		商品信息

	user 字段子集说明
		
	user_name			string		买家名字
	
	
	goods 字段子集说明
	
	id  				int 		商品ID
	name                string 		商品名称
	introduce			string		商品描述信息
	image_url			string		商品图片地址
	pivot				array		该商品在本订单中的详细信息

		pivot 字段子集说明

		price			string		商品价格
		num				int			商品数量
		
`失败返回`


#### 2.5.9 卖家待发货订单列表[get] (non-send)
`请求参数：`

	page                int         分页

`成功返回：`

	返回信息同上

`失败返回：`


#### 2.5.9 卖家待收款订单列表[get] (pending-collnection)(仅针对货到付款订单)
`请求参数：`

	page                int         分页

`成功返回：`

	返回信息同上

`失败返回：`


#### 2.5.9 卖家获取订单详情[get] (detail-of-sell)
`请求参数：`

	order_id  			int			订单id

`成功返回：`

	id					int			订单ID号
	price               string      订单总金额
	status_name			string		订单显示状态
	payment_type		string      支付方式(显示)
	pay_type			int			支付方式
	cod_pay_type		int			货到付款方式
	is_cancel			int			订单是否被取消(1取消,0未取消)
	remark				string		订单备注信息
	created_at			string		创建时间
	paid_at				string		支付时间
	send_at				string		发货时间
	finished_at			string		完成时间
	shipping_address    array       收货信息
    goods    			array		商品详细信息

	delivery_man 字段子集说明

	name     			string		送货人姓名
	phone				string		送货人电话

	shipping_address 字段子集说明

	consigner			string		收货人姓名
	phone				string		收货人电话
	address				array		收货地址信息
		
		address 字段子集说明
		
		province_id         int         省id
		city_id             int         市id
    	district_id         int         县id
    	street              int         街道id
		address				string		详细地址

`失败返回：`


#### 2.5.10 卖家批量确认订单完成[put] (batch-finish-of-sell)(仅针对货到付款订单)
`请求参数：`

	order_id  				array		订单id
	
`成功返回：`


`失败返回：`


#### 2.5.10 卖家批量发货[put] (batch-send)
`请求参数：`

	order_id  				array		订单id
	delivery_man_id			int			配送员id号
	
`成功返回：`


`失败返回：`

#### 2.5.9 买家/卖家批量取消订单[put] (cancel-sure)
`请求参数：`

	order_id  				array		订单id

`成功返回：`


`失败返回：`


### 2.6 收藏 like
#### 2.5.1 商店收藏[post] (shops)
`请求参数：`

    province_id         int         省id
    city_id             int         市id
    district_id         int         县id
    street              int         街道id

`成功返回：`

    shops               array       商店列表

    shops 字段说明

    id                  int         店铺id
    name                string      店铺名
    min_money           decimal     最低配送额
    image_url           string      店铺图片
    orders              int         店铺销量

`失败返回`

#### 2.7.2 商品收藏[post] (goods)
`请求参数：`

    province_id         int         省id
    city_id             int         市id
    district_id         int         县id
    street              int         街道id

`成功返回：`

    goods               array       商品列表
    cateArr             array       分类列表

    goods 字段说明

    id                  int         商品id
    name                string      商品名
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_promotion        int         是否促销产品
    image_url           string      商品图片

    cateArr 字段说明

    id                  int         分类id
    name                string      分类名
    level               string      分类层级

`失败返回:`


### 2.10 配送信息 delivery-man
#### 2.10.1 配送人员列表[get] 
`请求参数:`

`成功返回:`

	delivery_man		array		配送人员信息
	
	delivery_man 字段子集说明
	
	id					int			配送人员ID
	name				string		姓名
	phone				string		电话

`失败返回:`

#### 2.10.2 添加配送人员[post] 
`请求参数:`
	
	name				string		姓名
	phone				int			电话(长度7~14位)

`成功返回:`

`失败返回:`

#### 2.10.3 编辑配送人员信息[put] 
`请求参数:`
	
	id					int			配送人员ID
	name				string		姓名
	phone				int			电话(长度7~14位)

`成功返回:`

	
`失败返回:`

#### 2.10.4 删除配送人员[delete] 
`请求参数:`
	
	id					int			配送人员ID

`成功返回:`

	
`失败返回:`