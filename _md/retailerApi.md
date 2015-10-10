# 快销品平台零售商API
## 1. API接口通信规定
- 接口采用 **HTTP**, **POST** 协议
- 请求URL **192.168.2.66/api/v1**
- **请求需要登录的API请在请求中附带cookie请求，以便进行验证处理**
- 数据请求必须参数
	- *time* 发送请求时间戳
	- *g* 项目标志 (本项目此参数通用为**ApiEducation**)
	- *m* 模块标志
	- *c* 方法标志
	- ……  其他请求数据请同级追加
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
	type      int       登录角色(1零售，2批发 ，3供应)
`成功返回:`
	　
`失败返回：`

	
#### 2.1.2 注销[get] (logout)
`请求参数：`

	　
`接口返回：`

### 2.2 商品模块 goods
#### 2.2.1 热门商品[get/post] (hot-goods)
`请求参数`

    page    int     分页

`成功返回：`

    data    array   商品信息

    data字段子集说明

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

    data    array   商品信息

    data字段子集说明

    id                  int         商品id
    name                string      商品名
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_promotion        int         是否促销产品
    image_url           string      商品图片

`失败返回：`


#### 2.2.3 商品详情[post] (detail/{goods_id})
`请求参数`


`成功返回：`

    data    array   商品信息

    data字段子集说明

    id                  int         商品id
    name                string      商品名
    sales_volume        int         销售量
    price_retailer      decimal     价格（对于终端商）
    min_num_retailer    int         最低购买量 (对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    min_num_wholesaler  int         最低购买量 (对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_out              int         是否换货
    is_change           int         是否可换货
    is_back             int         是否可退货
    is_expire           int         是否即期品
    is_promotion        int         是否促销产品
    promotion_info      string      促销信息
    introduce           string      商品图文详情
    shop_id             int         商品所属店铺id
    attrs               array       标签
    images              array       商品图片
    delivery_area       array       商品配送区域
    is_like             bool        是否已关注
    image_url           string      商品图片

    attrs 子字段说明

    $key => $value形式    $key表示标签名， $value 表示标签值

    images 子字段说明

    path                string      路径

`失败返回：`




### 2.3 分类 categories
#### 2.3.1 获取所有分类[post] (all)
`请求参数：`

`成功返回：`

    id                  int         分类id
    icon_pic_url        string      分类图标
    pid                 int         父级id
    name                string      分类名
    child               array       子级分类（子级分类返回数据与当前数据相同）

`失败返回：`

#### 2.3.2 获取标签[get] ({categoryId}/attrs)
`请求参数：`

    format              bool        是否格式化标签  (非必须参数)

`成功返回：`

   attr_id              int         标签id
   name                 string      标签名
   pid                  int         父级id
   child                array       子级标签 （仅当format为true时返回， 子级分类返回数据与当前数据相同）

`失败返回：`

### 2.4 购物车 cart
#### 2.4.1 查看购物车[post] (index)
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

            cart_goods子字段说明

            id                  int         商品id
            name                string      商品名
            price_retailer      decimal     价格（对于终端商）
            price_wholesaler    decimal     价格（对于批发商）

`失败返回：`



#### 2.4.2 加入购物车[post] (add/{goodsId})
`请求参数：`

    num                 int         购买数量

`成功返回：`

`失败返回`

#### 2.4.3 删除购物车[delete] (delete/{cartId})
`请求参数：`


`成功返回：`

`失败返回`

### 2.5 订单 order
#### 2.5.1 订单订单[post] (confirm-order)
`请求参数：`

    num                 array       购买的商品和数量 （key=>value形式    key为商品id   value 为购买数量）


`成功返回：`

`失败返回`

#### 2.5.2 获取已确认但未提交订单信息[get] (confirm-order)
`请求参数：`


`成功返回：`

     shops                array       商店列表
     shippingAddress      array       收货地址列表

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

     shippingAddress    子字段说明

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

#### 2.5.3 提交订单[get] (confirm-order)
`请求参数：`

    shop                array       商店

    shop 子字段说明（key=>value）  key为商店id

    shipping_address_id int         收货地址id
    pay_type            string      支付方式 （online 在线 ， cod 货到付款）
    remark              string      订单备注信息

`成功返回：`

`失败返回`


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






