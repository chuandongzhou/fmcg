# 快销品平台零售商API
## 1. API接口通信规定
- 接口采用 **HTTP**, **POST** 协议
- 请求URL **192.168.2.66/api/v1**
- 附加请求参数 **ter_type**: **web端传1**, **ios端传2**, **android端传3**
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

	account     string      账号
	password    string      密码
	type        int         用户类型 (1是终端   2是批发    3是供应)
	token       array       第三方token数据 （可选,有的话为绑定第三方token并登录）
	
	token  字段子集介绍
	
	    token       string          第三方token字符串
	    type        string          第三方类型（微信默认传weixin）
	
`成功返回:`

	id          int             用户id
	user_name   string          登录名
	type        int             用户类型 (1是终端   2是批发    3是供应)
	shop        array           商店

	shop 字段子集说明

	id                  int            商店id
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

#### 2.1.3 注册（获取验证码）[post] (reg-send-sms)

`请求参数：`

    type                        int         用户类型 （1终端商 2 批发商  3 供应商）
    user_name                   string      账号
    backup_mobile               string      密保手机
 
`接口返回：`
    
    

#### 2.1.4 注册（创建帐户）[post] (register-user)

`请求参数：`
    
    type                        int         用户类型 （1终端商 2 批发商  3 供应商）
    user_name                   string      账号
    backup_mobile               string      密保手机
    code                        string      验证码
    
`接口返回：`   

#### 2.1.5 注册（设置密码）[post] (set-password)

`请求参数：`
    
    type                        int         用户类型 （1终端商 2 批发商  3 供应商）
    user_name                   string      账号
    backup_mobile               string      密保手机
    password                    string      密码
    password_confirmation       string      确认密码
    
`接口返回：` 

#### 2.1.6 注册（填写商户信息）[post] (register)

`请求参数：`

	user_name                   string      账号
	password                    string      密码
	password_confirmation       string      确认密码
    type                        int         用户类型 （1终端商 2 批发商  3 供应商）
    name                        string      店铺名
    contact_person              string      联系人
    contact_info                string      联系方式
    backup_mobile               string      密保手机
    spreading_code              string      推广码
    license                     file        营业执照
    license_num                 string      营业执照编号
    business_license            file        食品经营许可证
    agency_contract             file        代理合同
    address                     array       店铺地址
    area                        array       配送区域列表 (仅批发商和供应商上传)
    x_lng                       float       经度
    y_lat                       float       纬度

     address 字段子集介绍

        id                      int             地址id
        province_id             int             省id
        city_id                 int             市id
        district_id             int             县id
        street_id               int             街道id
        area_name               string          省、市、县、街道名
        address                 string          详细地址

     area 字段说明

         id                     array               配送地址id列表(新添加地址id应为 '')
         province_id            array               省id列表
         city_id                array               市id列表
         district_id            array               县id列表
         street_id              array               街道id列表
         area_name              array               省名+市名+县名+街道名  列表
         address                array               详细地址列表
         min_money              array               最低配送额列表

`成功返回:`

`失败返回：`

#### 2.1.7 找回密码（获取验证码）[post] (send-sms)

`请求参数：`

    user_name                   string      账号
    backup_mobile               string      密保手机
    license_num                 string      营业执照注册号
 
`接口返回：`
    
#### 2.1.8 找回密码（确认找回）[post] (backup)

`请求参数：`

    user_name                   string      账号
    backup_mobile               string      密保手机
    license_num                 string      营业执照注册号
    password                    string      新密码
    password_confirmation       string      新密码确认
    code                        string      手机验证码
 
`接口返回：`

#### 2.1.9 获取是否已绑定平台账号（已绑定直接登录）[post] (user-token)
`请求参数：`

    token                       string      第三方token
    type                        string      应用类型  （微信默认传 'weixin'）
    
`接口返回：`
    
    返回空字典为未绑定
    返回user 对象为已绑定并已登录
    
#### 2.1.10 注册并绑定第三方token[post] (bind-socialite)

`请求参数：`

    password                    string      密码
    password_confirmation       string      确认密码
    type                        int         用户类型 （1终端商 2 批发商  3 供应商）
    name                        string      店铺名
    contact_person              string      联系人
    backup_mobile               string      密保手机
    license                     file        营业执照
    license_num                 string      营业执照编号
    business_license            file        食品经营许可证
    agency_contract             file        代理合同
    address                     array       店铺地址
    area                        array       配送区域列表 (仅批发商和供应商上传)
    x_lng                       float       经度
    y_lat                       float       纬度
    code                        string      验证码
    token                       array       第三方token数据

    address 字段子集介绍

        id                      int             地址id
        province_id             int             省id
        city_id                 int             市id
        district_id             int             县id
        street_id               int             街道id
        area_name               string          省、市、县、街道名
        address                 string          详细地址

    area 字段说明

        id                     array               配送地址id列表(新添加地址id应为 '')
        province_id            array               省id列表
        city_id                array               市id列表
        district_id            array               县id列表
        street_id              array               街道id列表
        area_name              array               省名+市名+县名+街道名  列表
        address                array               详细地址列表
        min_money              array               最低配送额列表
         
    token  字段子集介绍
     	
        token       string          第三方token字符串
        type        string          第三方类型（微信默认传weixin）

`成功返回:`

`失败返回：`


### 2.2 商品模块 goods
#### 2.2.1 获取商品栏目[get] (goods)
`请求参数`

