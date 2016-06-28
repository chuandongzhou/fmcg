# 快销品平台零售商API
## 1. API接口通信规定
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

  salesman      array           当前登录业务员

  salesman字段子集说明

  id                    int             业务员id
  account               string          账号
  shop_id               int             业务员所属店铺id
  name                  string          业务员名
  contact_information   string          联系方式
  avatar_url            string          头像

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

    name                    string          姓名
    contact_information     string          联系方式
    password                string          密码（非必填，不填是不修改密码）

`成功返回：`

`失败返回：`

### 2.3 客户模块salesman-customer
#### 2.3.1 获取所有客户[get] (/)
`请求参数：`

`成功返回：`

    customers               array           客户数据

    customers字段子集说明（包含分页信息）

    data                    array           客户数组

    customers字段子集说明

    id                      int             客户ID
    number                  int             客户号
    name                    string          客户名
    letter                  char            客户名首字母
    shop_id                 int             客户的平台id
    contact                 string          联系人
    contact_information     string          联系方式
    business_area           string          营业面积
    business_address_lng    float           营业地址经度
    business_address_lat    float           营业地址纬度
    shipping_address_lng    float           收货地址经度
    shipping_address_lat    float           收货地址纬度
    display_fee             decimal         陈列费

`失败返回：`

#### 2.3.2 客户编辑[put] (update-by-app)
`请求参数：`

    name                    string          客户名称
    contact                 string          联系人
    contact_information     string          联系方式
    business_area           string          营业面积
    display_fee             decimal         陈列费
    shop_id                 int             客户的平台id
    business_address        array           营业地址
    shipping_address        array           收货地址

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

`成功返回：`

    visit                   array           拜访列表
    
    visit字段子集说明
    
    id                      int             拜访id
    created_at              timestamp       拜访时间
    salesman_customer       array           拜访客户信息
    
    salesman_customer字段子集说明
    
    number                  int             客户号
    name                    string          客户名
    letter                  string          客户名首字母
    contact                 string          联系人
    contact_information     string          联系方式

`失败返回：`

#### 2.5.2 拜访详情 [get] (visit/{visit_id})

`请求参数：`

`成功返回：`

    visit_id                int             拜访id
    customer_name           string          客户名
    contact                 string          联系人
    contact_information     string          联系方式
    display_fee             decimal         订单陈列费
    
    statistics              array           订单商品
    mortgage                array           抵费商品
    
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
    
    mortgage字段子集说明
    
    name                    string          商品名
    num                     int             数量
    pieces                  string          单位
    
 `失败返回：`   
 
 #### 2.5.3 添加拜访 [post] (visit)
`请求参数：`

    salesman_customer_id    int             客户id
    goods                   array           拜访商品列表
    display_fee             decimal         陈列费
    mortgage                array           抵费商品列表
    
    goods字段子集说明
    
    id                      int             商品id
    pieces                  int             单位id
    stock                   string          商品库存
    production_date         date            商品生产日期
    order_form              array           订货数据（要订此商品时传入）
    return_order            array           退货数据（要退此商品时传入）
    
    order_form字段子集说明
     
        price               decimal         商品单价
        num                 int             订货数量
    return_order字段子集说明
    
        amount              decimal         退款金额
        num                 string          退货数量
    
     mortgage字段子集说明
        
        name                    string          商品名
        num                     int             数量
        pieces                  string          单位

`成功返回：`
	
`失败返回：`   	

### 2.6 订单模块order
#### 2.6.1 获取所有订货单[get] (order-forms)
`请求参数：`

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
    orderGoods              array           订货单商品列表
    mortgageGoods           array           陈列商品列表
    
    orderGoods字段子集说明
    
    goods_id                int             商品id
    goodsName               string          商品名
    price                   decimal         单价
    num                     int             数量
    pieces                  int             单位id
    amount                  decimal         金额
    
    mortgageGoods字段子集说明
    
    goodsName               string          商品名
    num                     int             数量
    pieces                  int             单位id

`失败返回：`

#### 2.6.2 获取所有退货单[get] (return-orders)
`请求参数：`

    page                int                 分页
                            
`成功返回：`

    orders             array                订货单数据

    orders字段子集说明（包含分页信息）

     id                      int             退货单号
     amount                  decimal         退货单金额
     orderGoods              array           退货单商品列表
     
      orderGoods字段子集说明
         
         goods_id                int             商品id
         goodsName               string          商品名
         price                   decimal         单价
         num                     int             数量
         pieces                  int             单位id
         amount                  decimal         金额