#快销品平台零售商API
##1. API接口通信规定
- 接口采用**HTTP**,**POST**协议
- 请求URL **192.168.2.66/api/v1/business/**
- 附加请求参数 **ter_type**: **web端传1**, **ios端传2**, **android端传3**
- 请求返回数据格式
	所有数据返回都基于以下的**json**协议
	>失败时返回：{"id":'success',"message":"String|Array",'errors':"{account:["不是一个合法的账号"]}"}
	id:状态值
	message:返回具体内容
	error:返回具体字段不合法
## 2. 接口详细说明
### 2.1 公共模块auth
#### 2.1.1 登陆[post] (login)
`请求参数：`
	
	account     string       账号
	password    string       密码

`成功返回：`

    salesman               array           当前登录业务员

    salesman字段子集说明

      id                    int             业务员id
      account               string          账号
	  maker_id				int				厂家ID(不是则为null)
      shop_id               int             业务员所属店铺id
      name                  string          业务员名
      contact_information   string          联系方式
      avatar_url            string          头像
	  is_maker				int				是否厂家业务员(0:不是,1:是)
      shop_type             int             所属店铺类型id (1终端 2批发 3供应)

`失败返回：`

#### 2.1.1 退出登陆[get] (logout)
`请求参数：`

`成功返回：`

`失败返回：`


### 2.2 业务员模块salesman
#### 2.2.1 首页数据[get] (home-data)
`请求参数：`


`成功返回：`

    target                  int             业务员本月目标
    thisMonthCompleted      decimal         本月已完成订单金额
    untreatedOrderForms     int             未处理订单数
    untreatedReturnOrders   int             未处理退货单数
    todayVisitCount         int             今日拜访客户数
	
`失败返回：`

#### 2.2.2 编辑个人资料[put] (update-by-app)
`请求参数：`

    avatar                  file            头像
    name                    string          姓名
    contact_information     string          联系方式
    password                string          密码（非必填，不填是不修改密码）

`成功返回：`

`失败返回：`

#### 2.2.3 修改密码[put] (password)

`请求参数：`

    old_password            string          原密码
    password                string          新密码
    password_confirmation   string          确认新密码

`成功返回：`

`失败返回：`

### 2.3 客户模块salesman-customer
#### 2.3.1 获取所有客户[get] (/)
`请求参数：`

`成功返回：`

    customers               array           客户数据

    customers字段子集说明（包含分页信息）


    id                      int             客户ID
    number                  int             客户号
    name                    string          客户名
    letter                  char            客户名首字母
    shop_id                 int             客户的平台id
    account                 string          客户的平台账号
    store_type              int             客户商店类型
	store_type_name			string			客户商店类型名称
    type                    int             客户类型（1 为终端商， 2 批发商）
    contact                 string          联系人
    contact_information     string          联系方式
    business_area           string          营业面积
    business_address_lng    float           营业地址经度
    business_address_lat    float           营业地址纬度
    shipping_address_lng    float           收货地址经度
    shipping_address_lat    float           收货地址纬度
    business_address        array           营业地址详情
    shipping_address        array           营业地址详情
    display_type            tinyint         陈列类型    （0 无陈列， 1 现金 ， 2 商品）
    display_start_month     string          陈列开始月份
    display_end_month       string          陈列结束月份
    display_fee             decimal         陈列费
    mortgage_goods          array           陈列商品信息

    business_address字段子集说明

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县/区id
    street_id               int             街道id
    area_name               string          省+市+县+街道名
    address                 string          详细地址

    business_address字段子集同business_address字段子集说明

`失败返回：`

#### 2.3.2 客户编辑[put] (update-by-app/{id})
`请求参数：`

    name                    string          客户名称
    contact                 string          联系人
    type                    int             客户类型（登录业务员店铺为供应商时传入[1 为终端商， 2 批发商]）
    contact_information     string          联系方式
    business_area           string          营业面积
    account                 string          客户的平台账号(选填)
    business_address        array           营业地址
	store_type				int				客户商店类型
    shipping_address        array           收货地址
    business_address_lng    float           营业地址经度
    business_address_lat    float           营业地址纬度
    shipping_address_lng    float           收货地址经度
    shipping_address_lat    float           收货地址纬度
    display_type            tinyint         陈列类型    （0 无陈列， 1 现金 ， 2 商品）
    display_start_month     string          陈列开始月份（当display_type不为0时传入。 格式如'2016-10' ）
    display_end_month       string          陈列结束月份（当display_type不为0时传入。 格式如'2016-12' ）
    display_fee             decimal         陈列费用（当display_type 为1时入）
    mortgage_goods          array           抵费商品 （当display_type为2时传入，陈列费商品id=>数量 格式。 如[5=>100]）



    business_address字段子集说明

    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址

    shipping_address字段子集同上

`成功返回：`

`失败返回：`

#### 2.3.3 客户添加[post] (/)
`请求参数：`

    name                    string          客户名称
    contact                 string          联系人
    type                    int             客户类型（登录业务员店铺为供应商时传入[1 为终端商， 2 批发商]）
    contact_information     string          联系方式
    business_area           string          营业面积
    account                 string          客户的平台账号(选填)
	store_type				int				客户商店类型
    business_address        array           营业地址
    shipping_address        array           收货地址
    business_address_lng    float           营业地址经度
    business_address_lat    float           营业地址纬度
    shipping_address_lng    float           收货地址经度
    shipping_address_lat    float           收货地址纬度
    display_type            tinyint         陈列类型    （0 无陈列， 1 现金 ， 2 商品）
    display_start_month     string          陈列开始月份（当display_type不为0时传入。 格式如'2016-10' ）
    display_end_month       string          陈列结束月份（当display_type不为0时传入。 格式如'2016-12' ）
    display_fee             decimal         陈列费用（当display_type 为1时入）
    mortgage_goods          array           抵费商品 （当display_type为2时传入，陈列费商品id=>数量 格式。 如[5=>100]）
    business_address字段子集说明

    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址

    shipping_address字段子集同上

`成功返回：`

`失败返回：`

#### 2.3.4 获取所有销售商品[get] (sale-goods)
`请求参数：`

    salesman_customer_id        int         客户id

`成功返回：`

    goods                       array       商品列表

    goods字段子集介绍

    id                          int         商品id
    name                        string      商品名
    image_url                   string      商品图片
    price_retailer              decimal     终端商价格
    pieces_retailer             int         终端商单位
    price_wholesaler            decimal     批发商价格
    pieces_wholesaler           int         批发商单位
    goods_pieces                array       商品单位（新， 为null时未设置）

    goods_pieces字段子集介绍

    pieces_level_1              int         一级单位
    pieces_level_2              int         二级单位（为null时未设置）
    pieces_level_3              int         三级单位（为null时未设置）

`失败返回`

#### 2.3.5 添加销售商品[post] (add-sale-goods)
`请求参数：`

    salesman_customer_id        int         客户id
    goods_id                    array       商品id列表

`成功返回：`

`失败返回`

#### 2.3.6 移除销售商品[delete] (delete-sale-goods)
`请求参数：`

    salesman_customer_id        int         客户id
    goods_id                    int         商品id

`成功返回：`

`失败返回`
#### 2.3.7 客户陈列费发放情况查询[get] (customer-display-fee)
`请求参数：`

	keyword                        string      客户关键字
	month                          date        月份

`成功返回：`

	customers                 array         所有客户

	customers子字子集段说明
	
	id                          int             客户id
	name                        string          客户名
	display_type                int             客户陈列类型
	display_fee                 decimal         应发现金（当display_type为 1 时使用）
	business_address_name       string          客户营业地址
	orders                      array           订单实发陈列费列表
	mortgage_goods              array           应发抵费商品 （当display_type为 2 时使用）

	orders子字子集段说明

	id                          int             订单id
	order_status_name           string          订单状态
	used                        decimal         此订单实发陈列费现金（当display_type为 1 时使用）
	mortgages                   array           此订单实发陈列费商品 （当display_type为 2 时使用）
    created_at                  timestamp       订单创建时间

        mortgages字段子集说明

        id                      int             陈列费商品id
        name                    string          陈列费商品名
        used                    int             发放数量

    mortgage_goods子字子集段说明

    id                          int             陈列商品id
    goods_name                  string          商品名
    pivot                       array           应发陈列费商品信息

        pivot子字子集段说明

        total                   int             应发陈列费商品




`失败返回：`


#### 2.3.8 陈列费发放情况查询[post] (display-fee)
`请求参数：`

	start_at                    date        开始时间
	end_at                      date        结束时间

`成功返回：`

	orders                    array         所有订单
	
	orders子字子集段说明
	
	id                       int           订单ID
	status                   int           订单状态
	created_at               date          下单时间
	display_fee              decimal       陈列费现金
	order_remark             string        订单备注
	display_remark           string        陈列费备注
	mortgage_goods           array         陈列费抵费商品
	
	mortgage_goods字段子集说明
	
	goods_name              string         抵费商品名称
	
	pivot                   array          中间信息

	pivot字段子集说明
	
	num                     int            抵费商品数量

`失败返回：`


####2.3.9 客户曾购买的商品查询[get] (purchased-goods)	
`请求参数：`

	customer_id           int             客户ID

`成功返回：`
	data                 array            所有购买信息

	data字段子集说明
	goods_id             int              商品ID
	price                decimal          商品价格
	pieces               int              商品单位


####2.3.10 获取全部商店类型[get] (store-type)	
`请求参数：`

`成功返回：`
	data                 array            所有类型
	key					 int			  类型代码
	value				 string			  具体类型
	
	
	


### 2.4 抵费商品 mortgage-goods
#### 2.4.1 获取所有抵费商品[get] (/)
`请求参数：`

`成功返回：`

    mortgageGoods          array            抵费商品（包含分页）
    
    data                    array           抵费商品列表
    
    data字段子集介绍
    
    id                      int             商品主键
    goods_id                int             抵费商品id
    goods_name              string          商品名
    pieces                  int             商品单位id
    
`失败返回`



### 2.5 拜访模块 visit
#### 2.5.1 获取所有拜访[get] (/)
`请求参数：`

    start_date              date            开始日期 （字符串，如： 2016-8-10）
    end_date                date            结束日期 （字符串，如： 2016-8-10）
    name                    string          客户名


`成功返回：`

    visit                   array           拜访列表
    
    visit字段子集说明
    
    number                  int             客户号
    name                    string          客户名
    letter                  string          客户名首字母
    contact                 string          联系人
    contact_information     string          联系方式
	orders					array           订单列表
	visits                  array           拜访记录列表
	business_address		object          营业地址
    
    orders字段子集说明

    id                	    int             订单ID
	type                    int             订单类型（0 订货单 ， 1退货单 ）
	created_at              timestamp       订单生成时间
	amount                  decimal         订单金额
	order_remark            string          订单备注
	display_remark          string          陈列费备注
	
   
    visits字段子集介绍

    id                     int             拜访ID
	created_at             timestamp       拜访时间

	business_address字段说明
	
	area_name              string         营业地址
	address                string         营业地址详情

`失败返回：`

#### 2.5.2 拜访详情 [get] (visit/{visit_id})

`请求参数：`

`成功返回：`

    visit_id                int             拜访id
    customer_name           string          客户名
    contact                 string          联系人
    contact_information     string          联系方式
    statistics              array           订单商品
    mortgage                array           抵费商品 [月份=>商品信息]
	gifts					array			赠品
	promo					array			促销活动信息
    display_fee             array           订单陈列费 [月份 => 费用]
	photos					array			所有拜访图片url
    
    statistics字段子集说明
    
    goods_id                int             商品id
    order_num               int             订货数量
    order_amount            decimal         订货金额
    price                   decimal         商品单价
    pieces                  string          商品单位
    goods_name              string          商品名
    stock                   string          商品库存
    production_date         date            生产日期
    return_amount           decimal         退货金额
    return_order_num        string          退货数量
    image_url               string          商品图片地址
    
    mortgage字段子集（商品信息）说明

    id                      int             商品id
    name                    string          商品名
    num                     int             数量
    pieces                  string          单位
    month                   string          月份

    gifts字段子集说明

    id                	    int             商品id
    num              	    int             个数
    pieces           	    int             单位

    promo字段子集说明
	同2.10.1
<a href="#2.10.1">2.10.1</a>


 `失败返回：`

 
#### 2.5.3 添加拜访 [post] (/)
`请求参数：`

    salesman_customer_id    int             客户id
    x_lng                   float           经度
    y_lat                   float           纬度
    address                 string          地址
    goods                   array           拜访商品列表
    order_remark            string          订单备注
    display_remark          string          陈列费备注
	apply_promo_id			int				促销申请ID
    gifts                   array           赠品
    display_fee             array           陈列费用（当客户display_type 为1时入 ,如 ['2016-10'=> 100, '2016-11' => 100]）
    mortgage                array           抵费商品（当客户display_type 为2时传入，月份=>['id'=>抵费商品id, 'num'=>商品数量] 格式。 如['2016-10'=>['id' => 5, 'num'=>100]]）

    
    goods字段子集说明
    
    id                      int             商品id
    stock                   string          商品库存
    production_date         date            商品生产日期（'2016-07-13'）
    order_form              array           订货数据（要订此商品时传入）
    return_order            array           退货数据（要退此商品时传入）
    
    order_form字段子集说明
        pieces              int             单位id
        price               decimal         商品单价
        num                 int             订货数量
    return_order字段子集说明
    
        amount              decimal         退款金额
        num                 string          退货数量
        pieces              int             单位id

    gifts字段子集说明

        id                  int             商品id
        num                 int             个数
        pieces              int             单位

`成功返回：`
	
`失败返回：` 
#### 2.5.4 添加拜访图片 [post] (visit/{visit_id}/add-photos)
	photos					array			图片数组
![](../img/photosTemplate.png)

`请求参数：`

`成功返回：`

#### 2.5.5 判断是否可以添加拜访 [get] (can-add/{customer_id})
`请求参数：`

`成功返回：`
	visit               array            该字段为空表示可以添加
	
`失败返回：`

#### 2.5.6 根据月份获取客户剩余陈列商品 [get] (surplus-mortgage-goods)
`请求参数：`

    customer_id         int                 客户id
    month               string              月份（'2016-10'）

`成功返回：`

    surplus             array               客户当月剩余陈列商品数量
    noConfirm           array               当月陈列商品未审核订单

    surplus字段子集介绍

    id                  int                 陈列费商品id
    name                string              商品名
    surplus             int                 剩余量
    pieces_name         string              单位

    noConfirm字段子集介绍

    id                  int                   订货单号
    time                timestamp             下单时间
    mortgageGoods       array                 陈列费列表

`失败返回：`


#### 2.5.7 根据月份获取剩余陈列费 [get] (surplus-display-fee)
`请求参数：`

     customer_id         int                 客户id
     month               string              月份（'2016-10'）

`成功返回：`

    surplus             decimal             客户当月剩余陈列费
    noConfirm           array               当月陈列费未审核订单

    noConfirm字段子集介绍

    month                       string              月份
    used                        decimal             金额
    sales_man_visit_order       int                 订单号
    created_at                  timestamp           下单时间


`失败返回：`

### 2.6 订单模块order
#### 2.6.1 获取所有订货单[get] (order-forms)
`请求参数：`

    customer            string/int          客户名称/单号
    status              int                 状态   （0未审核  1已通过）
    start_date          date                开始时间
    end_date            date                结束时间
    page                int                 分页
                            
`成功返回：`

    orders             array                订货单数据
    gifts              array                订单赠品

    orders字段子集说明（包含分页信息）

    data                    array           订货单数组
    
    data字段子集说明
    
    id                      int             订单号
    amount                  decimal         订单金额
    display_fee             decimal         陈列费
    status                  tinyint         订单状态（0未审核  1已审核）
    order_remark            string          订单备注
    display_remark          string          陈列费备注
   

`失败返回：`

#### 2.6.2 获取所有退货单[get] (return-orders)
`请求参数：`

    customer            string/int          客户名称/单号
    status              int                 状态   （0未审核  1已通过）
    start_date          date                开始时间
    end_date            date                结束时间
    page                int                 分页
                            
`成功返回：`

    orders             array                订货单数据

    orders字段子集说明（包含分页信息）

     id                      int             退货单号
     amount                  decimal         退货单金额
    

#### 2.6.3 获取订单详情[get] (order-detail/{order_id})
`请求参数：`
                            
`成功返回：`

    order             array                订货单数据

    order字段子集说明

    order_goods              array           订货单商品列表
    mortgage                 array           抵费商品 [月份=>商品信息]
    displayFee               array           订单陈列费 [月份 => 费用]
    order                    array           平台订单详情 （含送货人信息）
    gifts                    array           赠品
	promo                    array           促销活动
    
    order_goods字段子集说明
    
    goods_id                int             商品id
    price                   decimal         单价
    num                     int             数量
    pieces                  int             单位id
    amount                  decimal         金额
	goods                   object          商品信息

    goods字段说明

    name                        string          商品名称
    price_retailer              float           终端商价格
    price_wholesaler            float           批发商价格
    pieces_retailer             float           终端商单位
    pieces_wholesaler           float           批发商单位
    goods_pieces                array           商品单位（新， 为null时未设置）

    goods_pieces字段子集介绍

    pieces_level_1              int         一级单位
    pieces_level_2              int         二级单位（为null时未设置）
    pieces_level_3              int         三级单位（为null时未设置）

    mortgage字段子集（商品信息）说明

        id                      int             商品id
        name                    string          商品名
        num                     int             数量
        pieces                  string          单位
        month                   string          月份

    gifts字段子集说明

        id                  int             商品id
        num                 int             个数
        pieces              int             单位

 	promo 字段子集说明
	同2.10.1


#### 2.6.4 订单修改全部（删除后重新增加）[post] (update-all/{order_id})
`请求参数：`

    goods                   array           订单商品列表
    display_fee             array           陈列费用 （当客户display_type 为1时入 ,如 ['2016-10'=> 100, '2016-11' => 100]）
    mortgage                array           抵费商品 （当客户display_type为2时传入，月份=>['id'=>抵费商品id, 'num'=>商品数量] 格式。 如['2016-10'=>['id' => 5, 'num'=>100]]）
    order_remark            string          订单备注
	apply_promo_id			int				促销活动ID
    display_remark          string          陈列费备注
    gifts                   array           赠品

    goods子集详情

    id                      int             商品id
    pieces                  int             商品单位
    price                   decimal         商品单价
    num                     int

     gifts字段子集说明

        id                  int             商品id
        num                 int             个数
        pieces              int             单位

`成功返回：`

`失败返回：`

#### 2.6.5 订单删除[delete] ({order_id})
`请求参数：`

`成功返回：`

`失败返回：`




### 2.7 平台商品 goods
#### 2.7.1 获取所有平台商品[get] (/)
`请求参数：`

      name        	string              商品名
      category_id 	int                 商品分类id  (如110000   最高位数1为层级，后面为分类id)
      page         int                 分页

`成功返回：`

    data                array       商品列表

    data 字段子集说明

    id                          int         商品id
    name                        string      商品名
    price_retailer              decimal     价格（对于终端商）
    price_wholesaler            decimal     价格（对于批发商）
    pieces_retailer             int         单位（对于终端商）
    pieces_wholesaler           int         单位（对于批发商）
    image_url                   string      商品图片
    goods_pieces                array       商品单位（新， 为null时未设置）

    goods_pieces字段子集介绍

    pieces_level_1              int         一级单位
    pieces_level_2              int         二级单位（为null时未设置）
    pieces_level_3              int         三级单位（为null时未设置）

#### 2.7.2 店铺分类[get] (categories)
`请求参数：`

`成功返回：`

    categories       array           店铺分类数组

    categories       子集介绍

        id              int                 分类id
        pid             int                 分类父级id
        name            string              分类名
        level           int                 分类层级
        icon_url        string              分类icon图片
        child           array               分类子级（格式同categories）

### 2.8 赠品 gift
#### 2.8.1 获取平台所有赠品[get] (/)
`请求参数：`

`成功返回：`

      gifts       array           赠品列表

      gifts子集介绍

        id                  int                 赠品id
        name                string              赠品名
        pieces              array               赠品所有单位

### 2.9 资产 asset
####2.9.1 获取店铺所有资产[get] (/)
`请求参数：`

`成功返回：`

    assets               array                厂家资产列表
    assets子集介绍
    id                  int                 资产id
    name                string              资产名称
    quantity            int                 资产数量
    unit                string              资产单位
    condition           string              申请条件
    remark              string              资产备注
  `失败返回：`
####2.9.2 申请资产[post] (/apply)
`请求参数：`

    asset_id            int                 资产ID
    client_id           int                 客户ID
    quantity            int                 申请数量
    apply_remark        string              申请备注
`成功返回：`

`失败返回：`

####2.9.3 资产申请列表 [get] (/apply)
`请求参数:`

	 start_at				date				开始时间
	 end_at					date				结束时间
	 condition				string				客户名称
	 asset					string/int			资产名称或ID
	 status					int	0/1				审核状态 0：未审核，1：通过
	 

`成功返回:`

	applyLists          	array				资产申请列表

	applyLists子集介绍

	id						int					资产申请ID
	asset_id				int					资产编号
	client_id				int					客户ID
	quantity				int					申请数量
	salesman_id				int					业务员ID
	use_date				date				开始使用时间
	apply_remark			string				申请备注
	status					int					审核状态(0:未审核,1:审核通过)
	created_at				date				申请日期
	pass_date				date				通过时间
	client					array				客户信息
	asset					array				资产信息
	salesman				array				业务员信息

	client 子集介绍
	同2.3.1
	asset子集介绍
	同2.9.1
	salesman子集介绍
	同2.1.1


`失败返回:`

#### 2.9.4 添加使用时间 [put] (/use-date/{apply_id})
`请求参数:`

	 date                   date				开始使用时间
`成功返回:`

`失败返回:`


#### 2.9.5  删除申请 [delete] (/apply/{apply_id})
`请求参数:`

`成功返回:`

	
`失败返回:`

### 2.10  促销 promo

#### 2.10.1 获取促销活动列表 [get] (/)
`请求参数:`

`成功返回:`

	promos					array				促销活动列表

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
						
	
`失败返回:`

#### 2.10.2  业务员获取促销申请列表 [get] (/apply)
`请求参数:`
	
	start_at				date				开始时间
	end_at					date				结束时间
	client_name				string				客户名称
	status					int					审核状态(0:未审核,1:通过)

`成功返回:`

	id						int					申请ID
	pass_date				date				通过时间
	status					int					审核状态(0:未审核,1:通过)
	apply_remark			string				申请备注
	created_at 				date				申请时间
	client					array				客户信息
	promo					array				促销信息
	
	client 子集介绍
	同2.3.1
	promo  子集介绍
	同2.10.1
	
`失败返回:`

#### 2.10.3  申请促销 [post] (/apply)
`请求参数:`
	
	promo_id				int					促销ID
	client_id				int					客户ID
	apply_remark			string				申请备注

`成功返回:`

	
`失败返回:`
#### 2.10.4  获取审核通过的促销活动 [get] (/apply/pass)
`请求参数:`

	client_id				int					客户ID

`成功返回:`

	promos					array				审核通过的活动列表

	promos 子集介绍
	同 2.10.1
	增加 
    apply_id		    	int					申请ID
	
`失败返回:`

#### 2.10.5  删除申请 [delete] (/apply/{apply_id})
`请求参数:`

`成功返回:`
	
`失败返回:`