`成功返回：`

    goodsColumns    array   商品栏目信息

    goodsColumns字段子集说明

    id                  int         分类id
    level               int         分类层级
    name                string      分类名
    goods               array       栏目商品列表

    goods字段子集说明

    id                  int         商品id
    name                string      商品名
    bar_code            string      条形码
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    min_num_retailer    int         最低购买数（对于终端商）
    min_num_wholesaler  int         最低购买数（对于批发商）
    is_new              int         是否新品（1是 , 0不是）
    is_promotion        int         是否促销产品
    image_url           string      商品图片

`失败返回：`




#### 2.2.2 商品搜索[post] (search)
`请求参数`

    sort        	string              排序 （name , price , new）
    district_id 	int                 县id
    street_id   	int                 街道id
    name        	string              商品名
    category_id 	int                 商品分类id  (如110000   最高位数1为层级，后面为分类id)
    attr        	array               标签数组
    page        	int                 分页

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

    id                          int         商品id
    name                        string      商品名
    sales_volume                int         销售量
    price_retailer              decimal     价格（对于终端商）
    price_retailer_pick_up      decimal     自提价格（对于终端商）
    min_num_retailer            int         最低购买量 (对于终端商）
    pieces_retailer             int         单位编号 （对于终端商  0盒  1瓶 2箱 3听 4条 5袋  6罐  7包）
    specification_retailer      string      规格 （对于终端商）
    price_wholesaler            decimal     价格（对于批发商）
    price_wholesaler_pick_up    decimal     自提价格（对于批发商）
    min_num_wholesaler          int         最低购买量 (对于批发商）
    pieces_wholesaler           int         单位编号 （对于批发商）
    specification_wholesaler    string      规格 （对于批发商）
    pieces                      string      单位名 （根据不同登录角色区分）
    shelf_life                  string      保质期
    bar_code                    string      商品条形码
    is_new                      int         是否新品（1是 , 0不是）
    is_out                      int         是否缺货
    is_change                   int         是否可换货
    is_back                     int         是否可退货
    is_expire                   int         是否即期品
    is_promotion                int         是否促销产品
    promotion_info              string      促销信息
    introduce                   string      商品图文详情
    shop_id                     int         商品所属店铺id
    shop_name                   string      商品所属店铺
    attrs                       array       标签
    delivery_area               array       商品配送区域
    is_like                     bool        是否已关注
    image_url                   string      商品图片('第一张')
    images_url                  array       商品全部图片

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

#### 2.2.4 商品单位[get] (pieces)
`请求参数`


`成功返回：`

    'pieces'                    array           商品单位数组
    
#### 2.2.5 获取常购商品[get] (always-buy)    
`请求参数:`

    sort            string          排序（传num为购买数量，不传或传其它均为最近购买）
    name            string          商品名

`成功返回：`

    goods    array   商品信息

    goods字段子集说明
    
    id                  int         商品id
    name                string      商品名
    price_retailer      decimal     价格（对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    pieces_retailer     int         单位（对于终端商）
    pieces_wholesaler   int         单位（对于批发商）
    image_url           string      图片地址
    price               decimal     最终价格
    pieces_id           int         最终单位
    
`失败返回：`    

### 2.3 我的商品 my-goods

#### 2.3.1获取我的商品列表 [get] ()
`请求参数：`

    province_id int                 省id
    city_id     int                 市id
    district_id int                 县id
    street_id   int                 街道id
    name        string              商品名
    category_id int                 商品分类id  (如110000   最高位数1为层级，后面为分类id)
    attr        array               标签数组
    status      int                 上下架（1 搜索上架商品  0搜索下架商品）
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
    like_amount         int         商品收藏数

    categories 字段子集说明

    id                  int         分类id
    name                string      分类名
    level               int         分类层级
    pid                 int         父级id
    icon_url            string      图标地址

`失败返回：`

#### 2.3.2 我的商品详情[get] ({goods_id})
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
    pieces_retailer     int         单位编号 （对于终端商）
    specification_retailer  string  规格 （对于终端商）
    price_wholesaler    decimal     价格（对于批发商）
    min_num_wholesaler  int         最低购买量 (对于批发商）
    pieces_wholesaler   int         单位编号 （对于批发商）
    specification_wholesaler  string  规格 （对于批发商）
    pieces              string      单位名 （根据不同登录角色区分）
    shelf_life          string      保质期
    bar_code            string      商品条形码
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
    goodsPieces         array       商品规格单位

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

    id                  int         图片id
    name                string      图片名
    path                string      路径

    goodsPieces子字段说明

    pieces_level_1              int         一级单位
    pieces_level_2              int         二级单位
    system_1                    int         一级与二级之间进制（有二级单位时必填）
    pieces_level_3              int         三级单位
    system_2                    int         二级三级之间进制（有三级单位时必填）
    specification               string      最小单位规格

`失败返回：`


#### 2.3.3 添加我的商品 [post] ()
`请求参数：`

    cate_level_1                int         第一层分类
    cate_level_2                int         第二层分类
    cate_level_3                int         第三层分类
    bar_code                    string      商品条形码
    name                        string      商品名
    pieces_level_1              int         一级单位
    pieces_level_2              int         二级单位
    system_1                    int         一级与二级之间进制（有二级单位时必填）
    pieces_level_3              int         三级单位
    system_2                    int         二级三级之间进制（有三级单位时必填）
    specification               string      最小单位规格
    
    price_retailer              decimal     价格 （对于终端商）
    price_retailer_pick_up      decimal     自提价格（对于终端商）
    min_num_retailer            int         最低购买量 （对于终端商）
    pieces_retailer             int         单位编号 （对于终端商）
    specification_retailer      string      规格  （对于终端商）
    price_wholesaler            decimal     价格 （对于批发商  供应商时添加）
    price_wholesaler_pick_up     decimal    自提价格（对于批发商 供应商时添加）
    min_num_wholesaler          int         最低购买量 （对于批发商  供应商时添加）
    pieces_wholesaler           int         单位编号 （对于批发商  供应商时添加）
    specification_wholesaler    string      规格  （对于批发商 供应商时添加）
    shelf_life                  string      保质期
    is_new                      int         是否新品（1是   0不是）
    is_out                      int         是否缺货 （1是   0不是）
    is_change                   int         是否可换货 （1是   0不是）
    is_back                     int         是否可退货 （1是   0不是）
    is_expire                   int         是否即将过期 （1是   0不是）
    is_promotion                int         是否促销    （1是   0不是）
    promotion_info              string      促销信息    （取当是促销时传入）
    cate_level_1                int         第一层分类
    cate_level_2                int         第二层分类
    cate_level_3                int         第三层分类
    attrs                       array       标签数组 （数组下标为此标签父类id）
    area                        array       商品配送区域数组
    introduce                   string      商品图文介绍

    area 字段子集介绍

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址

    attrs 字段子集介绍

    attr_pid => attr_id


`成功返回：`

`失败返回：`

#### 2.3.4 更新我的商品 [put] ({goods_id})
`请求参数（同添加商品）：`

  

`成功返回：`

`失败返回：`

#### 2.3.5  商品上下架 [put] (shelve)
`请求参数：`

    id                      int         商品id
    status                  int         上下架状态 {1 上架   0下架}

`成功返回：`

#### 2.3.6 删除我的商品 [delete] ({goods_id})
`请求参数：`

`成功返回：`



#### 2.3.7 获取商品图库 [get] （images）
`请求参数：`

    bar_code            string      商品条形码

`成功返回：`

    goodsImages         array       商品图片列表

    id                  int         图库id
    image_url           string      图片地址

`失败返回：`


### 2.4 店铺模块 shop

#### 2.4.1 获取商店栏目[get] shops
`请求参数：`


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
    image_url           string      店铺图片

#### 2.4.2 根据距离排序获取所有店铺[get] all
`请求参数：`

    name                string      店铺名（可空）
    page                int         页码
    x_lng               float       纬度
    y_lat               float       经度

`成功返回：`

    distance            float       距离 （米）
    id                  int         店铺id
    name                string      店铺名
    min_money           decimal     最低配送额
    contact_person      string      联系人
    contact_info        string      联系方式
    logo_url            string      logo地址
    shop_address        array       店铺地址
    x_lng               float       纬度
    y_lat               float       经度
    user                array       用户

    shop_address 字段子集说明

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址

    user 字段子集说明

    type                    int             店铺所属用户类型

`失败返回：`

#### 2.4.3 店铺详情[get]   ({shop_id})
`请求参数：`

`成功返回：`

    id                  int         店铺id
    name                string      店铺名
    contact_person      string      联系人
    contact_info        string      联系方式
    introduction        string      店铺介绍
    min_money           decimal     最低配送额
    is_like             bool        是否已收藏
    goods_count         int         商品总数
    user_type           int         店铺类型
    sales_volume        int         销售总量
    delivery_area       array       店铺配送区域

    delivery_area 字段说明

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址


#### 2.4.4 店铺商品[get]   ({shop_id}/goods)
`请求参数：`

      province_id 	int                 省id
      city_id     	int                 市id
      district_id 	int                 县id
      street_id   	int                 街道id
      name        	string              商品名
      category_id 	int                 商品分类id  (如110000   最高位数1为层级，后面为分类id)
      attr        	array               标签数组
      page         int                 分页

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
    image_url           string      商品图片



#### 2.4.5 店铺扩展信息[get]   ({shop_id}/extend)
`请求参数：`

`成功返回：`

    license_url             string          营业执照地址
    business_license_url    string          店铺经营许可证地址
    agency_contract_url     string          代理合同地址
    images_url              array           店铺图片地址
    delivery_area           array           配送区域列表
    address                 array           店铺地址

    images_url 字段子集介绍

    name                string      图片名
    path                string      图片路径
    id                  int         图片id
    url                 string      图片详细路径

    delivery_area 字段子集介绍

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址

     address 字段子集介绍

    id                      int             地址id
    province_id             int             省id
    city_id                 int             市id
    district_id             int             县id
    street_id               int             街道id
    area_name               string          省、市、县、街道名
    address                 string          详细地址


#### 2.4.6 店铺修改[put] (personal/shop/{shop_id})
`请求参数：`

    logo                    string/file         店铺logo
    name                    string              店铺名
    contact_person          string              联系人
    contact_info            string              联系方式
    min_money               decimal             最低配送额
    introduction            string              店铺介绍
    license                 string/file         营业执照
    business_license        string/file         经营许可证
    agency_contract         string/file         代理合同
    images                  array               商品图片列表(店铺已拥有)
    mobile_images           array               新增图片数组
    address                 array               地址
    x_lng                   float               经度
    y_lat                   float               纬度
    >area                    array               配送区域列表

    images  字段说明

    id                      array               图片id列表 (新添加图片id应为 '')
    path                    array               图片地址列表
    name                    array               图片名列表

    address 字段说明

    province_id             int                 省id
    city_id                 int                 市id
    district_id             int                 县id
    street_id               int                 街道id
    address                 string              详细地址

    >area 字段说明

    >id                      array               配送地址id列表(新添加地址id应为 '')
    >province_id             array               省id列表
    >city_id                 array               市id列表
    >district_id             array               县id列表
    >street_id               array               街道id列表
    >area_name               array               省名+市名+县名+街道名  列表
    >address                 array               详细地址列表

`成功返回：`

`失败返回：`

#### 2.4.7 店铺分类[get] ({shop_id}/category)
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


#### 2.4.8 店铺广告[get] ({shop_id}/adverts)
`请求参数：`

`成功返回：`

   		adverts         array                店铺广告列表 （为空时默认图片地址：http://dingbaida.com/images/default-shop-image.jpg）

   		adverts       子集介绍

        id              int                 广告id
        name            string              广告名
        image_url       string              广告图片地址
        goods_id        int                 广告所属商品id
		type            int                 (5:商品广告，6:促销信息)
		url				string              商品广告时是链接地址，促销信息时是促销信息类容

#### 2.4.9 店铺业务员[get] (/salesman)
`请求参数：`
	
	salesman_name			string				业务员名称(非必须)

`成功返回：`
		
	id						int					业务员ID
    account					string				账号
    shop_id					int					所属店铺ID
    name					string				名称
    contact_information		int					电话
    status					int					状态
    maker_id				int/null			所属厂家
    expire_at				time/null			过期时间
    avatar_url				string				头像地址


### 2.5 分类 categories
#### 2.5.1 获取所有分类[post] (all)
`请求参数：`

`成功返回：`

    id                  int         分类id
    icon_url            string      分类图标
    pid                 int         父级id
    name                string      分类名
    level               int         分类层级
    child               array       子级分类（子级分类返回数据与当前数据相同）

`失败返回：`

#### 2.5.2 获取标签[get] ({categoryId}/attrs)
`请求参数：`

    format              bool        是否格式化标签  (非必须参数)

`成功返回：`

    attr_id              int         标签id
    name                 string      标签名
    pid                  int         父级id
    child                array       子级标签 （仅当format为true时返回， 子级分类返回数据与当前数据相同）

`失败返回：`

### 2.6 购物车 cart
#### 2.6.1 查看购物车[get] (index)
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

#### 2.6.2 加入购物车[post] (add/{goodsId})
`请求参数：`

    num                 int         购买数量

`成功返回：`

`失败返回`

#### 2.6.3 删除购物车[delete] (delete/{cartId})
`请求参数：`


`成功返回：`

`失败返回`

### 2.7 订单 order
#### 2.7.1 确认订单[post] (confirm-order)
`请求参数：`

    num                 array       购买的商品和数量 （key=>value形式    key为商品id   value 为购买数量）


`成功返回：`

`失败返回`

#### 2.7.2 获取已确认但未提交订单信息[get] (confirm-order)
`请求参数：`


`成功返回：`

     shops                array       商店列表
	 pay_type			  array		  支付类型
	 pay_way		      array		  支付方式

    shops 子字段说明

    name                string      商店名
    id                  int         商店id
    min_money           decimal     最低配送额
    cart_goods          array       属于本商店的商品
    coupons             array       当前店铺可用的优惠券

        cart_goods子字段说明

        id                  int         购物车id
        goods_id            int         商品id
        user_id             int         用户id
        is_like             bool        是否已收藏
        images              string      商品图片
        goods               array       商品详情

        goods子字段说明

        id                          int         商品id
        name                        string      商品名
        price_retailer              decimal     价格（对于终端商）
        price_retailer_pick_up      decimal     自提价格（对于终端商）
        price_wholesaler            decimal     价格（对于批发商）
        price_wholesaler_pick_up    decimal     自提价格（对于批发商）
        
        coupons 子字段说明
        
        id                  int         优惠券id
        full                decimal     订单满多少金额
        discount            decimal     订单优惠金额



`失败返回`

#### 2.7.3 提交订单[post] (submit-order)
`请求参数：`

    shipping_address_id int         收货地址id(仅当为送货订单时传入)
    pay_type            string      支付类型 （仅当为送货订单时传入 ： online 在线 ， cod 货到付款）
    pay_way             string      付款方式 （仅当pay_type为cod时传入 ：传入'cash'为现金  传入'card'为刷卡）
    shop                array       商店

    shop 子字段说明（key=>value）  key为商店id

    remark              string      订单备注信息
    coupon_id           int         优惠券id

`成功返回：`

    pay_type            string      支付方式 （online 在线 ， cod 货到付款）
    order_id            int         订单id  (订单为在线付款时返回)
    type                string      订单id类型  （为all是该订单id属于总订单）



`失败返回`


#### 2.7.4 买家获取订单列表[get] (list-of-buy)
`请求参数：`

    page                int         分页

`成功返回：`

	data                array       订单信息

	data 字段子集说明
	
	id					int			订单ID号
	price               string      订单总金额
	after_rebates_price decimal     订单优惠后金额
	status_name			string		订单显示状态
	payment_type		string      支付方式(如:在线支付;货到付款)
	pay_way             string      付款方式 （1为现金  2为刷卡 ）
	pay_type			int			支付方式(1:在线支付;2:货到付款;3:自提)
	pay_status			int			支付状态(0:未付款;1:已付款)
	status				int			订单状态(1:未发货;2:已发货;3:完成)
	is_cancel			int			订单是否被取消(1取消,0未取消)
	can_cancel			bool		是否可取消(是true,否false)
	can_payment			bool		是否可在线支付
	can_confirm_arrived bool		是否可确认收货(针对在线支付)
	type                int         订单类型（0自主订单， 1业务订单）
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

#### 2.7.5 待付款订单列表[get] (non-payment)(仅显示在线支付订单)
`请求参数：`

	page 				int			分页

`成功返回：`
	
	返回信息同上

`失败返回`

#### 2.7.6 买家待收货订单列表[get] (non-arrived)
`请求参数：`

	page 				int			分页

`成功返回：`
	
	返回信息同上

`失败返回`

#### 2.7.7 买家批量确认订单完成[put] (batch-finish-of-buy)(仅针对在线支付订单)
`请求参数：`

	order_id  				array		订单id

`成功返回：`


`失败返回：`

#### 2.7.8 买家获取订单详情[get] (detail-of-buy)(仅发货后和完成后才能查看)
`请求参数：`

	order_id			int			订单号

`成功返回：`

	id					int			订单ID号
	price               string      订单总金额
	display_fee         decimal     陈列费
	status_name			string		订单显示状态
	payment_type		string      支付方式
	is_cancel			int			订单是否被取消(1取消,0未取消)
	remark				string		订单备注信息
	apply_promo_id		int			促销活动申请ID
	created_at			string		创建时间
	paid_at				string		支付时间
	send_at				string		发货时间
	finished_at			string		完成时间
	refund_at           string      退款时间
	can_cancel			bool		是否可取消(是true,否false)
	can_payment			bool		是否可在线支付
	can_confirm_arrived bool		是否可确认收货(针对在线支付)
	trade_no            string      付款成功时交易流水号
	order_refund        array       退款详情
	delivery_man		array		送货人信息(仅发货后才有,否则为Null)
	shipping_address    array       收货信息
    mortgageGoods    	array		抵费商品详细信息
    orderGoods    	    array		订单商品详细信息 （格式同mortgageGoods）
	gifts				array		赠品
	promo				array		参加的促销活动

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

	refund_reason 字段子集说明（有退款时返回）

    reason              string      退款原因
    time                timestamp   申请退款时间
    
    invalid_reason 字段子集说明（作废时返回）
    
    reason              string      退款原因
    time                timestamp   申请退款时间

`失败返回`


#### 2.7.9 卖家获取订单列表[get] (list-of-sell)(不显示已取消订单)
`请求参数：`

    status                  string         订单状态 （见返回的status列表）
    start_at                timestamp       开始时间
    end_at                  timestamp       结束时间
    search_content          string/int      订单号或买家店铺名
    page               		int            分页

`成功返回：`

	orders               		 array       订单信息
	status               		 array       订单筛选状态列表

	orders 字段子集说明
	
	id						 	int			订单ID号
	price              		 	string      订单总金额
	after_rebates_price         decimal     订单优惠后金额
	status_name					string		订单显示状态
	apply_promo_id				int			促销活动申请ID
	payment_type				string      支付方式(如:在线支付;货到付款)
	pay_way                     string      付款方式 （1为现金  2为刷卡）
	pay_type					int			支付方式(1:在线支付;2:货到付款)
	pay_status					int			支付状态(0未付款，1付款成功，2付款失败，3退款中，4退款成功)
	status						int			订单状态(1:未发货;2:已发货;3:完成)
	is_cancel					int			订单是否被取消(1取消,0未取消)
	can_cancel					bool		是否可取消(是true,否false)
	can_payment					bool		是否可支付订单(是true,否false)
	can_confirm					bool		是否可确认订单(是true,否false)
	can_send					bool		是否可发货
	can_confirm_collections 	bool		是否可确认收款(针对货到付款)
	user                        array       买家信息
    mortgageGoods    			array		抵费商品信息
    orderGoods    			    array		订单商品信息
    user_shop_name              string      买家店铺名
	gifts						array		赠品
	promo						array		参加的促销活动

	user 字段子集说明
		
	    shop                array       买家店铺

	    shop 字段子集说明

	    name                string      店铺名
	
	
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


#### 2.7.10 卖家待发货订单列表[get] (non-send)
`请求参数：`

	page                int         分页

`成功返回：`

	返回信息同上

`失败返回：`


#### 2.7.10.1 买家待发货订单列表[get] (unsent)
`请求参数：`

	page                int         分页

`成功返回：`

	返回信息同上

`失败返回：`


#### 2.7.11 卖家待收款订单列表[get] (pending-collection)(仅针对货到付款订单)
`请求参数：`

	page                int         分页

`成功返回：`

	返回信息同上

`失败返回：`

#### 2.7.12 卖家确认订单[put] (order-confirm/{order_id}) (确认后不可取消，不可退款)
`请求参数：`


`成功返回：`

	返回信息同上

`失败返回：


#### 2.7.13 卖家获取订单详情[get] (detail-of-sell)
`请求参数：`

	order_id  				int			订单id

`成功返回：`

	id						int			订单ID号
	price               	string      订单总金额
	display_fee             decimal     陈列费
	status_name				string		订单显示状态
	payment_type			string      支付方式(显示)
	pay_type				int			支付方式
	pay_way                 string      付款方式 （'cash'为现金  传入'card'为刷卡）
	is_cancel				int			订单是否被取消(1取消,0未取消)
	remark					string		订单备注信息
	created_at				string		创建时间
	paid_at					string		支付时间
	send_at					string		发货时间
	refund_at               string      退款时间
	finished_at				string		完成时间
	can_cancel				bool		是否可取消(是true,否false)
	can_send				bool		是否可发货
	can_confirm_collections bool		是否可确认收款(针对货到付款)
	trade_no                string      付款成功时交易流水号
	order_refund            array       退款详情
	shipping_address    	array       收货信息
    mortgageGoods    		array		抵费商品详细信息
    orderGoods    	    	array		订单商品详细信息 （格式同mortgageGoods）
    orderChangeRecode       array       订单修改记录


	delivery_man 字段子集说明

	name     			string		送货人姓名
	phone				string		送货人电话

	shipping_address 字段子集说明

	consigner			string		收货人姓名
	phone				string		收货人电话
	address				array		收货地址信息
	x_lng               float       经度
	y_lat               float       纬度
		
    address 字段子集说明

    province_id         int         省id
    city_id             int         市id
    district_id         int         县id
    street              int         街道id
    address				string		详细地址

    refund_reason 字段子集说明（有退款时返回）
    
    reason              string      退款原因
    time                timestamp   申请退款时间
    
    invalid_reason 字段子集说明（作废时返回）
    
    reason              string      退款原因
    time                timestamp   申请退款时间

    orderChangeRecode 字段子集说明

    content             string      修改内容
    created_at          timestamp   修改时间

`失败返回：`


#### 2.7.14 卖家批量确认订单完成[put] (batch-finish-of-sell)(仅针对货到付款订单)
`请求参数：`

	order_id  				array		订单id
	
`成功返回：`


`失败返回：`


#### 2.7.15 卖家批量发货[put] (batch-send)
`请求参数：`

	order_id  				array		订单id
	delivery_man_id			array		配送员id数组
	
`成功返回：`


`失败返回：`

#### 2.7.16 买家/卖家批量取消订单[put] (cancel-sure)
`请求参数：`

	order_id  				array		订单id

`成功返回：`


`失败返回：`


#### 2.7.17 卖家修改订单[put] (change-order)
`请求参数：`

	order_id  				int		订单id
	price  					int		商品单价
	num                     int     商品数量
	pivot_id  				int		商品在order_goods表中的id

`成功返回：`


`失败返回：`

#### 2.7.18 卖家订单统计[get] (statistics)
`请求参数：`

`成功返回：`
	
	all						array		累计信息统计
	today					array		今日信息统计
	sevenDay				array		过去7天信息统计

	all 字段说明

	count					int			累计订单数
	finish					int			累计完成订单数
	
	today 字段说明
	
	new						int			今日新增订单数
	finish					int			今日完成订单数
	
	sevenDay 字段子集说明
	
		new					int			当日新增订单数(按今天前第六天开始倒序排列的)
		finish				int			当然完成订单数

`失败返回：`

#### 2.7.19 买家获取待确认订单[get] (wait-confirm-by-user)
`请求参数：`

`成功返回：`
    见 2.7.4

`失败返回：`

#### 2.7.20 卖家获取待确认订单[get] (wait-confirm-by-seller)
`请求参数：`

`成功返回：`
    见 2.7.4

`失败返回：`

#### 2.7.21 根据收货地址获取店铺配送额[get] (min-money)
`请求参数：`

    shipping_address_id             int                 收货地址id
    shop_id                         array               要获取的店铺id数组

`成功返回：`
   
    shopMinMoney                    array               最低配送额列表
    
    shopMinMoney字段子集
    
    shop_id                         int                 店铺id
    min_money                       decimal             最低配送额

`失败返回：`

#### 2.7.22 卖家订单商品删除[delete] (goods-delete/{order_goods_id})
`请求参数：`

`成功返回：`

`失败返回：`

#### 2.7.23 订单作废[post] (invalid/{order_id})
`请求参数：`

    reason                          string                  作废原因

`成功返回：`

`失败返回：`


#### 2.7.24 卖家订单状态统计[待确认,待发货,待付款,待收款][get] (order-count-sell)
`请求参数：`

`成功返回：`

    waitReceive                     int                     待付款

    waitSend                        int                     待发货
  
    refund                          int                     待收款

    waitConfirm                     int                     待确认

`失败返回：`

#### 2.7.24.1 买家订单状态统计[未确认,未发货,未付款,待收货][get] (order-count-buy)
`请求参数：`

`成功返回：`

    waitReceive                     int                     未付款

    waitSend                        int                     未发货

    refund                          int                     待收货
   
    waitConfirm                     int                     未确认

`失败返回：`

#### 2.7.25 获取业务订单列表[get] (business)
`请求参数：`

	salesman_id						int						业务员ID
	start_date						time					开始时间
	end_date						time					结束时间
	customer						string/int				单号or客户名
	status							int						状态
	page							int						分页

`成功返回：`
  	
	data							array					订单数组

	data字段说明
		
		id							int						订单ID
        created_at					time					创建时间
        amount						decimal					总金额
        order_id					int/null				平台订单ID
        salesman_customer_id		int						客户ID
        salesman_id					int						客户ID
        status						int						订单状态
        customer_name				string					客户名称
        order_goods					array					订单商品
     
	order_goods 字段说明
            salesman_visit_order_id		int					拜访订单ID
            goods_id					int					商品ID
            num							int					商品数量
            pieces						int					单位代码
            price						decimal				单价
            pieces_name					string				单位名
            goods_name					string				商品名
            goods_image					string				商品图片地址

`失败返回：`


### 2.8 收藏 like

#### 2.8.1 收藏 [put] (interests)
`请求参数：`

    status              int         收藏状态 （0 取消收藏 1 加入收藏）
    type                string      收藏类型 （'shops' 商店   'goods' 商品）
    id                  int         要收藏的id

`成功返回：`


`失败返回：`

#### 2.8.2 商店收藏[post] (shops)
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
    logo_url           string       店铺图片
    orders              int         店铺销量

`失败返回`

#### 2.8.3 商品收藏[post] (goods)
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

`失败返回：`


### 2.9 收货地址[personal/shipping-address]

#### 2.9.1 获取收货地址列表[get] ()

`请求参数：`

`成功返回：`

    shippingAddress     array       收货地址列表

    shoppingAddress 字段子集介绍

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

`失败返回：`

#### 2.9.2 获取收货地址详情[get] ({id})

`请求参数：`

`成功返回：`

    id                 int         收货地址id
    consigner          string      收货人
    phone              string      手机号码
    is_default         int         是否默认
    user_id            int         用户id
    x_lng               float       经度
    y_lat               float       纬度
    address            array       地址详情

    address    子字段说明

    id                 int         地址id
    province_id        int         省id
    city_id            int         市id
    district_id        int         县id
    street_id          int         街道id
    area_name          string      区域名
    address            string      详细地址

#### 2.9.3 添加收货地址 [post] ()
`请求参数：`

    consigner           string      收货人
    phone               string      联系电话
    province_id         int         省id
    city_id             int         市id
    district_id         int         县id
    street_id           int         街道id
    x_lng               float       经度
    y_lat               float       纬度
    area_name           string      省名+市名+县名+街道名
    address             string      详细地址
    is_default          int         是否设置为默认（1是， 0或不传为否）
`成功返回：`

`失败返回：`

#### 2.9.4 更新收货地址 [put] ({shipping_address_id})
`请求参数：`

    consigner           string      收货人
    phone               string      联系电话
    x_lng               float       经度
    y_lat               float       纬度
    province_id         int         省id
    city_id             int         市id
    district_id         int         县id
    street_id           int         街道id
    area_name           string      省名+市名+县名+街道名
    address             string      详细地址

`成功返回：`

`失败返回：`

#### 2.9.5 设置默认地址 [put] (default/{id})

`请求参数：`

`成功返回：`

`失败返回：`

#### 2.9.6  删除收货地址 [delete] ({id})

`请求参数：`

`成功返回：`

`失败返回：`

### 2.10  配送区域（personal/delivery-area）
#### 2.10.1 获取所有配送区域[get] (/)

`请求参数：`

`成功返回：`

    area                    array           配送区域数组
    
    area字段子集介绍
    
       id                      int             地址id
       province_id             int             省id
       city_id                 int             市id
       district_id             int             县id
       area_name               string          省、市、县名
       address                 string          详细地址
       min_money               decimal          最低配送额
       
#### 2.10.2 添加配送区域[post] (/)

`请求参数：
    
       province_id             int             省id
       city_id                 int             市id
       district_id             int             县id
       area_name               string          省、市、县、街道名
       address                 string          详细地址
       min_money               decimal          最低配送额
        
`成功返回：`

`失败返回：`

#### 2.10.3 修改配送区域[put] (/{id})

`请求参数：
    
       province_id             int             省id
       city_id                 int             市id
       district_id             int             县id
       area_name               string          省、市、县、街道名
       address                 string          详细地址
       min_money               decimal          最低配送额
        
`成功返回：`

`失败返回：`

#### 2.10.4 删除配送区域[delete] (/{id})

`请求参数：
        
`成功返回：`

`失败返回：`


### 2.11  修改密码（personal/password）
####2.11.1 修改密码[put]

`请求参数：`

    old_password            string          原密码
    password                string          新密码
    password_confirmation   string          确认新密码

`成功返回：`

`失败返回：`




`失败返回:`


### 2.12 配送信息 [personal/delivery-man]
#### 2.12.1 配送人员列表[get]
`请求参数:`

`成功返回:`

	delivery_man		array		配送人员信息
	
	delivery_man 字段子集说明
	
	id					int			配送人员ID
	user_name           string      pos机登录名
	pos_sign            string      pos机编号
	name				string		姓名
	phone				string		电话

`失败返回:`

#### 2.12.2 添加配送人员[post]
`请求参数:`

	user_name           string      pos机登录名
	password            string      pos机登录密码
	password_confirmation   string  pos机密码确认
    pos_sign            string      pos机编号
	name				string		姓名
	phone				int			电话(长度7~14位)

`成功返回:`

`失败返回:`

#### 2.12.3 编辑配送人员信息[put] ({id})
`请求参数:`
	
	user_name           string      pos机登录名
    password            string      pos机登录密码 （不修改密码可不填）
    password_confirmation   string  pos机密码确认（不修改密码可不填）
    pos_sign            string      pos机编号
    name				string		姓名
    phone				int			电话(长度7~14位)

`成功返回:`

	
`失败返回:`

#### 2.12.4 删除配送人员[delete] ({id})
`请求参数:`

`成功返回:`

	
`失败返回:`


### 2.13 财务管理 [personal/finance]
#### 2.13.1 提现申请记录[get] (withdraw) (按提现申请的创建时间查询)
`请求参数:`
	
	page				int			分页
	start_time			string		开始时间(默认上个月的今天)	
	end_time			string		结束时间(默认今天的24点)	

`成功返回:`
	withdraws			array		提现记录

	withdraws 字段子集说明

		data			array		提现记录
		
		data 字段子集说明
			
            id				int			提现单号
            amount			string		本次提现金额
            status			int			提现订单状态号
            status_info		string		提现订单状态
            trade_no		string		交易单号(未打款则为空字符串)
            reason			string		审核不通过原因
            created_at		string		创建时间
            failed_at		string		不通过时间
            pass_at			string		通过时间
            payment_at		string		打款时间  (未执行到的操作对应时间格式-0001-11-30 00:00:00)
            user_banks	array		提现银行卡信息

        user_banks 字段子集说明

            card_holder		string		持卡人姓名
            card_number		string		卡号
            card_type		int			银行名称(参考personal/banks [get]返回信息)
	
`失败返回:`

#### 2.13.2 交易流水帐[get] (daybook)
`请求参数:`

	page				int			分页
	start_at			string		开始时间(默认上个月的今天)
	end_at				string		结束时间(默认今天的24点)

`成功返回:`
	
	data			array		交易记录

	data 字段子集说明

      trade_no          string          交易号
      order_id          int             订单号
      pay_type          int             交易平台    (1 易宝   2 pingxx  3 pos机)
      type              int             交易类型  （1 入帐   2提现）
      amount            decimal         交易金额
      target_fee        decimal         交易手续费
      finished_at       timestamp       交易完成时间
	  operate			string			+ / -


`失败返回:`

#### 2.13.3 获取帐户余额[get] (balance)
`请求参数:`


`成功返回:`


	balance				decimal			帐户余额
	protectedBalance	decimal			受保护的金额 （当天交易成功金额）

`失败返回:`

#### 2.13.4 提现申请[post] (add-withdraw)
`请求参数:`
	
	amount				int			提现金额
	bank_id				int			提现账号ID

`成功返回:`

`失败返回:`

### 2.14 提现账号管理  [personal/bank]
#### 2.14.1 获取提现账号列表[get]
`请求参数:`
	
`成功返回:`

	user_bank_cards		array			该用户的银行卡信息

	user_bank_cards 字段子集说明
	
		id					int				id
		card_number			int				银行账号
		card_holder			string			持卡人姓名
		card_type			int				银行名称(参考personal/banks [get]返回信息)
		card_address		string			开户行所在地
		is_defalut			int				是否是默认提现账号(1=>是默认提现账号,否则未0)
	 	

`失败返回:`

#### 2.14.2 添加提现账号[post]
`请求参数:`
	
	card_number			int				银行账号
	card_holder			string			持卡人姓名
	card_type			int				银行名称(参考personal/banks [get]返回信息)
	card_address		string			开户行所在地
	
`成功返回:`
        bank_id        int      新增成功自增id

`失败返回:`

#### 2.14.3 修改提现账号[put] ({id})
`请求参数:`
	
	同上
	
`成功返回:`

`失败返回:`

#### 2.14.4 删除提现账号[delete] ({id})
`请求参数:`
	
`成功返回:`

`失败返回:`


### 2.15 设置默认提现账号  [personal/bank-default]
#### 2.15.1 设置默认提现账号[put] ({id})
`请求参数:`
	
`成功返回:`

`失败返回:`

### 2.16 获取银行信息  [personal/bank-info]
#### 2.16.1 获取银行信息[get]
`请求参数:`


`成功返回:`

	banks 		array			银行信息(key=>value)


`失败返回:`


### 2.17 推送设备  [push]
#### 2.17.1 添加推送设备[post] (active-token)
`请求参数:`
	
	token			string			token
	version			string			手机版本号
	type			int				设备类型(ios=>1;android=>2)
	
`成功返回:`

`失败返回:`


#### 2.17.1 删除推送设备[delete] (deactive-token)
`请求参数:`
	
	token			string			token
	
`成功返回:`

`失败返回:`

### 2.18 支付  [pay]
#### 2.18.1 获取charge对象[get] (charge/{order_id})
`请求参数：`

    type            string          订单号类型  （为all时是总订单号，不传或不是all为子订单）
    channel         string          支付渠道     （yeepay_wap 为易宝 , alipay为支付宝app）

`成功返回：`

    charge          object          charge对象

`失败返回：`

#### 2.18.2 余额支付[post] (balancepay/{order_id})
`请求参数：`

    type            string          订单号类型  （为all时是总订单号，不传或不是all为子订单）

`成功返回：`

`失败返回：`


#### 2.18.3 退款[get] (refund/{order_id})
`请求参数：`

    reason         string           退款原因
    is_seller      string           卖家退款 （退款为卖家是传入，固定值true）

`成功返回：`

`失败返回：`


### 2.19 获取版本信息  [version]
#### 2.19.1 获取版本信息 [get] 
`请求参数：`

	type            	int             移动端类型(1:ios; 2:android; 3司机; 4外勤)

`成功返回：`

    download_url        string          最新版本下载地址
	record				array			最新版相关详情

	record 字段说明
		
		version_name		string		版本名
		version_no			string		版本号
		content				string		更新内容							 

`失败返回：`

### 2.20 获取移动端广告  [advert]
#### 2.20.1 获取移动端广告 [get]
`请求参数：`


`成功返回：`

    advert              array           广告列表

    advert 字段说明

        id              int             广告id
        name            string          广告名
        url             string          广告指向链接
        image_url       string          广告图片地址

### 2.21 优惠券模块  [coupon]
#### 2.21.1 获取我的优惠券 [get] (user-coupon)
`请求参数：`

`成功返回：`

    coupons             array           优惠券列表
    
    coupons 字段说明
    
        id              int             优惠券id
        full            decimal         订单满
        discount        decimal         订单减
        diff_time       string          优惠券离过期时间
        shop            array           优惠店铺信息
        
        shop 字段说明
            
            id                  int             店铺id
            name                string          店铺名
            contact_person      string          联系人
            contact_info        string          联系电话


`失败返回：`

#### 2.21.2 获取店铺可领优惠券 [get] ({shop_id})
`请求参数：`

`成功返回：`

    返回格式同上
    
`失败返回：`

#### 2.21.3 优惠券领取 [post] (receive/{coupon_id})
`请求参数：`

`成功返回：`

    coupons            array           领取的优惠券信息（格式同coupons）