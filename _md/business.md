#快销品平台零售商API
##1. API接口通信规定
-接口采用**HTTP**,**POST**协议
-请求URL **192.168.2.66/api/v1/business/**
-请求返回数据格式
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
      shop_id               int             业务员所属店铺id
      name                  string          业务员名
      contact_information   string          联系方式
      avatar_url            string          头像
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
    contact_information     string          联系方式
    business_area           string          营业面积
    account                 string          客户的平台账号(选填)
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

#### 2.3.3 客户添加[post] (/)
`请求参数：`

    name                    string          客户名称
    contact                 string          联系人
    contact_information     string          联系方式
    business_area           string          营业面积
    account                 string          客户的平台账号(选填)
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
#### 2.3.7 客户陈列费发放情况查询[post] (customer-display-fee)
`请求参数：`

	salesman_customer_id        int         客户id
	start_at                    date        开始时间
	end_at                      date        结束时间

`成功返回：`

	orders                    array         所有订单
	display_fee               decimal       协议陈列费

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
    display_fee             array           订单陈列费 [月份 => 费用]
    
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


`成功返回：`
	
`失败返回：` 

#### 2.5.4 判断是否可以添加拜访 [get] (can-add/{customer_id})
`请求参数：`

`成功返回：`
	visit               array            该字段为空表示可以添加
	
`失败返回：`

#### 2.5.5 根据月份获取客户剩余陈列商品 [get] (surplus-mortgage-goods)
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


#### 2.5.6 根据月份获取剩余陈列费 [get] (surplus-display-fee)
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

    status              int                 状态   （0未审核  1已通过）
    start_date          date                开始时间
    end_date            date                结束时间
    page                int                 分页
                            
`成功返回：`

    orders             array                订货单数据

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
    
    order_goods字段子集说明
    
    goods_id                int             商品id
    price                   decimal         单价
    num                     int             数量
    pieces                  int             单位id
    amount                  decimal         金额
	goods                   object          商品信息

    goods字段说明

    name               string          商品名称

    mortgage字段子集（商品信息）说明

        id                      int             商品id
        name                    string          商品名
        num                     int             数量
        pieces                  string          单位
        month                   string          月份


#### 2.6.4 订单修改全部（删除后重新增加）[post] (update-all/{order_id})
`请求参数：`

    goods                   array           订单商品列表
    display_fee             array           陈列费用（当客户display_type 为1时入 ,如 ['2016-10'=> 100, '2016-11' => 100]）
    mortgage                array           抵费商品 （当客户display_type为2时传入，月份=>['id'=>抵费商品id, 'num'=>商品数量] 格式。 如['2016-10'=>['id' => 5, 'num'=>100]]）
    order_remark            string          订单备注
    display_remark          string          陈列费备注

    goods子集详情

    id                      int             商品id
    pieces                  int             商品单位
    price                   decimal         商品单价
    num                     int             商品购买数量

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

    id                  int         商品id
    name                string      商品名
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    pieces_retailer     int         单位（对于终端商）
    pieces_wholesaler   int         单位（对于批发商）
    image_url           string      商品图片

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

